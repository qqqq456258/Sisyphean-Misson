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
    
    if( $_POST['option'] == 1 ){ //搜尋題目資料庫。
        $numOfQuestion = 0;
        if( $_POST['degree'] == "nightmare"){   //A+B+C
            $numOfQuestion = 10;
            $sql_question = "SELECT english_vacabulary,chinese_vacabulary,voice_path FROM new_card_system.vacabulary  ORDER BY RAND() LIMIT $numOfQuestion";
            $stmt = $pdo->prepare($sql_question);
            $stmt->execute() or exit("讀取 vacabulary 資料表時，發生錯誤。"); //執行。
            $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
            
        }else{          //A+B
            $numOfQuestion = 5;
            $sql_question = "SELECT english_vacabulary,chinese_vacabulary,voice_path FROM new_card_system.vacabulary WHERE topic = :Topic_1 OR topic = :Topic_2 ORDER BY RAND() LIMIT $numOfQuestion";
            $stmt = $pdo->prepare($sql_question);
            $stmt->bindValue(':Topic_1','sisyphean_normal'); // 避免SQL injection。
            $stmt->bindValue(':Topic_2','original_vacabulary'); // 避免SQL injection。
            $stmt->execute() or exit("讀取 vacabulary 資料表時，發生錯誤。"); //執行。
            $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
        }
        echo json_encode($row);
        
    }elseif( $_POST['option'] == 2 ){ // 插入資料進入排行榜。
        $sql = "INSERT INTO new_card_system.sisyphean_rank(studentID,level_num,today_date) VALUES(:StudentID,:Level_num,:Today_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':StudentID',$_SESSION["user"]);
        $stmt->bindValue(':Level_num',$_POST['Level_num']);
        $stmt->bindValue(':Today_date',date("Y-m-d"));
        $stmt->execute() or exit("sisyphean_rank written failed.");
        echo "true";
    }
    
?>