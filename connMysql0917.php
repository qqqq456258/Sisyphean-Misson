<?php
try{
	$dsn = "mysql:host=localhost;port=3306;dbname=new_card_system;charset=utf8";
    $user = "root";	// mysql使用者名稱
    $password = "";	// mysql使用者密碼
    $options = array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    /*
		 ATTR_EERMODE：是個要用PDO時的一定要的基本設定。
		 ERRMODE_EXCEPTION. 它會自動告訴PDO每次查詢失敗時拋出異常。
    */
    $pdo = new PDO($dsn, $user,$password,$options);
    $pdo->query("SET NAMES 'utf8'");  // 以UTF8編碼，SQL語法使用於資料庫。
}catch(PDOException $e){	
    echo "資料庫連線失敗！錯誤訊息：",$e->getMessage();	
    exit;
}
?> 
