<?php 
function start_session($expire = 0) {       //設置 session 時間的函式。
       if ($expire == 0) { 
           $expire = ini_get('session.gc_maxlifetime'); 
      } else { 
          ini_set('session.gc_maxlifetime', $expire); 
       } 
      if (empty($_COOKIE['PHPSESSID'])) { 
          session_set_cookie_params($expire); 
          session_start(); 
      } else { 
          session_start(); 
          setcookie('PHPSESSID', session_id(), time() + $expire); 
     } 
} 
start_session(7200);        //設定session 時間為 7200秒 = 2小時。
include("connMysql0917.php");
$insert_into = $_POST['execute'];
$type = $_POST['options'];
$not_filename = $_POST['fileName'];
$spilt_string = explode('_',$not_filename);
$sender = $_SESSION["user"];
$recipient = $spilt_string[1];
$eng_vacabulary = $spilt_string[2];
$filename = "_".$spilt_string[1]."_".$spilt_string[2]."_".$spilt_string[3]."_".$spilt_string[4];
$date =date("Y-m-d");
$zero = 0;
$state = "";

$sql_isNULL = "SELECT * FROM new_card_system.evaluation_detail WHERE sender_studentID = :sender AND record_filename = :filename AND record_type = :type";
$stmt = $pdo->prepare($sql_isNULL);
$stmt->bindValue(':sender',$sender);
$stmt->bindValue(':filename',$filename);
$stmt->bindValue(':type',$type);
$stmt->execute();
$row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
$Rows = count($row);


if($type == 0 && $Rows == 0 && $insert_into == 1){ // 評價繪圖很棒。
    $state = "覺得圖畫得很漂亮！！";
    
    $sql = "INSERT INTO new_card_system.evaluation_detail(sender_studentID,recipient_studentID,record_filename,record_date,record_type) VALUES(:sender_studentID,:recipient_studentID,:record_filename,:record_date,:record_type)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':sender_studentID',$sender);
    $stmt->bindValue(':recipient_studentID',$recipient);
    $stmt->bindValue(':record_filename',$filename);
    $stmt->bindValue(':record_date',$date);
    $stmt->bindValue(':record_type',$type);
    $stmt->execute();
    
    $sql_updateData = "UPDATE new_card_system.card SET gain_heart_amount=gain_heart_amount+1 WHERE filename=:fileName";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':fileName',$filename);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.personal_information SET total_heart_amount=total_heart_amount+1 WHERE pi_account=:pi_account";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':pi_account',$recipient);
    $stmt->execute();
    
    $pdo = null;
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}elseif($type == 1 && $Rows == 0 && $insert_into == 1){ // 評價發音很棒。
    $state = "覺得發音得很標準！！";
    $sql = "INSERT INTO new_card_system.evaluation_detail(sender_studentID,recipient_studentID,record_filename,record_date,record_type) VALUES(:sender_studentID,:recipient_studentID,:record_filename,:record_date,:record_type)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':sender_studentID',$sender);
    $stmt->bindValue(':recipient_studentID',$recipient);
    $stmt->bindValue(':record_filename',$filename);
    $stmt->bindValue(':record_date',$date);
    $stmt->bindValue(':record_type',$type);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.card SET gain_star_amount=gain_star_amount+1 WHERE filename=:fileName";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':fileName',$filename);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.personal_information SET total_star_amount=total_star_amount+1 WHERE pi_account=:pi_account";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':pi_account',$recipient);
    $stmt->execute();
    
    $pdo = null;
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}elseif($type == 2 && $Rows == 0 && $insert_into == 1){ // 收藏起來。
    $state = "你收藏了這張好字卡！！";
    
//      存圖檔。
	$imageName = "image/".$filename.".png";	
    $data = file_get_contents($imageName);    //回傳一段string = 圖檔編碼。
    $GetOnly =  md5(uniqid(rand()));
    $newFileName = "_".$sender.'_'.$eng_vacabulary.'_'.$date.'_'.$GetOnly;
    $newFilePath = "image/".$newFileName.".png";
    file_put_contents($newFilePath, $data); 
	     /* file_put_contents()，放入一個必填、一個可選參數：路徑、文件數據
	     (如base64)，若沒有此文件，會直接依內容新建一個新文件。*/
        
//      存音檔。
    $audioName = "ownVoice/".$filename.".wav";
    $data = file_get_contents($audioName);
    $newFilePath = "ownVoice/".$newFileName.".wav";
	file_put_contents($newFilePath, $data); 
    
    $sql = "INSERT INTO new_card_system.evaluation_detail(sender_studentID,recipient_studentID,record_filename,record_date,record_type) VALUES(:sender_studentID,:recipient_studentID,:record_filename,:record_date,:record_type)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':sender_studentID',$sender);
    $stmt->bindValue(':recipient_studentID',$recipient);
    $stmt->bindValue(':record_filename',$filename);
    $stmt->bindValue(':record_date',$date);
    $stmt->bindValue(':record_type',$type);
    $stmt->execute();
    
    $sql = "SELECT chinese_vacabulary FROM new_card_system.vacabulary WHERE english_vacabulary = :english_vacabulary";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':english_vacabulary',$eng_vacabulary);
    $stmt->execute(); 
    $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
    $chi_vacabulary = $row[0]['chinese_vacabulary'];
    
    $sql = "INSERT INTO new_card_system.card(author,filename,last_save_date,collected_time,gain_heart_amount,gain_star_amount,eng_vacabulary,chi_vacabulary,possessor,guess_total_time) VALUES(:author,:filename,:last_save_date,:collected_time,:gain_heart_amount,:gain_star_amount,:eng_vacabulary,:chi_vacabulary,:possessor,:guess_total_time)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':author',$sender);
    $stmt->bindValue(':filename',$newFileName);
    $stmt->bindValue(':last_save_date',$date);
    $stmt->bindValue(':collected_time',$zero);
    $stmt->bindValue(':gain_heart_amount',$zero);
    $stmt->bindValue(':gain_star_amount',$zero);
    $stmt->bindValue(':eng_vacabulary',$eng_vacabulary);
    $stmt->bindValue(':chi_vacabulary',$chi_vacabulary);
    $stmt->bindValue(':possessor',$recipient);
    $stmt->bindValue(':guess_total_time',$zero);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.card SET collected_time=collected_time+1 WHERE filename=:fileName";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':fileName',$filename);
    $stmt->execute();
    
    $sql_updateData = "UPDATE new_card_system.personal_information SET total_card_amount=total_card_amount+1 WHERE pi_account=:pi_account";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':pi_account',$sender);
    $stmt->execute();
    
    $pdo = null;
    
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}elseif($type == 2 && $Rows == 0 && $insert_into == 0){
    $state = "沒問題可以收藏！！";
    $pdo = null;
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}elseif($type == 3){
    $state = "看看資訊吧~";    
    $sql = "SELECT * FROM new_card_system.card WHERE filename = :filename";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':filename',$filename);
    $stmt->execute() or exit("無法開啟 card 資料表。");
    $row = $stmt->fetchALL(PDO::FETCH_ASSOC);  
            
    $last_save_date = $row[0]['last_save_date'];
    $collected_time = $row[0]['collected_time'];
    $heart_num = $row[0]['gain_heart_amount'];
    $star_num = $row[0]['gain_star_amount'];
    $possessor = $row[0]['possessor'];

    $pdo = null;
    $Response = array('author' => $recipient ,
                      'collected_time' =>$collected_time ,
                     'star_num' => $star_num ,
                     'heart_num' => $heart_num ,
                     'last_date' => $last_save_date,
                     'possessor' => $possessor,
                     'state' => $state);
    echo json_encode($Response);
    
}elseif($Rows == 0 && $insert_into == 0){
    $state = "沒問題可以評價！！";
    $pdo = null;
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}else{
    $state = "評價或收藏過囉！！";
    $pdo = null;
    $Response = array('state' => $state);
    echo json_encode($Response);
    
}


?>