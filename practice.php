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
    start_session(7200);        //設定session 時間為 7200秒 = 2小時
    include("connMysql0917.php");
    $userName = $_SESSION["user"];
    $images = glob('image/*.png');
    $num=count($images);
    if($num>0){
        $random = rand(0,$num-1);
        $filePath = $images[$random];
		$filename = str_replace('image/','',$filePath);	//將多餘字刪除。
		$filename = str_replace('.png','',$filename);	//將多餘字刪除。
        $spilt_string = explode('_', $filename);
        $author = $spilt_string[1];
        $english = $spilt_string[2];
        echo "<script>var word='".$english."';</script>"; // 放入答案。
        echo "<script>var imagePath='".$filePath."';</script>"; // 放入圖片路徑。
        echo "<script>var fileName='".$filename."';</script>"; // 放入圖片路徑。
        echo "<script>console.log(word);</script>"; // 放入圖片路徑。

        $sql_find_word = "SELECT * FROM new_card_system.card WHERE filename = :filename LIMIT 1";
        $stmt = $pdo->prepare($sql_find_word);
        $stmt->bindValue(':filename',$filename);
        $stmt->execute() or exit("無法開啟 card 資料表。");
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC);  
        $Rows = count($row);

        //顯示提示-錄音	    		       
        //從資料夾提取對應的音檔
        $sound_files = glob("ownVoice/$filename.wav");
        $sound_Path = $sound_files[0];
        echo "<script>var test='ownVoice/$filename.wav';</script>";
    }

?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <title>英語自繪王i-Drawing!</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="//jqueryui.com/resources/demos/style.css">
    <link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato|Roboto|Roboto+Condensed" rel="stylesheet">
    <style type="text/css">
        body {
            background-image: url("material/bg_practice.png");
            background-repeat: no-repeat;
            background-size: cover;
        }

        .dlg-no-close .ui-dialog-titlebar-close {
            display: none;
        }

        #dPlate {
            float: left;
            margin-left: 20%;
            margin-top: 10%;
        }

        #dAnswer {
            padding-top: 13%;
            padding-left: 12%;
            float: left;
            background-size: 350px 100px;
            background-repeat: no-repeat;
        }

        #dAllHint {
            margin-left: 40px;
        }

        #answer {
            margin: 10px 0 10px 22px;
            padding: 5px;
            height: 50px;
            border-radius: 10px;
            font-family: 'Lato', sans-serif;
            font-weight: bold;
            width: 200px;
            border:3px solid black;
            font-size: 25px;
            float: left;
            text-transform:lowercase;
        }

        #tip {
            font-size: 25px;
            margin: 0 0 0 22px;
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: bold;
        }

        #fAnswer {
            padding-left: 100px;
        }

        #tAnswer {
            float: left;
        }

        #tCorrect1,
        #tCorrect2,
        #tCorrect3 {
            margin-left: 20px;
            border-radius: 10px;
            padding: 3px;
            width: 200px;
            height: 45px;
            font-size: 25px;
            font-weight: 700;
            font-family: 'Bitter', serif;
        }
        #tCorrect1_check,
        #tCorrect2_check,
        #tCorrect3_check{
            font-size: 30px;
            font-weight: 900;
        }

        #dCorrect_content {
            float: left;
            font-weight: 600;
            font-size: 28px;
            text-align: center;
            margin: 10px 80px 0 40px;
        }
        #dCorrect_spell{
            float: left;
            margin: 20px 0 0 20px;
            
        }
        #btn_check {
            width: 200px;
            height: 60px;
            padding-left: 20px;
            padding-top: 20px;
        }
        #btn_ok1{
            width: 200px;
            height: 60px;
            float: right;
            position: fixed;
            right: 200px;
            bottom: 200px;
        }
        #tip_chinese{
            float: right;
            position: fixed;
            right: 0px;
            bottom: 400px;
            height: 60px;
            background-color: #666666;
            color: gold;
            border: 1px solid #666666;
            width: 210px;
            font-family: '微軟正黑體';
            font-size: 30px;
            font-weight: 700;
            border-radius: 25px 0 0 25px;
            transition: 1s;
        }
        #tip_chinese:hover{
            color: cyan;
            width: 330px; 
            background-color: #000;
            border: 1px solid #000;
        }
        #tip_voice{
            float: right;
            position: fixed;
            right: 0px;
            bottom: 300px;
            height: 60px;
            background-color: #666666;
            color: #00DD00;
            border: 1px solid #666666;
            width: 210px;
            font-family: '微軟正黑體';
            font-size: 30px;
            font-weight: 700;
            border-radius: 25px 0 0 25px;
            transition: 1s;
        }
        #tip_voice:hover{
            color: #FF44AA;
            width: 330px; 
            background-color: #000;
            border: 1px solid #000;
        }
        #give_up_button{
            float: right;
            position: fixed;
            right: 0px;
            bottom: 100px;
            height: 60px;
            color: #FF3333;
            background-color: #666666;
            border: 1px solid #666666;
            width: 210px;
            font-family: '微軟正黑體';
            font-size: 30px;
            font-weight: 700;
            border-radius: 25px 0 0 25px;
            transition: 1s;
        }
        #give_up_button:hover{
            color: #FFFFFF;
            background-color: #B94FFF;
            border: 1px solid #B94FFF;
            width: 330px; 
        }
        #infoOfCard{
            float: right;
            position: fixed;
            right: 0px;
            bottom: 200px;
            height: 60px;
            color: #5599FF;
            background-color: #666666;
            border: 1px solid #666666;
            width: 210px;
            font-family: '微軟正黑體';
            font-size: 30px;
            font-weight: 700;
            border-radius: 25px 0 0 25px;
            transition: 1s;
        }
        #infoOfCard:hover{
            color: #9900FF;
            background-color: #000;
            border: 1px solid #000;
            width: 330px; 
        }
        
        @font-face{
            font-family:'support';
            src:url(font/support.ttf);
        }
        #createPage,#userPage,#guessPage,#watchPage,#mission,#rank{
            border: 1px solid white;
            background-color: aliceblue;
            color: darkgreen;
            font-weight: 900;
            border-radius: 15px;
            font-size: 23px;
            padding: 10px 16px 0 0;
            margin: 5px;
            font-family: 'support';
            text-align: center;
            display: inline-block;
            opacity: .7;
            transition: .5s;
        }
        #createPage:hover,#userPage:hover,#guessPage:hover,#watchPage:hover,#mission:hover,#rank:hover{
            opacity: 1;
        }
        .btn{
            line-height: 170%;
            height:91px;
            float:left;
        }
        .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
            float: none;
        }

        .ui-dialog .ui-dialog-title, .ui-dialog .ui-dialog-buttonpane {
            margin: 0px auto;
            font-family: '微軟正黑體';
            font-size:30px;
            font-weight: 600;
            color: maroon;
            text-align:center;
            padding-left: 0.4em;
            padding-right: 0.4em;
        }
        b{
            font-size:80px;
        }
        #help,#help2,#infoThisCard{
            margin: 0px auto;
            background-color: floralwhite;
        }
        p{
            font-size: 20px;
            font-family: '微軟正黑體';
        }
        span{
            font-size: 31px;
            font-weight: 750;
        }

    </style>
</head>

<body>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#answer').keyup(function() { //字元檢測。
                var answer = $('#answer').val().toLowerCase(); //將輸入值轉換小寫。
                if (answer == word) {
                    $('#tip').css("color", "#228B22").text("正確答案");
                    TrueOrFalse();
                    /*  正確答案。  */
                } else if (answer == "") {
                    $('#tip').empty();
                    /*  若輸入為空，則清除提示  */
                } else if (answer.substring(0, answer.length) == word.substring(0, answer.length)) {
                    $('#tip').css("color", "#EE7700").text("離答案差"+(word.length-answer.length)+"個字");
                    /*  檢查輸入每個字是否錯誤，對的話就繼續。  */
                } else if (answer.length > word.length) {
                    $('#tip').css("color", "blue").text("離答案太遠囉");
                    /*  檢查是否輸入超出答案的長度。  */
                } else {
                    $('#tip').css("color", "red").text("錯了哦");
                    /*  檢查是否輸入輸入錯誤。  */
                }

            });


            $('#answer').keydown(function(event) {
                if (event.keyCode == 13) {
                    alert("請不要按enter！！");
                }
            });


            $('#tCorrect1, #tCorrect2, #tCorrect3').keyup(function() {
                var IDname = $(this).attr("id");
                if($(this).val() == word){
                    $("#"+IDname+"_check").css("color","green");
                    $("#"+IDname+"_check").text("✔");
                }else{
                    $("#"+IDname+"_check").css("color","red");
                    $("#"+IDname+"_check").text("✖");
                }
                if ($('#tCorrect1').val() == word && $('#tCorrect2').val() == word && $('#tCorrect3').val() == word) {
                    $('#btn_ok1').removeAttr("disabled");
                    $('#btn_ok1').attr("src", "material/btn_ok.png");
                } else {
                    $('#btn_ok1').attr("disabled", "disabled");
                    $('#btn_ok1').attr("src", "material/btn_ok_d.png");
                }
            });

                    

        });

    </script>
    <script type="text/javascript">
        function TrueOrFalse() {
            var praise = ["0", "恭喜答對囉！\rCongrates！", "做得好\rGood Job！", "漂亮的答題！\rWell played！", "你真是個天才賞畫家！\rGenius！", "太棒了！\rCool！"];
            var maxNum = praise.length - 1;
            var minNum = 1;
            var n = Math.floor(Math.random() * (maxNum - minNum + 1)) + minNum; //隨機參數。
            var pre_check = document.getElementById('answer').value;
            var check = pre_check.replace(/\s+/g, "");
            //判斷對或錯
            var value_spilt_string = word;
            if ( check === value_spilt_string ) {
                
                $. ajax({
                    type : "POST" ,
                    url : "practice_situation.php" ,
                    data : {options:1,fileName:fileName},
                    dataType: "text",
                    success: function(data){
                        window.location.href = 'practice.php';
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
                        alert(msg);
                        window.location.href = 'practice.php';
                    },
                });
            } else if (check == "" || check == null) { //防呆直接按enter.
                console.log("miss");
                alert("記得輸入單字哦！！");
                return false;
            } else {
                return false;
            }
            
            
        }

        function tip_1() {
            $('#help').attr("display", "block");
            $('#help').dialog({
                resizable: false,
                width: 330,
                modal: true,
                show: {
                    effect: "fold",
                    duration: 500
                },
                hide: {
                    effect: "fold",
                    duration: 500
                }
            });
        }

        function tip_2() {
            $('#help2').attr("display", "block");
            $('#help2').dialog({
                resizable: false,
                width: 350,
                modal: true,
                show: {
                    effect: "fold",
                    duration: 500
                },
                hide: {
                    effect: "fold",
                    duration: 500
                }
            });
        }

        function give_up() {
            $('#dialog-confirm').attr("display", "block");
            $("#dialog-confirm").dialog({
                resizable: true,
                dialogClass: "dlg-no-close",
                closeOnEscape: false,
                height: $(window).height() * 0.9, //dialog視窗高度
                width: $(window).width() * 0.9,
                modal: true,
                show: {
                    effect: "slide",
                    duration: 500
                },
                hide: {
                    effect: "explode",
                    duration: 500
                }
            });
            $(window).resize(function() {
                var wWidth = $(window).width();
                var dWidth = wWidth * 0.9;
                var wHeight = $(window).height();
                var dHeight = wHeight * 0.9;
                $("#dialog-confirm").dialog("option", "width", dWidth);
                $("#dialog-confirm").dialog("option", "height", dHeight);
            });
            
                $. ajax({
                    type : "POST" ,
                    url : "practice_situation.php" ,
                    data : {options:2,fileName:fileName},
                    dataType: "text",
                    success: function(data){
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
                        alert(msg);
                        window.location.href = 'practice.php';
                    },

                });   
        }
        
        function getInfo(){
            $('#infoThisCard').attr("display", "block");
            $('#infoThisCard').dialog({
                resizable: false,
                width: 400,
                modal: true,
                show: {
                    effect: "fold",
                    duration: 500
                },
                hide: {
                    effect: "fold",
                    duration: 500
                }
            });
        }

    </script>

    <table id="dBg_title">
        <tr>
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
        </tr>
    </table>
    <div id="dPlate">
        <?php
            echo "<img src = '$images[$random]' width=250px height=250px border='0px'/>";
        ?>
    </div>
    <div id="dAnswer">
        <form id="fAnswer" onsubmit="return TrueOrFalse();" onkeydown="if(event.keyCode==13)return false;">
            <input id="Hidden1" type="hidden" value="0" />
            <img src="material/myanswer3.png" style="width: 260px; height: 70px;"><br>
            <div style="clear: both; display: inline-block;">
                <input type="text" id="answer" required autocomplete="off" autofocus="autofocus"><br>
                <span id="tip"></span><br>
            </div>
        </form>
        <button id="tip_chinese" onclick="tip_1()">提示 1</button><br>
        <button id="tip_voice" onclick="tip_2()">提示 2</button><br>
        <button id="give_up_button" onclick="give_up()">我要放棄</button><br>
        <button id="infoOfCard" onclick="getInfo()">字卡資訊</button><br>

        <!--答錯顯示提示-->
        <div id="help" title="中文提示" style="display: none;">
            <?php echo "中文意思是： <b>".$row[0]['chi_vacabulary']."</b>" ?>
        </div>
        <div id="help2" title="提示語音" style="display: none;">
            <?php echo "<p>聽聽看別人怎麼說：</p><br><audio controls controlsList='nodownload'><source src = '$sound_Path' type='audio/wav'></audio>"; ?>
        </div>
        <div id="infoThisCard" title="關於題目" style="display: none;">
        <?php
            $sql = "SELECT * FROM new_card_system.card WHERE filename = :filename";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':filename',$filename);
            $stmt->execute() or exit("無法開啟 card 資料表。");
            $row = $stmt->fetchALL(PDO::FETCH_ASSOC);  
            
            $last_save_date = $row[0]['last_save_date'];
            $collected_time = $row[0]['collected_time'];
            $heart_num = $row[0]['gain_heart_amount'];
            $star_num = $row[0]['gain_star_amount'];
            $possessor = $row[0]['possessor'];
            $guess_total_time = $row[0]['guess_total_time'];
            
            $sql = "SELECT SUM(wrong_time) FROM new_card_system.guess_detail WHERE guess_filename = :filename";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':filename',$filename);
            $stmt->execute() or exit("無法開啟 guess_detail 資料表。");
            $row = $stmt->fetchALL(PDO::FETCH_ASSOC);  
            $wrong_total_time = $row[0]['SUM(wrong_time)'];
            $errorRatio = "未知";
            if($guess_total_time != 0){
                $errorRatio = round(($wrong_total_time/$guess_total_time)*100);
            }
            
            $sql = "SELECT * FROM new_card_system.guess_detail WHERE guess_filename = :filename AND guess_stdID = :userName";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':filename',$filename);
            $stmt->bindValue(':userName',$userName);
            $stmt->execute() or exit("無法開啟 guess_detail 資料表。");
            $row = $stmt->fetchALL(PDO::FETCH_ASSOC);
            $Rows = count($row);
            $my_wrong_time = 0;
            if($Rows > 0){
                $my_wrong_time = $row[0]['wrong_time'];
            }
            
            
            echo "
                <p>困難度：<span>$errorRatio</span> %</p>
                <p>這張字卡總共被收藏：<span>$collected_time</span> 次</p>
                <p>認為發音很標準的有：<span>$star_num</span> 人</p>
                <p>認為畫得很漂亮的有：<span>$heart_num</span> 人</p>
                <p>你曾經猜錯這張字卡：<span>$my_wrong_time</span> 次</p>
                "
            
        ?>
           
           </div>
        <div id="dialog-confirm" title="訂正學習" style="display:none;">
            <div id="dBg">
                <img src="material/bg_correct.png" style="margin:auto;display:block; width: 480px;height: 100px;" />
            </div>
            <hr color="#483D8B" size="3" width="100%" style="margin-top:3px;">
            <div>
                <div id="dCorrect_content">
                    <div id="dCorrect_vacabulary">
                        <h2>
                            <?php
                                echo $english.'&emsp;&emsp;'.$row[0]['chi_vacabulary'];
					       ?>
                        </h2>
                    </div>
                    <div>
                        <?php
                                echo "<audio autoplay controls><source src ='sound/$english.mp3' type='audio/mpeg'></audio>";
					   ?>
                    </div>
                    <div id="dCorrect_img">
                        <?php
			        	    echo "<img src = '$images[$random]' width=250px height=250px border='0px'/>";
			             ?>
                    </div>
                </div>
                <div id="dCorrect_spell">
                    <table>
                        <tr>
                            <td>
                                <image src="material/tCorrect1.png" width="180px" height="60px" />
                                <input type="text" id="tCorrect1" />
                                <span id="tCorrect1_check"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <image src="material/tCorrect2.png" width="180px" height="60px" />
                                <input type="text" id="tCorrect2" />
                                <span id="tCorrect2_check"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <image src="material/tCorrect3.png" width="180px" height="60px" />
                                <input type="text" id="tCorrect3" />
                                <span id="tCorrect3_check"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="image" id="btn_ok1" src='material/btn_ok_d.png' onclick="location.href = 'practice.php'" disabled="disabled" />
                                <h1 style="margin:30px; text-align:center;">下次別放棄！！ 要確實訂正才能進步喔！</h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
