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
?>
<!DOCTYPE html>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <title>英語自繪王i-Drawing!</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="_js/Recorderjs-master/dist/recorder.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Oswald|Roboto+Condensed" rel="stylesheet">
    <style>
        
        #answer{
            padding: 5px 10px;
            border: 1px solid #000 ;
            border-radius: 5px;
            font-family: 'Lato', sans-serif;
            font-weight: bold;
        }
        #tip{
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 26px;
            font-weight: bold;
        }
        #logo{
            margin-top: 10px;
        }
        body,input { 
            font-size: 15pt;
        }
        #dCanvas,#dLine { clear: both; }
        .option
        {
            float: left; width: 20px; height: 20px; border: 2px solid #cccccc;
            margin-right: 4px; margin-bottom: 4px;
            background-image: url('material/plate.png');
        }
        .active { border: 2px solid black; }
        .lw { text-align: center; vertical-align: middle; }
        img.output { border: 1px solid green; }
        #cSketchPad { cursor: arrow; }
        body{
            background-image: url('images/sisyphean_newCard.jpg');
            background-repeat:no-repeat;
            background-size: cover;
        }
        
        #dAll{
            margin: 0px auto;
        }
        #div1{
            text-align: center;
            float: left;
            margin: 50px;
        }
        #div2{
            text-align: center;
            float: left;
            margin: 50px;
        }
        #div3{
            text-align: center;
            float: left;
            margin: 50px;
        }
        #div4{
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            float:right;
            position:fixed;
            right:0px;
            bottom:0px;
            margin: 20px;
        }
        #div5{
            text-align: center;
            font-weight: bolder;
            font-size: 100px;
            color: crimson;
            float:right;
            position:fixed;
            right:100px;
            bottom:400px;
            margin: 20px;
            display: none;
        }
        #div6{
            text-align: center;
            font-weight: bolder;
            font-size: 90px;
            color: crimson;
            float:right;
            position:fixed;
            right:600px;
            bottom:0px;
            margin: 10px;
            display: none;
        }
        #dreset{
            float: left;
        }
        #dcheck{
            float: left;
            padding-left : 80px;
        }
        #dQuestion{
            text-align: center;
            font-size: 60px;
            font-weight: 600;
            margin: 30px;
        }
        .heading{
            font-family: 'Oswald', sans-serif;
            font-size: 30px;
            font-weight: 700;
        }
        .heading span{
            font-size: 40px;
            color: crimson;
        }
        #start{
            width: 100px;
            border-radius: 7px;
            font-family: '微軟正黑體';
            font-size: 36px;
            font-weight: 700;
            margin-right: 50px;
        }
        #stop{
            width: 100px;
            border-radius: 7px;
            font-family: '微軟正黑體';
            font-size: 36px;
            font-weight: 700;
        }
        #createPage,#userPage,#guessPage,#watchPage,#shop,#mission,#rank{
            font-weight: 900;
            background-color: white;
            border-radius: 15px;
            width:260px;
            height:70px;
            font-size: 25px;
            margin: 7px;
            text-align: center;
            display: inline-block;
            transition: .5s;
            opacity: .7;
        }
        #createPage:hover,#userPage:hover,#guessPage:hover,#watchPage:hover,#shop:hover,#mission:hover,#rank:hover{
            background-color: black;
            color: aliceblue;
            border:8px solid black;
            opacity: 1;
        }
        #createPage{
            border:8px solid #444;
        }
        #userPage{
            border:8px solid #FF3EFF;
        }
        #guessPage{
            border:8px solid #FF4500;
        }
        #watchPage{
            border:8px solid #4169E1;
        }
        #shop{
            border:8px solid gold;
        }
        #mission{
            border:8px solid #7700FF;
        }
        #rank{
            border:8px solid #00DD00;
        }
    </style>
    <script>
        var bGenImage_determined = 0; //用於判斷畫板是否輸入完整。
        var tip_determined = 0; //用於判斷字元是否輸入完整。
        var voice_determined = 0; //用於聲音是否輸入完整。
        var Audio_dataURI = "";
        $(document).ready(function() {
            //產生不同顏色的div方格當作調色盤選項
            var colors =
                "#FF3333;#FF44AA;#FFAA33;#FFFF00;#33FF33;#227700;#33FFFF;#0000FF;#550088;#9900FF;#AA7700;#000000;#FFFFFF".split(';');
            var sb = [];
            $.each(colors, function(i, v) {
                sb.push("<div class='option' style='background-color:" + v + "'></div>");
            });
            $("#dPallete").html(sb.join("\n"));
            //產生不同尺寸的方格當作線條粗細選項
            sb = [];
            for (var i = 1; i <= 10; i++)
                sb.push("<div class='option lw'>" +
                    "<div style='margin-top:#px;margin-left:#px;width:%px;height:%px'></div></div>"
                    .replace(/%/g, i).replace(/#/g, 10 - i / 2));
            $("#dLine").html(sb.join('\n'));
            var $clrs = $("#dPallete .option");
            var $lws = $("#dLine .option");
            //點選調色盤時切換焦點並取得顏色存入p_color，
            //同時變更線條粗細選項的方格的顏色
            $clrs.click(function() {
                $clrs.removeClass("active");
                $(this).addClass("active");
                p_color = this.style.backgroundColor;
                $lws.children("div").css("background-color", p_color);
            }).first().click();
            //點選線條粗細選項時切換焦點並取得寬度存入p_width
            $lws.click(function() {
                $lws.removeClass("active");
                $(this).addClass("active");
                p_width =
                    $(this).children("div").css("width").replace("px", "");

            }).eq(3).click();

            //取得canvas context
            var canvas = document.querySelector("#cSketchPad");
            var ctx = canvas.getContext("2d");
            ctx.lineCap = "round";
            ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            function mouseDown(e) {
                this.draw = true;
                ctx.strokeStyle = p_color;
                ctx.lineWidth = p_width;
                ctx.fillStyle = "white";


                var o = this;
                this.offsetX = this.offsetLeft;
                this.offsetY = this.offsetTop;

                while (o.offsetParent) {
                    o = o.offsetParent;
                    this.offsetX += o.offsetLeft;
                    this.offsetY += o.offsetTop;
                }

                ctx.beginPath();
                ctx.moveTo(e.pageX - this.offsetX, e.pageY - this.offsetY);
            }

            function mouseMove(e) {
                if (this.draw) {
                    ctx.lineTo(e.pageX - this.offsetX, e.pageY - this.offsetY);
                    ctx.stroke();
                }
            }

            function mouseUp(e) {
                this.draw = false;
            }

            function touchStart(e) {
                this.draw = true;
                this.ctx = this.getContext("2d");
                this.touch = e.targetTouches[0];
                this.ctx.strokeStyle = p_color;
                this.ctx.lineWidth = p_width;

                var o = this;
                this.offsetX = this.offsetLeft;
                this.offsetY = this.offsetTop;

                while (o.offsetParent) {
                    o = o.offsetParent;
                    this.offsetX += o.offsetLeft;
                    this.offsetY += o.offsetTop;
                }

                this.ctx.beginPath();
                this.ctx.moveTo(this.touch.pageX - this.offsetX, this.touch.pageY - this.offsetY);
                e.preventDefault();
            }

            function touchMove(e) {
                this.touch = e.targetTouches[0];
                if (this.draw) {
                    this.ctx.lineTo(this.touch.pageX - this.offsetX, this.touch.pageY - this.offsetY);
                    this.ctx.stroke();
                }
                e.preventDefault();
            }

            function touchEnd(e) {
                this.draw = false;
                e.preventDefault();
            }

            $("#bGenImage").click(function() {

                $("#div1").html("<img src='"+canvas.toDataURL()+"' id='img_final' class='output'><b style='font-size: 48px;font-weight: 700;color:green;'>&ensp;✔</b>");

                bGenImage_determined = 1; //用於判斷畫板是否齊全。

                if (tip_determined == 1 && bGenImage_determined == 1 && voice_determined == 1) { //判斷是否要顯示submit of button。
                    $('#bSendImage').prop("disabled", false); //開啟按鈕元件。
                    $('#bSendImage').attr("src", "material/btn_sent.png"); //換按鈕圖案。
                    $('#div5,#div6').toggle();
                    $('#div5').animate({bottom:'250px'},"slow");
                    $('#div6').animate({right:'450px'},"slow");
                }
            });

            //重畫
            $("#bReset").click(function() {
                ctx.fillStyle = "white";
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            });

            //出題考同學
            $("#bSendImage").on("click",function() {
                var canvas_to_image = canvas.toDataURL();
                $. ajax({        //直接以ajax傳遞資料。
                    type : "POST" ,
                    url : "saveImage.php" ,
                    data : {imgURI:canvas_to_image,audURI:Audio_dataURI},
                    dataType: "text",
                    success: function(data){
                      window.location.href = 'userpage.php';
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
            
            });

            //事件控制0117
            window.addEventListener('load', function() {
                var canvas = document.querySelector('#cSketchPad');
                document.querySelector("#cSketchPad");
                canvas.addEventListener('mousedown', mouseDown);
                canvas.addEventListener('mousemove', mouseMove);
                canvas.addEventListener('mouseup', mouseUp);

                canvas.addEventListener('touchstart', touchStart);
                canvas.addEventListener('touchmove', touchMove);
                canvas.addEventListener('touchend', touchEnd);
            });

            //輸入字元並立刻給予提示
            $('#answer').keyup(function() {
                
                var wordToAns = word.toLowerCase();
                var answer = $('#answer').val().toLowerCase(); //將輸入值轉換小寫。
                if (answer == wordToAns) {
                    $('#tip').css("color", "green").text("right");
                    $('#div2').html("<b style='font-size: 48px;font-weight: 700;font-family: 'Lato', sans-serif;'>"+word+"<span style='color:green;'>&ensp;✔</span></b>");
                    tip_determined = 1; //用於判斷字元輸入是否正確。
                    /*  正確答案。  */
                } else if (answer == "") {
                    $('#tip').empty();
                    tip_determined = 0; //用於判斷字元輸入是否正確。
                    /*  若輸入為空，則清除提示  */
                } else if (answer.substring(0, answer.length) == wordToAns.substring(0, answer.length)) {
                    $('#tip').css("color", "gold").text("keep going");
                    tip_determined = 0; //用於判斷字元輸入是否正確。
                    /*  檢查輸入每個字是否錯誤，對的話就繼續。  */
                } else if (answer.length > wordToAns.length) {
                    $('#tip').css("color", "blue").text("Too much letters");
                    tip_determined = 0; //用於判斷字元輸入是否正確。
                    /*  檢查是否輸入超出答案的長度。  */
                } else {
                    $('#tip').css("color", "red").text("wrong");
                    tip_determined = 0; //用於判斷字元輸入是否正確。
                    /*  檢查是否輸入輸入錯誤。  */
                }

                if (tip_determined == 1 && bGenImage_determined == 1 && voice_determined == 1) { //判斷是否要顯示submit of button。
                    $('#bSendImage').prop("disabled", false); //開啟按鈕元件。
                    $('#bSendImage').attr("src", "material/btn_sent.png"); //換按鈕圖案。
                    $('#div5,#div6').toggle();
                    $('#div5').animate({bottom:'250px'},"slow");
                    $('#div6').animate({right:'450px'},"slow");
                }

            });

        });

    </script>
    <script>
        function challenge_theme(){
            var TorF = confirm("你確定要進入嗎？");
            if(TorF){
                window.location.href = 'challenge.php';
            }else{
                alert("危險不要靠近哦 ~ ");
            }
        }
    </script>
    <script>
        var audio_context;
        var recorder;

        function startUserMedia(stream) {
            var input = audio_context.createMediaStreamSource(stream);
            recorder = new Recorder(input);
        }

        function startRecording(button) {
            recorder && recorder.record();
            button.disabled = true;
            button.nextElementSibling.disabled = false;
        }

        function stopRecording(button) {
            recorder && recorder.stop();
            button.disabled = true;
            createDownloadLink();
            recorder.clear();
        }

        function createDownloadLink() {
            recorder && recorder.exportWAV(function(blob) {
                
                $('#div3').html("<b style='font-size: 48px;font-weight: 700;'>你的聲音：</b><div style='margin-top:30px;'><span id='recordingslist'></span><b style='font-size: 48px;font-weight: 700;color:green;'>&ensp;✔</b></div>");
                var url = URL.createObjectURL(blob);
                var span = document.getElementById('recordingslist');
                var au = document.createElement('audio');
                var startButton = document.getElementById('start');
                var stopButton = document.getElementById('stop');
                
                var reader = new FileReader();
                /*FileReader 物件，Web 應用程式能以非同步（asynchronously）方式讀取儲存在用戶端的檔案（或原始資料暫存）內容
                ，可以使用 File 或 Blob 物件指定要讀取的資料*/
                
                reader.onloadend = function (e) {   //事件：每一次讀取結束之後觸發（不論成功或失敗）。
                    Audio_dataURI = e.target.result;
                    /*  e.target.result(執行結果) → data:audio/wav;base64,...String...。並存入變數準備處理。   */
                };
                reader.readAsDataURL(blob); //執行轉換blob → dataURI。
    
                
                au.controls = true;
                au.src = url;
                span.appendChild(au);
                if(!(typeof(Audio_dataURI) == "undefined")){
                    voice_determined = 1;
                    if (tip_determined == 1 && bGenImage_determined == 1 && voice_determined == 1) { 
                        //判斷是否要顯示submit of button。
                        $('#bSendImage').prop("disabled", false); //開啟按鈕元件。
                        $('#bSendImage').attr("src", "material/btn_sent.png"); //換按鈕圖案。
                        $('#div5,#div6').toggle();
                        $('#div5').animate({bottom:'250px'},"slow");
                        $('#div6').animate({right:'450px'},"slow");
                    }
                }
            });
        }

        window.onload = function init() {
            try {
                // webkit shim
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
                window.URL = window.URL || window.webkitURL;
                audio_context = new AudioContext;
            } catch (e) {
                console.log('No web audio support in this browser!');
            }

            navigator.getUserMedia({
                audio: true
            }, startUserMedia, function(e) {
                console.log('No live audio input: ' + e);
            });
        };

    </script>
    
</head>

<body>

        <div id="dQuestion">
        <?php
            echo "<script>var word = '".$_GET['word']."'</script>";
//            echo $_GET['voicePath'];
            
                //輸出英文、中文字和語音檔。
            echo $_GET['word']." ".$_GET['translation']."&emsp;<audio controls autoplay><source src = '".$_GET['voicePath']."' type='audio/mpeg'>Your browser does not support the audio element.</audio>";
            $_SESSION["eng_vacabulary"] = $_GET['word'];
            $_SESSION["chi_vacabulary"] = $_GET['translation'];
        ?>
        </div>

<div id='div1'>
       <p class='heading'><span>First：</span>Please draw your theme.</p>
        <div id="plate_line">
            <div id="dPallete"></div>
            <div id="dLine"></div>
        </div>
        <div id="dCanvas">
            <canvas id="cSketchPad" width="380" height="380" style="border: 3px solid black" />
        </div>

        <div id="dreset">
            <input type="image" id="bReset" value="我要重畫" src='material/btn_drawagain.png' width="160px" height="55px" />
        </div>
        <div id="dcheck">
            <input type="image" id="bGenImage" src='material/btn_done.png' width="160px" height="55px" value="確認完成" />
        </div>

    </div>
<div id='div2'>
    <p class='heading'><span>Second：</span>Please spell it.</p>
    <table border=0>
        <tr>
            <td>
                <h2><input type="text" name="savehint" id="answer" value="" style="font-size: 30px;width: 200px" autocomplete="off" /></h2>
            </td>
        </tr>
        <tr>
            <td>
                <span id="tip"></span>
            </td>
        </tr>
    </table>
</div>
<div id="div3">
    <p class='heading'><span>Third：</span>Please record your voice.</p>
    <button id="start" onclick="startRecording(this);">start</button>
    <button id="stop" onclick="stopRecording(this);" disabled>stop</button>
</div>
<div id="div4">
    <input type="image" src="material/btn_sent01.png" width="330px" height="165px" id="bSendImage" disabled="true" />
    <br>
    <h5>注意!要完成作品才能出題考同學唷✔</h5>
</div>

<div id="div5"><p>↓↓<br>↓↓</p>
</div>
<div id="div6"><p>→→<br>→→</p>
</div>
</body>

</html>
