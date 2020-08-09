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
	

	if($_GET['DoYouWantThis'] == "true"){		//儲存別人作品的函式。
        
//      存圖檔。
		$data = file_get_contents($_GET['imagePath']);    //回傳一段string = 圖檔編碼。
		$imageName = str_replace('image/','',$_GET['imagePath']);	//將多餘字刪除。
		$spilt_string = explode('_', $imageName);	//針對檔名做拆解。
	    $spilt_vacabulary = $spilt_string[2];	//英文單字
		$author = $_SESSION['user'];			//新座號
		$date =date("Y-m-d");					//時間
        $GetOnly =  md5(uniqid(rand()));
	    $newFilePath = "image/_".$author.'_'.$spilt_vacabulary.'_'.$date.'_'.$GetOnly.".png";
	     // uniqid() 函数基于以微秒计的当前时间，生成一个唯一的 ID。
	    file_put_contents($newFilePath, $data); 
	     /* file_put_contents()，放入一個必填、一個可選參數：路徑、文件數據
	     (如base64)，若沒有此文件，會直接依內容新建一個新文件。*/
        
        
//      存音檔。
		$audioName = str_replace('image/','ownVoice/',$_GET['imagePath']);
		$audioName = str_replace('png','wav',$audioName);
        $data = file_get_contents($audioName);
		$spilt_string = explode('_', $audioName);
	    $newFilePath = "ownVoice/_".$author.'_'.$spilt_vacabulary.'_'.$date.'_'.$GetOnly.".wav";
	    file_put_contents($newFilePath, $data); 
        $From = $_GET['From'];
		header("Location:".$From.".php"); 	//重定向瀏覽器 
		exit;								//確保重定向後，後續代碼不會被執行 

	}else{
        $From = $_GET['From'];
        print("error");
		header("Location:".$From.".php"); 
		exit;
	}
?>
