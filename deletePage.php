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
    
    /*刪除舊檔案*/
    $image_errorCode = "成功";
    $audio_errorCode = "成功";
    $string = explode('_',$_POST['fileName']);
    $author = $string[1];
    $old_aud_file_path = "ownVoice/".$_POST['fileName'].".wav";
    $old_img_file_path =  "image/".$_POST['fileName'].".png";

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
    
    $sql_delete = "DELETE FROM new_card_system.card WHERE filename = :target";
    $stmt = $pdo->prepare($sql_delete);
    if(!$stmt){
        $audio_errorCode = "失敗";
        $image_errorCode = "失敗";
    }
    $stmt->bindValue(':target',$_POST['fileName']);
    $stmt->execute();

    $sql_updateData = "UPDATE new_card_system.personal_information SET total_card_amount=total_card_amount-1 WHERE pi_account=:pi_account";
    $stmt = $pdo->prepare($sql_updateData);
    $stmt->bindValue(':pi_account',$author);
    $stmt->execute();

    $pdo = null;

    print('image_errorCode => 圖檔刪除情況： '.$image_errorCode);
    print('audio_errorCode => 聲音刪除情況： '.$audio_errorCode);
    
    header('Location: userpage.php');
    exit;
    
?>