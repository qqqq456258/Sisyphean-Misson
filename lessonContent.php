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

    header("Content-Type:text/html;charset=utf-8");
    function content($getArray){
            include("connMysql0917.php");	
            $images = glob('image/*.png*');    //得到image資料夾裡所有檔案數量				
            $num=count($images);
            $control = 0;	
            for($i=0; $i<$num; $i++){			
						$string = $images[$i];
                        $filename = str_replace('image/', '', $string);
                        $filename = str_replace('.png', '', $filename);
						$spilt_string = explode('_', $string);		    
					    $spilt_vacabulary = $spilt_string[2];
                        if(in_array($spilt_vacabulary, $getArray)){
                            $sql = "SELECT chi FROM tomorrowenglish.everybody_up_vacabulary WHERE eng = :Spilt_vacabulary LIMIT 1";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':Spilt_vacabulary',$spilt_vacabulary); // 避免SQL injection。
                            $stmt->execute() or exit("讀取everybody_up_vacabulary資料表時，發生錯誤。"); //執行。 
                            $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
                            $chinese = $row[0]['chi'];    //照指令執行只有取到一筆，所以輸出陣列中 中文字 欄位就好。
                            
                            $voicePath = str_replace('image/', 'ownVoice/', $string);
                            $voicePath = str_replace('.png', '.wav', $voicePath);
                            
                            $author = $_SESSION['user'];					
                            $spilt_author = $spilt_string[1];
                            $spilt_id = $spilt_string[3];
                            $spilt_date = $spilt_string[4];						 
                            if($control%3==0){
                                echo "<tr>";
                            }
							echo "<td class='card' width=405px height=500px style='margin: 5px; border-radius: 15px; border:6px #5F9EA0 solid;background-color:#FFFFFF; align='center' ><h1>".$spilt_vacabulary." ".$chinese."</h1>".
              
                                
								"<input type='image' id='heart".$filename."' class='heart' title='圖畫有創意' width=64px height=64px src='material/heart_dark.png'/>".
                                
								"<input type='image' id='star".$filename."' class='star' title='發音很標準' width=64px height=64px src='material/star_dark.png'/>".
 	
								"<input type='image' id='collect".$filename."' class='collect' title='收藏這張' width=64px height=64px src='material/noGetThis.png'/>".      
                                
								"<input type='image' id='info".$filename."' class='info' title='字卡資訊' width=64px height=64px src='material/noGetInfo.png'/>".  
                                
								"<audio controls controlsList='nodownload'><source src = '$voicePath' type='audio/mp3'></audio>"."</br><img src = '$string' width=400px height=400px border='1' style='border:3px #FFD382 dashed;'/>"."</td>";
                            if($control%3==2){
                                echo "</tr>";
                            }
                            
                            $control = $control+1;
						}

				    }	        
    }
    	
    $select_op=$_GET['lesson'];
    if($select_op != ""){
        if($select_op == "lesson1"){
            $lesson1 = array("paper","glue", "scissors", "paint", "crayon", "pencil", "pen", "marker");
            content($lesson1);	
            
        }else if($select_op == "lesson2"){
            $lesson2 = array("ballon","ball", "doll", "yo-yo", "train", "boat", "jet","car");
            content($lesson2);	
            
        }else if($select_op == "lesson3"){
			$lesson3 = array("red","yellow", "blue", "green", "orange", "purple","pink", "brown");
            content($lesson3);	
            
        }else if($select_op == "lesson4"){
            $lesson4 = array("one","two", "three", "four", "five", "six", "seven", "eight", "nine", "ten");
            content($lesson4);	
            
        }else if($select_op == "lesson5"){
            $lesson5 = array("cat","dog", "bird", "rabbit", "goat", "duck", "horse", "cow");
            content($lesson5);	
            
        }else if($select_op == "lesson6"){
            $lesson6 = array("one","two", "three", "four", "five", "six", "seven", "eight", "nine", "ten");
            content($lesson6);	
        }
        
    }
?>
