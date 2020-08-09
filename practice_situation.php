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
$state = "success";
$author = $_SESSION['user'];
$filename = $_POST['fileName'];
$date = date("Y-m-d");

if($_POST['options'] == 1){
//紀錄 practice.php 中字卡猜題次數中猜對的情況。
        $sql_increate_total = "UPDATE new_card_system.card SET guess_total_time = guess_total_time+1 WHERE filename = :filename";
        $stmt = $pdo->prepare($sql_increate_total);
        $stmt->bindValue(':filename',$filename);
        $stmt->execute() or exit("Failed.-1");


        $sql_isNull ="SELECT * FROM new_card_system.guess_detail WHERE guess_stdID = :author AND guess_filename = :filename";
        $stmt = $pdo->prepare($sql_isNull);
        $stmt->bindValue(':author',$author);
        $stmt->bindValue(':filename',$filename);
        $stmt->execute() or exit("Failed.-2");
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        $Rows = count($row);

        if($Rows == 0){
            $sql_increateWrongTime = "INSERT INTO new_card_system.guess_detail(guess_stdID,guess_filename,last_date,wrong_time) VALUES (:guess_stdID,:guess_filename,:last_date,:wrong_time)";
            $stmt = $pdo->prepare($sql_increateWrongTime);
            if (!$stmt) {
                $state = "Failed.-3";
            }
            $stmt->bindValue(':guess_stdID',$author);
            $stmt->bindValue(':guess_filename',$filename);
            $stmt->bindValue(':last_date',$date);
            $stmt->bindValue(':wrong_time',0);
            $stmt->execute() or exit("Failed.-4");
        }
    
}elseif($_POST['options'] == 2){
    //紀錄 practice.php 中字卡猜題次數中猜錯的情況。
        $sql_increate_total = "UPDATE new_card_system.card SET guess_total_time = guess_total_time+1 WHERE filename = :filename";
        $stmt = $pdo->prepare($sql_increate_total);
        $stmt->bindValue(':filename',$filename);
        $stmt->execute() or exit("Failed.-1");


        $sql_isNull ="SELECT * FROM new_card_system.guess_detail WHERE guess_stdID = :author AND guess_filename = :filename";
        $stmt = $pdo->prepare($sql_isNull);
        $stmt->bindValue(':author',$author);
        $stmt->bindValue(':filename',$filename);
        $stmt->execute() or exit("Failed.-2");
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        $Rows = count($row);

        if($Rows > 0){
            $wrong_time = $row[0]['wrong_time']+1;
            $sql_increateWrongTime = "UPDATE new_card_system.guess_detail SET last_date = :last_date, wrong_time = :wrong_time WHERE guess_filename = :filename AND guess_stdID = :author";
            $stmt = $pdo->prepare($sql_increateWrongTime);
            if (!$stmt) {
                $state = "Failed.-3";
            }
            $stmt->bindValue(':last_date',$date);
            $stmt->bindValue(':wrong_time',$wrong_time);
            $stmt->bindValue(':filename',$filename);
            $stmt->bindValue(':author',$author);
            $stmt->execute() or exit("Failed.-4");

        }else{
            $sql_increateWrongTime = "INSERT INTO new_card_system.guess_detail(guess_stdID,guess_filename,last_date,wrong_time) VALUES (:guess_stdID,:guess_filename,:last_date,:wrong_time)";
            $stmt = $pdo->prepare($sql_increateWrongTime);
            if (!$stmt) {
                $state = "fail-5";
            }
            $stmt->bindValue(':guess_stdID',$author);
            $stmt->bindValue(':guess_filename',$filename);
            $stmt->bindValue(':last_date',$date);
            $stmt->bindValue(':wrong_time',1);
            $stmt->execute() or exit("Failed.-6");
        }

}


    $pdo = null;
    echo $state;
?>