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
include("connMysql0917.php");	//檢查有沒有登入
function notice($say){		//alert提示的函式。
    echo "<script type='text/javascript'>";
    echo "alert('$say');";
    echo "</script>";
}
?>

<!DOCTYPE html>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <head>
        <title></title>
        <style type="text/css">
            @font-face{
                font-family:'support';
                src:url(font/support.ttf);
            }
            body{
                margin: 0px auto;
                text-align: center;
            }
            h1{
                font-family: 'support';
                font-weight: 700;
                font-size: 80px;
            }
            #content{
                position: absolute;
                top: 0px;
                left: 0px;
                background-color: black;
                z-index: -1;
                width: 100vw;
                height: 100vh;
                transition: 3s;
            }
            #bg{
                position:fixed; 
                top:calc(50% - 324px); 
                left:calc(50% - 325px);
            }
            #bg:hover+#content{
                background-color: pink;
            }
            form{
                margin: 0px auto;
                text-align: center;
                position:fixed; 
                top:calc(50% - 220px); 
                left:calc(50% - 210px);
                z-index: 1;
                opacity: 0;
                transition: 1s;
            }
            form:hover{
                opacity: 1;
            }
            #test:hover~#content{
                background-color: pink;
            }
            p{
                font-family: 'support';
                font-weight: 500;
                font-size: 50px;
                color: aliceblue;
                vertical-align: middle;
            }
            #t_account,#t_psd{
                font-size: 30px;
                height: 40px;
                width: 200px;
                border-radius: 10px;
            }
            #enter{
                font-family: 'support';
                font-size: 50px;
                border-radius: 99px;
                height: 65px;
                width: 200px;
                background: transparent;
                color: black;
                border: 0px solid black;
                transition: 2s;
                font-weight: 800;
            }
            #test:hover>#enter{
                background: #FFF;
                color: darkgreen;
            }
            #enter:hover{
                width:400px;
            }
        </style>
    </head>
<body>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <h1>英文童話世界</h1>
    <form id="test" name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <p>帳號：<input type="text" id="t_account" name="id" autocomplete="off"/></p>
            <p>密碼：<input type="password" id="t_psd" name="pw" autocomplete="off" /></p>
            <input type="number" hidden="hidden" name="decision" value="1" />
            <br><br><br><br><br><br><br><br><br><br><br><br><br>
            <input id="enter" type="submit" value="登入" />
        </form>
    <img id="bg" src="images/index_bg.png">
    <div id="content"></div>
</body>

</html>

<?php

if( !empty($_POST['id']) || !empty($_POST['pw']) ){    //避免自動登入跳出錯誤之情況。
    //連接資料庫
    
    $id = $_POST['id'];
    $pw = $_POST['pw'];   
    $avoidSpace = strlen($id); // 為了過濾掉多按空白的情況。
    
    // 	搜尋資料庫資料
    $sql = "SELECT * FROM new_card_system.member WHERE user_account = :ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':ID',$id); // 避免SQL injection。以 :UserID 代替並放入語法內。
    $stmt->execute() or exit("讀取member資料表時，發生錯誤。"); //執行pdo物件；反之出錯。 
    $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一取出，並以陣列放入$row。
    $nRows = Count($row);  // 資料幾筆，預設：只取出一筆，所以基本上會輸出 1 。
    
    $accountLength = strlen($row[0]['user_account']); // 為了過濾掉多按空白的情況，以字串長度來檢測錯誤。

    if($id == null || $pw == null){		
        //輸入不完整先擋掉。
        notice('請輸入完整帳號、密碼。');
    
    }elseif($avoidSpace != $accountLength){	
        //按空白的情況。
        notice('你的帳號不小心輸入到空白囉！');
    }elseif( $nRows == 0){	
        //完整輸入後，過濾資料庫中沒帳號的、還有找資料庫時input內value會忽略尾端空白的防呆情況。
        notice("沒有此帳號，可能尚未註冊。");
        
    }elseif( $row[0]['user_account'] == $id && $row[0]['user_pwd'] == $pw ){
        //$row就是二維陣列。
        
        $sql = "SELECT * FROM new_card_system.personal_information WHERE pi_account = :ID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':ID',$id); // 避免SQL injection。以 :UserID 代替並放入語法內。
        $stmt->execute() or exit("讀取personal_information資料表時，發生錯誤。");
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC);
        $name = $row[0]['pi_name'];
        $_SESSION["user"] = $id;
        $_SESSION["psw"] = $pw;
        echo '<meta http-equiv=REFRESH CONTENT = 1;url=userpage.php>'; //延遲一秒，轉換至 userpage.php 頁面。
        notice('哈囉~'.$name.'同學，歡迎回來！！');
    }else{
        //擋掉無帳號且非完整輸入的以後，就只剩下密碼錯誤的情況。
        notice("密碼錯誤。");				
    }
    
}


?>
