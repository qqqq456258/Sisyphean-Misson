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
    function srcToDataURI($src){
        $encoded_string = base64_encode(file_get_contents($src));
        $dataURI = "data:audio/wav;base64,".$encoded_string;
        return $dataURI;
    }
?>
<!DOCTYPE html>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <title>英語自繪王i-Drawing!</title>
    <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.4.js"></script>
    <script src="_js/Recorderjs-master/dist/recorder.js"></script>
    <style>
        @font-face{
            font-family:'SansForgetica';
            src:url(font/SansForgetica/SansForgetica-Regular.otf);
        }
        #logo{
            margin-top: 20px;
        }
        #plate_line{
            margin: 5px;
        }
        body,input { font-size: 15pt; }
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
            margin: 0px auto;
            background-image: url('material/bg.jpg');
        	background-repeat:no-repeat;
            background-size: cover;
        }
        
        #div1,#div2,#div3{
            float: left;
            text-align: center;
            margin: 0px 50px 50px 50px;
        }

        #dreset{
            float: left;
        }
        #dcheck{
            float: left;
            padding-left : 80px;
        }
        #dQuestion{
            font-family: 'SansForgetica';
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
        #start,#stop,#noChange{
            border: 0px;
            width: 130px;
            border-radius: 16px;
            font-family: '微軟正黑體';
            font-size: 36px;
            font-weight: 700;
            transition: .5s;
            margin: 20px;
        }
        #noChange:hover{
            width: 220px;
            background-color: hotpink;
            color: white;
        }
        
        #answer{
            font-size: 30px;
            width: 250px;
            padding: 5px 10px;
            border: 1px solid #000 ;
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-weight: bold;
        }
        #tip{
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 26px;
            font-weight: bold;
        }
        /*錄音部分*/
         ul { list-style: none; }
         #recordingslist audio {
             display: block; margin-bottom: 10px; 
        }
        @font-face{
            font-family:'support';
            src:url(font/support.ttf);
        }
        #BACK{
            position: absolute;
            top: 200px;
            right:100px;
            margin: 15px auto;
            float: right;
            text-align: center;
            font-family: 'support';
            font-weight: 700;
            font-size: 30px;
            height: 60px;
            border: 0px;
            border-radius: 20px;
            transition: 1s;
        }
        #BACK:hover{
            background-color: azure;
        }
        
    </style>
    
    <script>
        var tip_determined = 0; //用於判斷字元是否輸入完整。
        var voice_determined = 0; //用於聲音是否輸入完整。
        var Audio_dataURI="";
        $(function () {
            //產生不同顏色的div方格當作調色盤選項
            var colors = "#FF3333;#FF44AA;#FFAA33;#FFFF00;#33FF33;#227700;#33FFFF;#0000FF;#550088;#9900FF;#AA7700;#000000;#FFFFFF".split(';');
            var sb = [];
            $.each(colors, function (i, v) {
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
            $clrs.click(function () {
                $clrs.removeClass("active");
                $(this).addClass("active");
                p_color = this.style.backgroundColor;
                $lws.children("div").css("background-color", p_color);
            }).first().click();
            //點選線條粗細選項時切換焦點並取得寬度存入p_width
            $lws.click(function () {
                $lws.removeClass("active");
                $(this).addClass("active");
                p_width =
                    $(this).children("div").css("width").replace("px", "");

            }).eq(3).click();

            //取得canvas context
            var $canvas = $("#cSketchPad");
            var ctx = $canvas[0].getContext("2d");
            ctx.lineCap = "round";

            ctx.fillStyle = "white"; //整個canvas塗上白色背景避免PNG的透明底色效果

            var image = new Image();      //建立image物件，然後載入時一起放上canvas。
            image.onload = function() {
                var w = this.width,
                    h = this.height;
                ctx.drawImage(this, 0, 0, $canvas.width(), $canvas.height());
              }
            image.src = '<?php echo $_GET["imgPath"]; ?>';

            var drawMode = false;
            //canvas點選、移動、放開按鍵事件時進行繪圖動作
            $canvas.mousedown(function (e) {
                ctx.beginPath();
                ctx.strokeStyle = p_color;
                ctx.lineWidth = p_width;
                ctx.moveTo(e.pageX - $canvas.position().left, e.pageY - $canvas.position().top);
                drawMode = true;
            })
                .mousemove(function (e) {
                    if (drawMode) {
                        ctx.lineTo(e.pageX - $canvas.position().left, e.pageY - $canvas.position().top);
                        ctx.stroke();
                    }
                })
                .mouseup(function (e) {
                    drawMode = false;
                });

            //確認完成:利用.toDataURL()將繪圖結果轉成圖檔
            $("#bGenImage").click(function () {
                if(voice_determined == 0 && tip_determined == 0){
                    alert("記得「拼字」然後「發音」哦！！");
                }else if(voice_determined == 0){
                    alert("要記得發音 "+word+" 看看哦！！");
                }else if(tip_determined == 0){
                    alert("要記得先拼出 "+word+" 這個字哦！！");
                }else{
                    var canvas_to_image = $canvas[0].toDataURL();
                    var fileName = '<?php echo $_GET['fileName']; ?>';
                    
                    $.ajax({        //直接以ajax傳遞資料給modifySuccess.php。
                        type : "POST" ,
                        url : "modifySuccess.php" ,
                        data : {imgURI:canvas_to_image,audURI:Audio_dataURI,fileName:fileName},
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
                            window.location.href = 'userpage.php';
                        },
                    });
                }

            });

            //重畫
            $("#bReset").click(function () {
                ctx.fillStyle = "white";
                ctx.fillRect(0,0,$canvas.width(), $canvas.height());
            });

        });      
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
            button.nextElementSibling.nextElementSibling.disabled = false;
            
        }

        function stopRecording(button) {
            recorder && recorder.stop();
            button.disabled = true;
            createDownloadLink();
            recorder.clear();
        }

        function createDownloadLink() {
            recorder && recorder.exportWAV(function(blob) {
                
                $('#div2').html(
                    "<b style='font-size: 48px;font-weight: 700;'>你的聲音：</b><div style='margin-top:30px;'><span id='recordingslist'></span><b style='font-size: 48px;font-weight: 700;color:green;'>&ensp;✔</b></div>"
                );
                voice_determined = 1; //用於聲音是否輸入完整。
                
                var url = URL.createObjectURL(blob);
                var span = document.getElementById('recordingslist');
                var au = document.createElement('audio');
                
                var reader = new FileReader();
                /*FileReader 物件，Web 應用程式能以非同步（asynchronously）方式讀取儲存在用戶端的檔案（或原始資料暫存）內容
                ，可以使用 File 或 Blob 物件指定要讀取的資料*/
                
                reader.onloadend = function (e) {   //事件：每一次讀取結束之後觸發（不論成功或失敗）。
                    console.log(e.target.result);   //e.target.result(執行結果) → data:audio/wav;base64,...String...。
                    Audio_dataURI = e.target.result;    //存入變數準備處理。
                };
                reader.readAsDataURL(blob); //執行轉換blob → dataURI。
    
                
                au.controls = true;
                au.src = url;
                span.appendChild(au);
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
    <script>
        $(document).ready(function(){
            $('#answer').keyup(function() {
                var answer = $(this).val().toLowerCase(); //將輸入值轉換小寫。
                if (answer == word) {
                    $('#tip').css("color", "green").text("Right");
                    $('#div1').html("<b style='font-size: 48px;font-weight: 700;font-family: 'Lato', sans-serif;'>"+word+"<span style='color:green;'>&ensp;✔</span></b>");
                    tip_determined = 1; //用於判斷字元是否輸入完整。
                    /*  正確答案。  */
                } else if (answer == "") {
                    $('#tip').empty();
                    /*  若輸入為空，則清除提示  */
                } else if (answer.substring(0, answer.length) == word.substring(0, answer.length)) {
                    $('#tip').css("color", "#EE7700").text("Keep going");
                    /*  檢查輸入每個字是否錯誤，對的話就繼續。  */
                } else if (answer.length > word.length) {
                    $('#tip').css("color", "blue").text("Too much letters");
                    /*  檢查是否輸入超出答案的長度。  */
                } else {
                    $('#tip').css("color", "red").text("Wrong");
                    /*  檢查是否輸入輸入錯誤。  */
                }
           });
            
            $('#noChange').click(function(){
                $('#div2').html("<b style='font-size: 48px;font-weight: 700;'>你選擇不改變</b><div style='margin-top:30px;'><span id='recordingslist'><audio controls><source src = '"+voicePath+"' type='audio/mpeg'></audio></span><b style='font-size: 48px;font-weight: 700;color:green;'>&ensp;✔</b></div>");
                    
                Audio_dataURI = '<?php echo srcToDataURI($_GET['voicePath']); ?>';
                voice_determined = 1; //用於聲音是否輸入完整、是否延用。
            });
            
            $('#start,#stop').mousemove(function(){
               $(this).css("backgroundColor","black");
               $(this).css("color","white");
            });
            $('#start,#stop').mouseout(function(){
               $(this).css("backgroundColor","");
               $(this).css("color","black");
            });
            $('#start').click(function(){
               $(this).remove();
                $('#noChange').remove();
            });           
            $('#stop').click(function(){
               voice_determined = 1; //用於聲音是否輸入完整。
            });
        });
    </script>
</head>
<body>
<div id="dQuestion">
    <?php
        //輸出英文、中文字和語音檔。
        echo "<h3>".$_GET['english']."&emsp;".$_GET['chinese']."&emsp;<audio controls autoplay><source src = '".$_GET['voicePath']."' type='audio/mpeg'>Your browser does not support the audio element.</audio></h3>";
        echo "<script type='text/javascript'>var word = '".$_GET['english']."';</script>";
        echo "<script type='text/javascript'>var voicePath = '".$_GET['voicePath']."';</script>";
    ?>
</div>
<div id='div1'>
    <p class='heading'><span>First：</span>拼出這個字吧！！</p>
    <table border=0>
        <tr>
            <td>
                <h2><input type="text" id="answer" value="" autocomplete="off" /></h2>
            </td>
        </tr>
        <tr>
            <td>
                <span id="tip"></span>
            </td>
        </tr>
    </table>
</div>
<div id="div2">
    <p class='heading'><span>Second：</span>錄製聲音吧！！</p>
    <button id="start" onclick="startRecording(this);">開始</button>
    <button id="stop" onclick="stopRecording(this);" disabled>結束</button>
    <hr>
    <br>
    <button id="noChange">不改變</button>
    
</div>
<div id='div3'>
       <p class='heading'><span>Third：</span>畫出你想要的圖吧！！</p>
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
<button id="BACK" type="button" onclick="history.back()">回去我的單字簿</button>
</body>

</html>
