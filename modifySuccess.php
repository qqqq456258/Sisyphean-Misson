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

    /*必備資料放入變數*/
    $image = $_POST['imgURI'];
    $audio = $_POST['audURI'];
    $filename = $_POST['fileName'];
    $spilt_filename = explode('_',$filename);
    $author = $_SESSION['user'];
    $eng_vacabulary = $spilt_filename[2];

    $old_aud_file_path = "ownVoice/$filename.wav";
    $old_img_file_path =  "image/$filename.png";
    $date =date("Y-m-d");
    $GetOnly =  md5(uniqid(rand()));
    $filename = "_".$author."_".$eng_vacabulary.'_'.$date.'_'.$GetOnly;


    /*處理base64編碼，新增檔案至資料夾內*/
    $Path = "image/$filename.png";
    $img = str_replace('data:image/png;base64,', '', $image);
    $img = str_replace(' ', '+', $img); //防呆，防止有人輸入空白導致答案不正確。
    $data = base64_decode($img);    //將base64解碼成圖檔資料。
    file_put_contents($Path, $data); // file_put_contents()，放入一個必填、一個可選參數：路徑、文件數據(如base64)。
    $Path = 'ownVoice/'.$filename.'.wav';
    $aud = str_replace('data:audio/wav;base64,', '', $audio);
    $aud = str_replace(' ', '+', $aud); 
    $data = base64_decode($aud);    //將base64解碼成音訊資料。
    file_put_contents($Path,$data);
    

    /*刪除舊檔案*/
    $image_errorCode = "成功";
    $audio_errorCode = "成功";
	if( file_exists( $old_aud_file_path ) ){		//音檔檔案存在之情況。
        if ( !unlink( $old_aud_file_path ) ){		//刪除舊的檔案，若遇到問題輸出。
			$audio_errorCode = "失敗";	           //表示檔案存在但刪除失敗。
		}
	}else{
		$audio_errorCode = "找不到";	         
	}
	if( file_exists( $old_img_file_path ) ){		//圖檔存在之情況。
        if ( !unlink( $old_img_file_path ) ){		//刪除舊的檔案，若遇到問題輸出。
			$image_errorCode = "失敗";	           //表示檔案存在但刪除失敗。
		}
	}else{
		$image_errorCode = "找不到";	             
	}

    //更新card資料表。
    $sql_updateData = "UPDATE new_card_system.card SET author=:author,filename=:filename,last_save_date=:last_modify_date WHERE filename=:old_fileName";
    $stmt = $pdo->prepare($sql_updateData);
    if (!$stmt) {
        $state = "fail";
    }
    $stmt->bindValue(':author',$author);
    $stmt->bindValue(':filename',$filename);
    $stmt->bindValue(':last_modify_date',$date);
    $stmt->bindValue(':old_fileName',$_POST['fileName']);
    $stmt->execute();


    $pdo = null;
    

    echo "舊圖檔刪除情況:$image_errorCode\n舊聲音刪除情況:$audio_errorCode";

?>