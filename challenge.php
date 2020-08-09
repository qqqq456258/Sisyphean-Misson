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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>challenge</title>
    <link href="https://fonts.googleapis.com/css?family=Lato|Roboto|Roboto+Condensed" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="_js/jquery-migrate.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Oswald|Roboto+Condensed" rel="stylesheet">
<style>

* {
  margin: 0;
  padding: 0;
  border: 0;
  box-sizing: border-box;
}
*:before, *:after {
  box-sizing: inherit;
}

html {
  width: 100vw;
  height: 100vh;
}

.start-screen {
    text-align: center;
    display: flex;
    flex-flow: nowrap column;
    align-items: center;
    margin: 40px;
}

.loading {
    text-align: center;
    display: flex;
    margin: 0px auto;
    color: #fff5a5;
    font-family: 'Roboto';
    font-weight: 900;
}

.loading__element {
  font: normal 100 2rem/1 Roboto;
  letter-spacing: .5em;
    font-size: 100px;
}

[class*="el"] {
  -webkit-animation: bouncing 2s infinite ease;
          animation: bouncing 2s infinite ease;
}

.el1 {
  -webkit-animation-delay: 0.1s;
          animation-delay: 0.1s;
}

.el2 {
  -webkit-animation-delay: 0.2s;
          animation-delay: 0.2s;
}

.el3 {
  -webkit-animation-delay: 0.3s;
          animation-delay: 0.3s;
}

.el4 {
  -webkit-animation-delay: 0.4s;
          animation-delay: 0.4s;
}

.el5 {
  -webkit-animation-delay: 0.5s;
          animation-delay: 0.5s;
}

.el6 {
  -webkit-animation-delay: 0.6s;
          animation-delay: 0.6s;
}

.el7 {
  -webkit-animation-delay: 0.7s;
          animation-delay: 0.7s;
}

.el8 {
  -webkit-animation-delay: 0.8s;
          animation-delay: 0.8s;
}

.el9 {
  -webkit-animation-delay: 0.9s;
          animation-delay: 0.9s;
}


    body{
        background-image: url(images/mountain.png);
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment:fixed;
        background-position:center;
        height: 100vh;
        overflow-y:hidden;
    }
    #degreeOfDifficulty{
        color: #3dce9d;
        text-align: center;
        margin: 40px auto;
        font-size: 120px;
        font-family: '微軟正黑體';
        font-weight: 900;
        display: none;
    }
    #normal,#nightmare,#rule{
        font-weight: 900;
        padding: 15px;
        margin: 80px 80px 0 80px;
        font-size: 70px;
        border: 0px solid white;
        font-family: '微軟正黑體';
        color: #1b95a2;
        text-align: center;
        border-radius: 30px;
        background: transparent;
        transition: 1s;
    }
    #rule{
        width: 380px;
        transition: .6s;
    }
    #normal:hover{
        color: #CC0000;
        background-color: white;
    }
    #nightmare:hover{
        color: #9900FF;
        background-color: black;
    }
    #rule:hover{
        color: darkgoldenrod;
        background-color: white;
        width: 500px;
    }
    #Preprocess{
        margin: 0px auto;
        text-align: center;
    }
    #StartGame,#leaveGame,#honorRoll,#story{
        font-family: '微軟正黑體';
        font-weight: 700;
        padding: 10px;
        margin: 30px;
        font-size: 70px;
        border: 0px solid white;
        color: #3fc7a8;
        text-align: center;
        border-radius: 30px;
        background: transparent;
        transition: .7s;
    }
    #StartGame:hover{
        border: 20px solid white;
        background: white;
        color: red;
    }
    #honorRoll:hover{
        border: 20px solid white;
        background: white;
        color: #00FF99;
    }
    #story:hover{
        border: 20px solid white;
        background: white;
        color: #7744FF;
    }
    #leaveGame:hover{
        border: 20px solid red;
        background: red;
        color: black;
    }
    #number{
        position: relative;
        top: 200px;
        margin:0px auto;
        text-align: center;
        font-size: 150px;
        font-weight: 800;
        font-family: '微軟正黑體';
        color: dodgerblue;
        display: none;
        white-space:pre;
    }
    #fiveSecond{
        margin:20px auto;
        text-align: center;
        color: aliceblue;
        font-size: 70px;
        font-weight: bolder;
        display: none;
    }
    main{
        margin: 20px 0px 0px 0px;
        text-align: center;
    }
    #result{
        position: relative;
        top: 50px;
        margin:0px auto;
        font-family: '微軟正黑體';
        font-size: 160px;
        font-weight: 900;
        color: whitesmoke;
        display: none;
    }
    #Gameover,#Right,#Wrong{
        display: none;
    }
    #question{
        margin: auto;
        font-family: '微軟正黑體';
        font-size: 140px;
        font-weight: 900;
        color: whitesmoke;
        display: none;
    }
    #option{
        padding: 10px;
        display: none;
    }
    #first,#second,#third,#fourth{
        border: 0px;
        display: inline-block;
        margin: 40px;
        height: 120px;
        width: 300px;
        font-size: 30px;
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-family: 'Lato';
        background-color: maroon;
        transition: .2s;
    }
    #first:hover,#second:hover,#third:hover,#fourth:hover{
        background-color: firebrick;
        font-weight: 900;
        font-size:34px; 
        
    }
    #GameRule{
        font-family: '微軟正黑體';
        text-align: center;
        margin: 0px auto;
        background: url(images/gameRuleBg.jpg);
    }
    #GameResult{
        font-family: '微軟正黑體';
        text-align: center;
        margin: 0px auto;
        font-weight: 800;
        font-size: 30px;
    }
    #GameRule h2{
        text-align: center;
        font-size: 45px;
        font-weight: 700;
        margin: 15px auto;
        color: black;
    }
    #GameRule .context{
        text-align: center;
        font-size: 30px;
        font-weight: 900;
        margin: 0px auto;
        color: #FF3333;
    }
    #focus{
        color: #5500FF;
    }
    #Rank{
        background: url(images/challenge_rank_bg.jpg);
        font-family: '微軟正黑體';
        text-align: center;
        margin: 0px auto;
        color: #FFCC22;
        font-size: 16px;
    }
    #Story{
        text-align: center;
        margin: 0px auto;
        overflow-x: hidden;
        overflow-y: hidden;
    }
    table{
        width: 100%;
        text-align: center;
        margin: 0px auto;
    }
    th{
        color: aliceblue;
        font-size: 22px;
        font-family: '微軟正黑體';
    }

    .no-close .ui-dialog-titlebar-close ,ui-dialog-titlebar ui-corner-all ui-widget-header ui-helper-clearfix{
      display: none;
        height: 0px;
    }
    
    .ui-dialog .ui-dialog-title{
        width: 100%;
        font-family: '微軟正黑體';
        font-size:60px;
        font-weight: 700;
        color: maroon;
        text-align:center;
    }
    .ui-dialog-content ui-widget-content{
        text-align:center;
        margin: 0px auto;
    }
    .ui-widget-overlay{
/*視窗外覆蓋之區塊*/
        background-color: black;
    }
    .ui-button-icon ui-icon ui-icon-closethick,ui-button ui-corner-all ui-widget ui-button-icon-only ui-dialog-titlebar-close{
        width: 80px;
        height: 80px;
    }
    .ui-button ui-corner-all ui-widget,ui-dialog-buttonset{
        border-radius: 20px;
        font-weight: 600;
        font-size: 25px;
        font-family: '微軟正黑體';
    }
</style>
<script>
    var fiveSecond = 5;     //倒數數字
    var reciprocal = 0;     //倒數器回傳參數true/false。
    var translation = [];   //題目翻譯(一維陣列)
    var translation_order = []; //題目順序
    var word = [];  //英文選項(一維陣列)
    var word_order = [];    //英文選項順序(二維陣列)
    var voice = [];         //題目語音位置
    var num = 0;            //到達層數
    var degree = "";        //難易度回傳
    var option = 0;         //以參數控制進入哪個php函式去做插入或搜尋資料庫。
    var numOfQuestion = 0;
   
    window.onload=function(){  //先載入成為快取。
        var img=new Image(); 
        img.src='images/challenge-2.jpg'; 
        img.onload=function(){ 
            var img2=new Image(); 
            img2.src='images/2.jpg';
            img2.onload=function(){ 
                console.log("兩張圖片OK");
            }
        } 
     } 
    
    function Timer() {
        fiveSecond = fiveSecond-1;
        $('#fiveSecond').text(fiveSecond);
        if(fiveSecond<=0){
            Stop();
            $('#fiveSecond,#question,#option').hide();
            $('#Gameover').show();
            $('#result').delay(500).show(1000,function(){         
                if(degree == "nightmare"){
                    /*插入資料排行榜*/
                    option = 2;
                    $. ajax({
                        type : "POST" ,
                        url : "sisyphean_data.php",
                        data : {Level_num:num , option:option},
                        dataType: "text",
                        success: function(data){
                            console.log(data);
                            }
                    });
                    
                    var data = "你到達了&emsp;<span style='color:red;'>Level&ensp;"+num+"</span><br>答案是：<br><br><span style='font-size:50px;color:#008800;'>"+word[translation_order[num]]+"&ensp;<br>"+translation[translation_order[num]]+"</span><br><br>你要把它做成字卡嗎？";
                    $('#GameResult').html(data);
                    $('#GameResult').attr("display","block");
                    $('#GameResult').dialog({
                        dialogClass: "no-close",
                        draggable: false,
                        modal: true,
                        width:600,
                        show: {
                            effect: "bounce",
                            duration: 500
                        },
                        hide: {
                            effect: "clip",
                            duration: 500
                        },
                        buttons: {
                            "好的": function() {
                                $( this ).dialog( "close" );
                                location.href = "sisyphean_newCard.php?word="+word[translation_order[num]]+"&voicePath="+voice[translation_order[num]]+"&translation="+translation[translation_order[num]];
                            },
                            "取消": function() {
                                $( this ).dialog( "close" );
                                location.href = "challenge.php";
                            }

                        }
                    });   

                }else{
                    var data = "你到達了&emsp;<span style='color:red;'>Level&ensp;"+num+"</span><br>答案是：<br><br><span style='font-size:50px;color:#008800;'>"+word[translation_order[num]]+"&ensp;<br>"+translation[translation_order[num]]+"</span>";
                    console.log(data);
                    $('#GameResult').html(data);
                    $('#GameResult').attr("display","block");
                    $('#GameResult').dialog({
                        dialogClass: "no-close",
                        draggable: false,
                        width:600,
                        modal: true,
                        show: {
                            effect: "bounce",
                            duration: 500
                        },
                        hide: {
                            effect: "clip",
                            duration: 500
                        },
                          buttons: {
                            "O K": function() {
                              $( this ).dialog( "close" );
                                location.href = "challenge.php";
                            }
                          }
                    });   
                    
                    
                    
                    
                }
                
                
                
            });
            

            
        }
    }
    function start(){
        num = 0;
        fiveSecond = 5;
        $('#number').text("Level  "+(num+1));
        $('#fiveSecond').text(fiveSecond);
        if(degree == "normal"){
            $('#number,#fiveSecond,#question,#Gameover').css('color','#ffd900');
            $('body').css("backgroundImage","url('images/2.jpg')");
            numOfQuestion = 5;
        }else{
            $('body').css("backgroundImage","url('images/challenge-2.jpg')");
            numOfQuestion = 10;
        }
        combinationForQA(numOfQuestion);
        inputData(0);
    }
    function Stop() {
        clearInterval(reciprocal);
        fiveSecond = 5;
        $('#fiveSecond').text(fiveSecond);
    }
    function inputData(_no){
        console.log(word[translation_order[_no]]);
        $('#question').text(translation[translation_order[_no]]);
        $('#first').text(word[word_order[_no][0]]);
        $('#second').text(word[word_order[_no][1]]);
        $('#third').text(word[word_order[_no][2]]);
        $('#fourth').text(word[word_order[_no][3]]);
    }
    function NextOne(){
        Stop();
        num++;
        $('#number').text("Level  "+(num+1));
        $('#fiveSecond').text(fiveSecond);
        console.log("找題目的Number: "+num);
        inputData(num);
    }
    
    function combinationForQA(numOfQuestion){
        var rdmArray = new Array();
        for(var i=0;i<numOfQuestion;i++){
            var rdm = 0;
            do{
                var exist = false;
                rdm = Math.floor(Math.random()*numOfQuestion);
                if(rdmArray.indexOf(rdm) != -1){
                    exist = true;
                }
            }while(exist);
            
            rdmArray[i] = rdm;
        }
        translation_order = rdmArray;
        
        var rdmArrayGroup = new Array(numOfQuestion);
        for(var i=0 ;i<numOfQuestion ;i++){
            var target = rdmArray[i];
            rdmArrayGroup[i] = new Array(4);
            for(var j=0;j<4;j++){
                var rdm = 0;
                do{
                    var exist = false;
                    rdm = Math.floor(Math.random()*numOfQuestion);
                    if(rdmArrayGroup[i].indexOf(rdm) != -1){
                        exist = true;
                    }
                }while(exist);
                rdmArrayGroup[i][j] = rdm;
                if(j==3 && rdmArrayGroup[i].indexOf(target) == -1){
                    j=-1;
                }
            }
        }
            word_order = rdmArrayGroup;
    }
    function leaveGame(){
        window.location.href = 'userpage.php'; 
    }
    function getVocabulary(numOfQuestion){
        var rdm = 0;
        rdm = Math.floor(Math.random()*numOfQuestion);
        return rdm;
    }
    function getTable(){
        $('#Rank').attr("display","block");
        $('#Rank').dialog({
            dialogClass: "no-close",
            draggable: false,
            width:500,
            modal: true,
            show: {
                effect: "clip",
                duration: 500
            },
            hide: {
                effect: "clip",
                duration: 500
            }
        });   
        
        $('.ui-widget-overlay').click(function(){
            $('#Rank').dialog('close'); 
            
        });
    }
    function getStory(){
        $('#Story').attr("display","block");
        $('#Story').dialog({
            dialogClass: "no-close",
            draggable: false,
            modal: true,
            width:1000,
            show: {
                effect: "clip",
                duration: 500
            },
            hide: {
                effect: "clip",
                duration: 500
            }
        });   
            
        $('.ui-widget-overlay').click(function(){
            $('#Story').dialog('close'); 
            
        });
    }
    function getRule(){
        $('#GameRule').attr("display","block");
        $('#GameRule').dialog({
            dialogClass: "no-close",
            draggable: false,
            modal: true,
            width:950,
            height:650,
            show: {
                effect: "clip",
                duration: 1000
            },
            hide: {
                effect: "clip",
                duration: 1000
            }
        });   
            
        $('.ui-widget-overlay').click(function(){
            $('#GameRule').dialog('close'); 
        });
    }   

    
    
    $(document).ready(function(){
      
        /*按下【開始挑戰】的動畫執行順序。*/
        $('#StartGame').click(function(){ //按下開始挑戰。
            $('#Preprocess').hide();
            $('#degreeOfDifficulty').delay(200).fadeIn(500);
            $('#normal,#nightmare').click(function(){ // 挑選難易度。
                $('#degreeOfDifficulty').hide();
                degree = $(this).val();
                option = 1;
                console.log(degree);
                $. ajax({        //直接以ajax傳遞資料。
                    type : "POST" ,
                    url : "sisyphean_data.php",
                    data : {degree:degree , option:option},
                    dataType: "json",
                    success: function(json){
                        var NumOfData = json.length; //取出物件長度
                        console.log("傳過來的題目數量："+NumOfData);
                        for(var i = 0; i<json.length ; i++){
                            word[i] = json[i]['english_vacabulary'];
                            translation[i] = json[i]['chinese_vacabulary'];
                            voice[i] = json[i]['voice_path'];
                        }
                        
                        start();
                        
                          $('#number').delay(300).show(700,function(){
                            $(this).delay(1000).hide(700,function(){
                                $('#fiveSecond,#question').delay(500).show(1000,function(){
                                    $('#option').delay(200).fadeIn(300);
                                    Stop();
                                    reciprocal = setInterval(Timer, 1000);
                                });
                            });
                          });
                    }
                });
            });    
        });
        
        /*倒數最後三秒時，秒數變紅。*/
        $('#fiveSecond').on("DOMSubtreeModified",function(){
           if($(this).text()=="1" ||$(this).text()=="2" ||$(this).text()=="3" ){
               /*想新增小數點秒數*/
               $(this).css("color","red");
           }else{
               if(degree == "normal"){
                    $(this).css("color","#ffd900");
               }else{
                    $(this).css("color","white");
               }
           }
        });
        
        /*按下選項後的反應和動畫。*/
        $('#first,#second,#third,#fourth').click(function(){
                console.log($(this).text());
                
                if(word[translation_order[num]] == $(this).text()){ // 答對的情況。
                    Stop();
                    $('#fiveSecond,#question,#option').hide();
                    $('#Right').show();
                    console.log("finalNumber:"+num);
                    if(num<numOfQuestion-1){ // 繼續下一題。
                        NextOne();
                        $('#result').show(function(){
                            $(this).delay(1000).hide(500,function(){
                                $('#Right').hide();
                                $('#number').delay(300).show(700,function(){
                                    $(this).delay(1000).hide(700,function(){
                                        $('#fiveSecond,#question').delay(500).show(1000,function(){
                                            $('#option').delay(200).fadeIn(300);
                                            Stop();
                                            reciprocal = setInterval(Timer, 1000);
                                        });
                                    });
                                });
                            });
                        });
                    }else{  // 把題目都解決的情況。
                        Stop();
                        $('#fiveSecond,#question,#option,#Right').hide();
                        $('#Gameover').text("恭喜你過關！！");
                        $('#Gameover').show();
                        $('#result').fadeIn(1300,function(){
                            var rdm = getVocabulary(numOfQuestion);
                            if(degree == "nightmare"){
                                var data = "你到達了&emsp;<span style='color:red;'>Level&ensp;"+(num+1)+"</span><br>你將獲得〝大量經驗值〞<br><span style='font-size:50px;color:#008800;'>還有<br>"+word[rdm]+"&ensp;<br>"+translation[rdm]+"</span><br><br>要畫畫新字卡嗎？"; 
                                
                                /*放上排行榜。*/
                                    option = 2;
                                    $. ajax({
                                        type : "POST" ,
                                        url : "sisyphean_data.php",
                                        data : {Level_num:(num+1) , option:option},
                                        dataType: "text",
                                        success: function(data){
                                        }
                                    });
                                
                                /*出現DIALOG*/
                                $('#GameResult').html(data);
                                $('#GameResult').attr("display","block");
                                $('#GameResult').dialog({
                                    dialogClass: "no-close",
                                    draggable: false,
                                    modal: true,
                                    width:600,
                                    show: {
                                        effect: "bounce",
                                        duration: 500
                                    },
                                    hide: {
                                        effect: "clip",
                                        duration: 500
                                    },
                                    buttons: {
                                        "好的": function() {
                                            $( this ).dialog( "close" );
                                            location.href = "challenge.php";
                                            location.href = "sisyphean_newCard.php?word="+word[rdm]+"&voicePath="+voice[rdm]+"&translation="+translation[rdm];
                                        },
                                        "取消": function() {
                                            $( this ).dialog( "close" );
                                            location.href = "challenge.php";
                                        }

                                    }
                                }); 
                            }else{
                                var data = "恭喜你到達了&emsp;<span style='color:red;'>Level&ensp;"+(num+1)+"</span><br>你將獲得〝大量經驗值〞。<br>"; 
                                $('#GameResult').html(data);
                                $('#GameResult').attr("display","block");
                                $('#GameResult').dialog({
                                    dialogClass: "no-close",
                                    draggable: false,
                                    modal: true,
                                    width:600,
                                    show: {
                                        effect: "bounce",
                                        duration: 500
                                    },
                                    hide: {
                                        effect: "clip",
                                        duration: 500
                                    },
                                    buttons: {
                                        "O K": function() {
                                            $( this ).dialog( "close" );
                                            location.href = "challenge.php";
                                        }

                                    }
                                });    
                            }
    
                        });
                    }
                    
                    
                }else{  //答錯的情況
                    Stop();
                    $('#fiveSecond,#question,#option').hide();
                    $('#Wrong').show();
                    $('#result').show(function(){
                        $('#Wrong').delay(500).hide(500,function(){
                            $('#Gameover').delay(500).show(1000,function(){
                                if(degree == "nightmare"){  
                                    /*插入資料排行榜*/
                                    option = 2;
                                    $. ajax({
                                        type : "POST" ,
                                        url : "sisyphean_data.php",
                                        data : {Level_num:num , option:option},
                                        dataType: "text",
                                        success: function(data){
                                                console.log(data);
                                            }
                                    });

                                    var data = "你到達了&emsp;<span style='color:red;'>Level&ensp;"+num+"</span><br>答案是：<br><br><span style='font-size:50px;color:#008800;'>"+word[translation_order[num]]+"&ensp;<br>"+translation[translation_order[num]]+"</span><br><br>你要把它做成字卡嗎？";
                                    $('#GameResult').html(data);
                                    $('#GameResult').attr("display","block");
                                    $('#GameResult').dialog({
                                        dialogClass: "no-close",
                                        draggable: false,
                                        modal: true,
                                        width:600,
                                        show: {
                                            effect: "bounce",
                                            duration: 500
                                        },
                                        hide: {
                                            effect: "clip",
                                            duration: 500
                                        },
                                        buttons: {
                                            "好的": function() {
                                                $( this ).dialog( "close" );
                                                location.href = "sisyphean_newCard.php?word="+word[translation_order[num]]+"&voicePath="+voice[translation_order[num]]+"&translation="+translation[translation_order[num]];
                                            },
                                            "取消": function() {
                                                $( this ).dialog( "close" );
                                                location.href = "challenge.php";
                                            }
                                        }
                                    });   

                                }else{
                                    var data = "你到達了&emsp;<span style='color:red;'>Level&ensp;"+num+"</span><br>答案是：<br><br><span style='font-size:50px;color:#008800;'>"+word[translation_order[num]]+"&ensp;<br>"+translation[translation_order[num]]+"</span>";
                                    console.log(data);
                                    $('#GameResult').html(data);
                                    $('#GameResult').attr("display","block");
                                    $('#GameResult').dialog({
                                        dialogClass: "no-close",
                                        draggable: false,
                                        width:600,
                                        modal: true,
                                        show: {
                                            effect: "bounce",
                                            duration: 500
                                        },
                                        hide: {
                                            effect: "clip",
                                            duration: 500
                                        },
                                          buttons: {
                                            "O K": function() {
                                              $( this ).dialog( "close" );
                                                location.href = "challenge.php";
                                            }
                                          }
                                    });   
                                }
                            });
                        });
                    });
                    
                }

            
            
        });
        
    });
</script>

</head>
<body>

<audio id="challengeAudio" autoplay loop preload="true">
  <source src="background_music/challenge.mp3" type="audio/mpeg">
  Your browser does not support HTML5 video.
</audio>
<audio id="normalAudio" loop preload="true">
  <source src="background_music/normal.mp3" type="audio/mpeg">
  Your browser does not support HTML5 video.
</audio>
<audio id="nightmareAudio" loop preload="true">
  <source src="background_music/nightmare.mp3" type="audio/mpeg">
  Your browser does not support HTML5 video.
</audio>
   
    <div id="Preprocess">
        <div class="start-screen">
            <div class="loading">
        <div class="loading__element el1">S</div>
        <div class="loading__element el2">i</div>
        <div class="loading__element el3">s</div>
        <div class="loading__element el4">y</div>
        <div class="loading__element el5">p</div>
        <div class="loading__element el6">h</div>
        <div class="loading__element el7">e</div>
        <div class="loading__element el7">a</div>
        <div class="loading__element el7">n</div>
      </div>
            <div class="loading">
        <div class="loading__element el1">M</div>
        <div class="loading__element el2">i</div>
        <div class="loading__element el3">s</div>
        <div class="loading__element el4">s</div>
        <div class="loading__element el5">i</div>
        <div class="loading__element el6">o</div>
        <div class="loading__element el7">n</div>
      </div>
        </div>
        <button id="StartGame">開始任務</button>
        <button id="honorRoll" onclick="getTable();">排行榜</button><br>
        <button id="story" onclick="getStory();">故事書</button>
        <button id="leaveGame" onclick="leaveGame();">離開這裡</button>
    </div>

    <div id="number"></div>
    
    <div id="degreeOfDifficulty">
        <span id="degreeOfDifficultyTitle">選擇難易度</span><br>
        <button id="normal" value="normal">練習</button>
        <button id="nightmare" value="nightmare">競賽</button><br>
        <button id="rule" onclick="getRule();">遊玩說明</button>
    </div>
    
    <main>
        <div id="fiveSecond"></div>
        
        <div id="question"></div>
        
        <div id="option">
            <button id="first"></button>
            <button id="second"></button><br>
            <button id="third"></button>
            <button id="fourth"></button>
        </div>
        
        <div id="result">
            <div id="Right"><img src='material/youAreRight.png'></div>
            <div id="Wrong"><img src='material/youAreWrong.png'></div>
            <div id="Gameover">Game Over</div>
        </div>
    </main>
    <div id="GameRule" title="遊戲說明" style="display:none;">
        <h2>【練習模式】</h2>
        <span class="context">每層樓會出一道中文題目，而下方會出現四個英文選項。<br>限時&ensp;5&ensp;秒內答對，即可上一層樓。<br>共有<span style="color:forestgreen;">&ensp;10&ensp;</span>層樓等你挑戰。</span><br><br>
        <h2>【競賽模式】</h2>
        <span class="context">每層樓會出一道中文題目，而下方會出現四個英文選項，<br>限時&ensp;5&ensp;秒內答對，即可上一層樓。<br><span id="focus">無論失敗或成功，都可以獲得創建字卡的機會，並進入排行榜上。</span><br>共有<span style="color:#0066FF;">&ensp;50&ensp;</span>樓，你能到達第幾層樓呢？</span><br>
    </div>
    <div id="Rank" title="排行榜" style="display:none;">
        <?php
            $sql_refresh_rank = "SELECT studentID,level_num FROM new_card_system.sisyphean_rank ORDER BY level_num DESC LIMIT 10";
            $stmt = $pdo->prepare($sql_refresh_rank);
            $stmt->execute() or exit("讀取 sisyphean_rank 資料表時，發生錯誤。"); //執行。
            $row_rank = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
            $numOfdata = count($row_rank);

            echo "<table rules='all' cellpadding='15'><tr><th>競賽排名</th><th>名字</th><th>層數</th></tr>";

            for($i=0 ; $i<$numOfdata ; $i++){

                $sqlForName = "SELECT pi_name FROM new_card_system.personal_information WHERE pi_account = :ID";
                $stmt = $pdo->prepare($sqlForName);
                $stmt->bindValue(':ID',$row_rank[$i]['studentID']); // 避免SQL injection。以 :UserID 代替並放入語法內。
                $stmt->execute() or exit("讀取personal_information資料表時，發生錯誤。");
                $row_name = $stmt->fetchALL(PDO::FETCH_ASSOC);
                if($i == 0 || $i == 1 || $i == 2 ){
                    echo "<tr>";
                    echo "<td><img src='images/NO".($i+1).".png' width='35px' height='45px'>No.".($i+1)."</td>";
                    echo "<td>".$row_name[0]['pi_name']."</td>";
                    echo "<td>".$row_rank[$i]['level_num']."</td>";
                    echo "</tr>";
                }else{
                    echo "<tr>";
                    echo "<td>No.".($i+1)."</td>";
                    echo "<td>".$row_name[0]['pi_name']."</td>";
                    echo "<td>".$row_rank[$i]['level_num']."</td>";
                    echo "</tr>";
                }
            }

            echo "</table>";
        ?>
    </div>
    <div id="Story" title="薛西弗斯的故事" style="display:none;">
        <img src="images/Story.png" style="width:953.5px;height:572px;">
    </div>
    <div id="GameResult" title="遊戲結束" style="display:none;"></div>
    <script>
//
//        var normal = document.getElementById("#normalAudio"); 
//        var nightmare = document.getElementById("#nightmareAudio"); 
//        var challenge = document.getElementById("#challengeAudio");
        
        function playAud(ID) {
            var Object = "#"+ID;
            var _audio = document.getElementById(Object); 
            _audio.play();
        }
        function pauseAud(ID) {
            var Object = "#"+ID;
            var _audio = document.getElementById(Object); 
            _audio.pause();
        }
        function preloadAud(ID){
            var Object = "#"+ID;
            var _audio = document.getElementById(Object); 
            _audio.preload = "auto";
        }
        function adjust(_vol,ID){
            var Object = "#"+ID;
            var _audio = document.getElementById(Object); 
            var _volume = (_vol/100);
            _audio.volume = _volume;
        }
        
    </script>
    
</body>

</html>