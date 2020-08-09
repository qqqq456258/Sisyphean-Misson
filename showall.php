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
    $id = $_SESSION['user'];
    //搜尋資料庫資料
    $sql = "SELECT * FROM tomorrowenglish.user01 WHERE name = :ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':ID',$id); // 避免SQL injection。以 :UserID 代替並放入語法內。
    $stmt->execute() or exit("讀取user資料表時，發生錯誤。"); //執行pdo物件；反之出錯。
    $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一取出，並以陣列放入$row。

?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>	
	<title>英語自繪王 i-Drawing!</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="//jqueryui.com/resources/demos/style.css">
	<style type="text/css">
        @font-face{
            font-family:'SansForgetica';
            src:url(font/SansForgetica/SansForgetica-Regular.otf);
        }
        @font-face{
            font-family:'support';
            src:url(font/support.ttf);
        }
        body{
            margin: 0 auto;
            background-image:url(images/full_res.jpg);
            background-repeat: repeat-y;
            background-size: 100%;
        }
		#pic{
            text-align: center;
			margin-bottom: 20px;
		}
        #another{
            text-align: center;
			margin-bottom: 20px;
        }
		#content>td{
            text-decoration: none;
			padding: 5px;
            margin: 15px;
			border:3px #000 solid;
		}
        #content{
            margin:0px auto;
            text-align: center;
        }
        .lesson{
            padding: 7px;
            margin: 12px;
            height: 50px;
            text-align: center;
            color: white;
            float: left;
            border-radius: 6.5px;
            transition: .5s;
        }
        #lesson1{
            background-color: darkblue;
        }
        #lesson2{
            background-color: deeppink;
        }
        #lesson3{
            background-color: forestgreen;
        }
        #lesson4{
            background-color: darkred;
        }
        #lesson5{
            background-color: darkgoldenrod;
        }
        #lesson6{
            background-color: darkmagenta;
        }
        .lesson:hover{
            color: black;
            background-color: white !important;
        }
        .save{
            float: right;
            border-radius: 8px;
            font-family: '微軟正黑體';
            font-size: 22px;
            margin: 10px;
        }
        #createPage,#userPage,#guessPage,#watchPage,#mission,#rank{
            font-weight: 900;
            background-color: white;
            border-radius: 15px;
            font-size: 23px;
            padding: 10px 15px 0 0;
            margin: 6px;
            text-align: center;
            display: inline-block;
            font-family: 'support';
            transition: .5s;
        }
        #createPage:hover,#userPage:hover,#guessPage:hover,#watchPage:hover,#mission:hover,#rank:hover{
            background-color: darkseagreen;
        }
        .btn{
            line-height: 170%;
            height:91px;
            float:left;
        }
        #info_dialog{
            margin: 0px auto;
            font-weight: 500;
            font-size: 25px;
            font-family: '微軟正黑體';
            
        }
        .ui-dialog-title{
            text-align: center;
            font-weight: 900;
            font-size: 35px;
            font-family: 'support';
        }
        #info_dialog p span{
            color: darkorange;
            margin: 0px auto;
            font-weight: 700;
            font-size: 30px;
            
        }
	</style>
	<script>
        var xmlHTTP;
        function $_xmlHttpRequest(){
            if(window.ActiveXObject){
                xmlHTTP = new ActiveXObject("Microsoft.XMLHTTP");
            }else if(window.XMLHttpRequest){
                xmlHTTP = new XMLHttpRequest();
            }
        }
        function check(lesson){
            $_xmlHttpRequest();
            xmlHTTP.open("GET","lessonContent.php?lesson="+lesson,true);
            xmlHTTP.onreadystatechange=function check_user(){
                if(xmlHTTP.readyState == 4){
                    if(xmlHTTP.status == 200){
                        var str = xmlHTTP.responseText;
                        document.getElementById("content").innerHTML = str;
                    }
                }
            }
            xmlHTTP.send(null);
        }
    </script>
    <script>
        
        $(document).ready(function(){
            
            /* 評價愛心和星星，只可評好而不可取消。*/
            var IalreadyRate = 0;
            var execute = 1;
            
            $('#content').on('mouseenter', '.heart,.star,.collect,.info', function (){
                var kindOf = $(this).attr('class');
                var status = $(this).attr('src');
                var target = $(this).attr('id');
                var type = 100;
                execute = 0;
                if(kindOf == "heart"){
                    type = 0;
                }else if(kindOf == "star"){
                    type = 1;
                }else if(kindOf == "collect"){
                    type = 2;
                }else if(kindOf == "info"){
                    type = 3;
                }
                
                $. ajax({   //先判斷有沒有被評價過
                        type : "POST" ,
                        url : "rate.php" ,
                        data : {options:type,fileName:target,execute:execute},
                        dataType: "json",
                        success: function(data){
                            console.log(data.state);
                            if(data.state == "評價或收藏過囉！！"){
                                IalreadyRate = 1; //先判斷是有被評分過的。
                            }
                        },
                        error: function (jqXHR, exception) {
                            var msg = '';
                            if (jqXHR.status === 0) {
                                msg = 'Not connect.\n Verify Network.';
                            } else if (jqXHR.status == 404) {
                                msg = 'Requested page not found. [404]';
                            } else if (jqXHR.status == 500) {
                                msg = 'Internal Server Error [500].';
                            } else if (exception === 'parsererror') {
                                msg = 'Requested JSON parse failed.';
                            } else if (exception === 'timeout') {
                                msg = 'Time out error.';
                            } else if (exception === 'abort') {
                                msg = 'Ajax request aborted.';
                            } else {
                                msg = 'Uncaught Error.\n' + jqXHR.responseText;
                            }
                            console.log(msg);
                        },
                    });
                
                if( kindOf == "heart" && status == "material/heart_dark.png" ){
                    $(this).attr('src','material/heart_shining.png');
                }else if( kindOf == "star" && status == "material/star_dark.png" ){
                    $(this).attr('src','material/star_shining.png');
                }else if( kindOf == "collect" && status == "material/noGetThis.png" ){
                    $(this).attr('src','material/GetThis.png');
                }else if( kindOf == "info" && status == "material/noGetInfo.png" ){
                    $(this).attr('src','material/GetInfo.png');
                }
                
            });
            
            $('#content').on('mouseout', '.heart,.star,.collect,.info', function (){
                var kindOf = $(this).attr('class');
                var status = $(this).attr('src');
                if( kindOf == "heart" && status == "material/heart_shining.png"){
                    $(this).attr('src','material/heart_dark.png');
                }else if( kindOf == "star" && status == "material/star_shining.png"){
                    $(this).attr('src','material/star_dark.png');
                }else if( kindOf == "collect" && status == "material/GetThis.png" ){
                    $(this).attr('src','material/noGetThis.png');
                }else if( kindOf == "info" && status == "material/GetInfo.png"){
                    $(this).attr('src','material/noGetInfo.png');
                }
                IalreadyRate = 0;
                execute = 1;
                
            });
            
            $('#content').on('click', '.heart,.star,.collect,.info', function (){
                var target = $(this).attr('id');
                var kindOf = $(this).attr('class');
                var status = $(this).attr('src');
                var type = 100;
                execute = 1;
                
                if(kindOf == "heart"){
                    type = 0;
                }else if(kindOf == "star"){
                    type = 1;
                }else if(kindOf == "collect"){
                    type = 2;
                }else if(kindOf == "info"){
                    type = 3;
                }
                
                if( IalreadyRate == 0 ){
                    if( type == 0 ){
                        $(this).attr('src','material/heart_shining.png');
                    }else if( type == 1 ){
                        $(this).attr('src','material/star_shining.png');
                    }
                    $. ajax({
                        type : "POST" ,
                        url : "rate.php" ,
                        data : {options:type,fileName:target,execute:execute},
                        dataType: "json",
                        success: function(data){
                            if( data.state == "覺得圖畫得很漂亮！！"){
                                alert(data.state);
                                IalreadyRate = 1; //先判斷是有被評分過的。
                            }else if( data.state == "覺得發音得很標準！！"){
                                alert(data.state);
                                IalreadyRate = 1; //先判斷是有被評分過的。
                            }else if( data.state == "你收藏了這張好字卡！！"){
                                alert(data.state);
                                IalreadyRate = 1; //先判斷是有被評分過的。
                            }else{
                                
                                var _recipient = data.author;
                                var _collected_time = data.collected_time;
                                var _star_num = data.star_num;
                                var _heart_num = data.heart_num;
                                var _last_date = data.last_date;
                                var _possessor = data.possessor;
                                
                                
                                var context = "<p>這張字卡總共被收藏：<span>"+_collected_time+"</span> 次</p><p>認為發音很標準的有：<span>"+_star_num+"</span> 人</p><p>認為畫得很漂亮的有：<span>"+_heart_num+"</span> 人</p>";
                                console.log(context);
                                
                                $('#info_dialog').html(context);
                                $('#info_dialog').attr('display','block');
                                $('#info_dialog').dialog({
                                    resizable: false,
                                    modal: true,
                                    width:500,
                                    show: {
                                        effect: "shake",
                                        duration: 500
                                    },
                                    hide: {
                                        effect: "clip",
                                        duration: 500
                                    }
                                });
                            }
                            
                            
                        },
                        error: function (jqXHR, exception) {
                            var msg = '';
                            if (jqXHR.status === 0) {
                                msg = 'Not connect.\n Verify Network.';
                            } else if (jqXHR.status == 404) {
                                msg = 'Requested page not found. [404]';
                            } else if (jqXHR.status == 500) {
                                msg = 'Internal Server Error [500].';
                            } else if (exception === 'parsererror') {
                                msg = 'Requested JSON parse failed.';
                            } else if (exception === 'timeout') {
                                msg = 'Time out error.';
                            } else if (exception === 'abort') {
                                msg = 'Ajax request aborted.';
                            } else {
                                msg = 'Uncaught Error.\n' + jqXHR.responseText;
                            }
                            console.log(msg);
                        },
                    });
                    
                    
                }else if( IalreadyRate == 1 ){
                    if( type == 0 ){
                        alert("你已經評價過圖畫囉！！");
                    }else if( type == 1 ){
                        alert("你已經評價過發音囉！！");
                    }else if( type == 2 ){
                        alert("你已經收藏過囉！！");
                    }
                }
                
            });   
            
            
            

        });
        
    </script>

</head>

<body>
<div>
    <div id="pic">
        <img src="material/showall.png" width="600px" height="180px"/>
    </div>
    <div id="another">
    <td>
                        <div id="userPage" onclick="location.href = 'userpage.php'">
                        <div class="btn"><img src="images/BackMyAlbum.gif" width="91px" height="91px"></div>
                        <div class="btn">回到我的單字卡<br>Back My Album</div>
                        </div>                 
                        
                        <div id="createPage" onclick="location.href = 'canvas20171107.php'">
                        <div class="btn"><img src="images/EnjoyCreating.gif" width="91px" height="91px"></div>
                        <div class="btn">繼續創作字卡<br>Enjoy Creating</div>
                        </div>

                        <div id="guessPage" onclick="location.href = 'practice.php'">
                        <div class="btn"><img src="images/EnjoyGuessing.gif" width="91px" height="91px"></div>
                        <div class="btn">猜猜看大家畫什麼<br>Enjoy Guessing</div>
                        </div>

                        <div id="watchPage" onclick="location.href = 'showall.php'">
                        <div class="btn"><img src="images/watchOthers.gif" width="91px" height="91px"></div>
                        <div class="btn">看看同學們作品<br>Watch Others</div>
                        </div>
                    
                        <div id="mission" onclick="location.href = 'challenge.php'">
                        <div class="btn"><img src="images/RestrictedArea.gif" width="91px" height="91px"></div>
                        <div class="btn">薛西弗斯的任務<br>Sisyphean Mission</div>
                        </div>
                        
                        <div id="rank" onclick="location.href = 'rank6.php'">
                        <div class="btn"><img src="images/Leaderboards.gif" width="91px" height="91px"></div>
                        <div class="btn">排行榜<br>Leaderboards</div>
                        </div>
    </td>
</div>
</div>

<hr color="#483D8B" size="3" width="100%" style="margin-top:20px;">
<table>
    <tr>
        <td>
            <h1 id="lesson1" class="lesson" onclick="check('lesson1');">
            Art Class
            </h1>
        </td>

        <td>
            <h1 id="lesson2" class="lesson"  onclick="check('lesson2');">Let's Play</h1>
        </td>

        <td>
            <h1 id="lesson3" class="lesson"  onclick="check('lesson3');">Many Colors</h1>
        </td>

        <td>
            <h1 id="lesson4" class="lesson"  onclick="check('lesson4');">Counting</h1>
        </td>

        <td>
            <h1 id="lesson5" class="lesson"  onclick="check('lesson5');">Animals</h1>
        </td>
        <td>
            <h1 id="lesson6" class="lesson"  onclick="check('lesson6');">number</h1>
        </td>

    </tr>
</table>
<div id="info_dialog" title="評價資訊" style="display:none;"></div>       
<hr color="#483D8B" size="3" width="100%">

<table id="content"></table>

</body>
</html>