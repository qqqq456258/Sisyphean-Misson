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

    date_default_timezone_set("Asia/Taipei");   //設定時區 
    include("connMysql0917.php");
    $state = "success";
    // 接收 POST 進來的 base64 DtatURI String
    $image = $_POST['imgURI'];
    $audio = $_POST['audURI'];
    $zero = 0;
    $eng_vacabulary = $_SESSION["eng_vacabulary"];   
    $author = $_SESSION['user'];
    $chi_vacabulary = $_SESSION["chi_vacabulary"];

    $img = str_replace('data:image/png;base64,', '', $image);
    $img = str_replace(' ', '+', $img); //防呆，防止有人輸入空白導致答案不正確。
    $data = base64_decode($img);    //將base64解碼成圖檔資料。
    $date =date("Y-m-d");
    $GetOnly =  md5(uniqid(rand()));
    $filename = "_".$author."_".$eng_vacabulary.'_'.$date.'_'.$GetOnly;
    
    $Path = "image/".$filename.'.png';
    file_put_contents($Path, $data); // file_put_contents()，放入一個必填、一個可選參數：路徑、文件數據(如base64)。
    
    $aud = str_replace('data:audio/wav;base64,', '', $audio);
    $aud = str_replace(' ', '+', $aud); 
    $data = base64_decode($aud);    //將base64解碼成圖檔資料。
    $Path = 'ownVoice/'.$filename.'.wav';
    file_put_contents($Path,$data);
        
    //存入card資料表。
    $sql_saveData = "INSERT INTO new_card_system.card(author,filename,last_save_date,collected_time,gain_heart_amount,gain_star_amount,eng_vacabulary,chi_vacabulary,possessor,guess_total_time) VALUES(:author,:filename,:last_save_date,:collected_time,:gain_heart_amount,:gain_star_amount,:eng_vacabulary,:chi_vacabulary,:possessor,:guess_total_time)";
    $stmt = $pdo->prepare($sql_saveData);
    if (!$stmt) {
        $state = "fail";
    }
    $stmt->bindValue(':author',$author);
    $stmt->bindValue(':filename',$filename);
    $stmt->bindValue(':last_save_date',$date);
    $stmt->bindValue(':collected_time',$zero);
    $stmt->bindValue(':gain_heart_amount',$zero);
    $stmt->bindValue(':gain_star_amount',$zero);
    $stmt->bindValue(':eng_vacabulary',$eng_vacabulary);
    $stmt->bindValue(':chi_vacabulary',$chi_vacabulary);
    $stmt->bindValue(':possessor',$author);
    $stmt->bindValue(':guess_total_time',$zero);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.personal_information SET total_card_amount=total_card_amount+1 WHERE pi_account=:pi_account";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':pi_account',$author);
    $stmt->execute();

    $pdo = null;
    echo $state;
?>


