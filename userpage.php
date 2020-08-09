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
function yourRank($experience = 0){
    if($experience >=0 && $experience<10){
        $identity = "幼稚園";
    }elseif($experience >=10 && $experience<30){
        $identity = "小學生";
    }elseif($experience >=30 && $experience<55){
        $identity = "國中生";
    }elseif($experience >=55 && $experience<85){
        $identity = "高中生";
    }elseif($experience >=85 && $experience<120){
        $identity = "大學生";
    }elseif($experience >=120 && $experience<160){
        $identity = "碩士生";
    }elseif($experience >=160 && $experience<205){
        $identity = "博士生";
    }else{
        $identity = "頂峰";
    }
    return $identity;
}
include("connMysql0917.php"); //檢查有沒有登入

    $author = $_SESSION["user"];
    $sql_find_personal_information = "SELECT * FROM new_card_system.personal_information WHERE pi_account = :ACCOUNT";
    $stmt = $pdo->prepare($sql_find_personal_information);
    $stmt->bindValue(':ACCOUNT',$author); // 避免SQL injection。
    $stmt->execute() or exit("讀取personal_information資料表時，發生錯誤。"); //執行。 
    $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
    $headshot_path = $row[0]['personal_headshot'];
    $name = $row[0]['pi_name'];
    $experience = $row[0]['experience_amount'];
    $heartAmount = $row[0]['total_heart_amount'];
    $starAmount = $row[0]['total_star_amount'];
    $cardAmount = $row[0]['total_card_amount'];
    $nickName = yourRank();
    
?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
    <title>英語自繪王 i-Drawing!</title>
    <link href="https://fonts.googleapis.com/css?family=Lato|Roboto|Roboto+Condensed" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="_js/jquery-msearch-master/src/msearch.js"></script>
    <style type="text/css">
        @font-face{
            font-family:'SansForgetica';
            src:url(font/SansForgetica/SansForgetica-Regular.otf);
        }
        @font-face{
            font-family:'support';
            src:url(font/support.ttf);
        }
        #head {
            margin: auto;
        }
        body{
            margin: 0px auto;
            background-color: pink;
        }
        #pic_table{
            margin:10px auto;
            text-align: center;
        }
        #formModify,#formDelete{
            float: right;
            margin: 5px;
        }

        #searchPosition {
            background-color: pink;
            margin:auto;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: right;
            color: azure;
            float: none;
        }
        #search{
            text-align: center;
            background:#FFF url(material/search.png) no-repeat 7px 12px;
            margin: 5px 50px 5px 5px;
            padding: 3px;
            height: 45px;
            border-radius: 99px;
            font-family: 'Lato', sans-serif;
            font-weight: bold;
            width: 30px;
            border:3px solid white;
            font-size: 25px;
            transition: 1.2s;
        }
        #search:hover{
            width: 300px;
        }  

        #createPage,#userPage,#guessPage,#watchPage,#mission,#rank{
            font-weight: 600;
            background-color: white;
            border-radius: 15px;
            font-size: 23px;
            padding: 10px 10px 0 0;
            margin: 3px;
            text-align: center;
            display: inline-block;
            font-family: 'support';
            transition: .5s;
        }
        #createPage:hover,#userPage:hover,#guessPage:hover,#watchPage:hover,#mission:hover,#rank:hover{
            background-color: #777;
            color: aliceblue;
        }
        .btn{
            line-height: 170%;
            height:91px;
            float:left;
        }
        
        #info{
            padding: 10px;
            border-radius: 0 0 99% 99% ;
            background: #000;
            color: aliceblue;
            position: fixed;
            font-family: 'Microsoft JhengHei';
            text-align: center;
            font-size: 25px;
            transition: 1s;
        }
        
        #info:hover{
            padding: 15px;
            border-radius: 0 0 15px 0;
            width: auto;
            height: auto;
            transition: 1s;
        }
        
        #info:hover>#personalContent{
            display: block;
        }
        #mypic{
            width:200px;
            height:125px;
        }
        #info ul li{
            text-align: left;
        }
        #personalContent{
            display: none;
        }
    </style>
</head>
<body>
<script>
    $(document).ready(function(){
        $('#search').mouseover(function(){
            $(this).attr("placeholder","Search..."); 
            $(this).width("300px");
        });
        $('#search').mouseout(function(){
            $(this).attr("placeholder","");
            if( $(this).val().length == 0){
                $(this).width("30px");
            }
        });
        $('#search').keyup(function(){
            $(this).attr("placeholder","");
            if( $(this).val().length != 0){
                $(this).width("300px");
            }
        });
        $('#search').msearch('#pic_table .wrap', 'data-name');

    });
</script>
<script>
        function modifyCard(){     
            var question = confirm("你確定要修改字卡？");
            if(question == true){
                return true;
            }else{
                return false;
            }
	   }

        function deleteCard(){
            var TorF = confirm("你不要這張字卡了嗎？");
            if( TorF==true ){
                return true;
            }else{
                return false;
            }
        }
</script>
   
    <div id="info">【個人資料】<br>
       <div id="personalContent">
<?php
echo " <ul>
            <img src='$headshot_path'>
            <li>姓名：$name</li>
            <li>稱號：$nickName</li>
            <li>經驗值：$experience</li>
            <li>獲得發音評價：$starAmount</li>
            <li>獲得畫圖評價：$heartAmount</li>
            <li>字卡總數：$cardAmount</li>
    </ul>    
    ";
?>

        </div>
    </div>
    <div style=" background-color: #CCDDFF;">
        <img src="material/bg_userpage.png" width="900px" height="150px" style="margin:auto;display:block;" />
    </div>
    <div>
        <table id="head">
            <tr>
                <td>
                    <div style="margin:10px auto;text-align: center;">

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

                        
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <hr style="color:#FFF;">
    <div id="searchPosition">
        <input id='search' type="text"/>
    </div>
    <table id="pic_table">
            <?php
					$dir="image";  //得知檔案路徑。
					$images = glob('image/*.png');  //得到image資料夾裡所有檔案之路徑。
					$num = count($images); //得到檔案總數。
                    $control = 0;
					for($i=0; $i<$num; $i++)       //目的：從image資料夾中抓出每個字卡，並輸出出來。
					{
						$string = $images[$i];
						$spilt_string = explode('_', $string);    // 遇到'_'，就切割string，結果為分割出的各段字串組成的陣列。
					    $spilt_author = $spilt_string[1];         // [1]為學號。				
						$spilt_vacabulary = $spilt_string[2];     // [2]為畫出的單字。
					    
                        $sql = "SELECT chinese_vacabulary FROM new_card_system.vacabulary WHERE english_vacabulary = :Spilt_vacabulary";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':Spilt_vacabulary',$spilt_vacabulary); // 避免SQL injection。
                        $stmt->execute() or exit("讀取 vacabulary 資料表時，發生錯誤。"); //執行。 
                        $row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一全部取出，並以陣列放入$row。
                        $chinese = $row[0]['chinese_vacabulary'];    //照指令執行只有取到一筆，所以輸出陣列中 中文字 欄位就好。 
                        
                        
					    if($spilt_author == $author){
                            $voicePath = str_replace('image/', 'ownVoice/', $string);
                            $voicePath = str_replace('.png', '.wav', $voicePath);
                            $filename = str_replace('image/', '', $string);
                            $filename = str_replace('.png', '', $filename);
                            
                            if($control%3==0){
                                echo "<tr>";
                            }
                            echo "<script type='text/javascript'>var english = '".$spilt_vacabulary."';</script>";
                            echo "<script type='text/javascript'>var chinese = '".$chinese."';</script>";
                            echo "<script type='text/javascript'>var imgPath = '".$string."';</script>";
                            echo "<script type='text/javascript'>var voicePath = '".$voicePath."';</script>";
                            
                        
							echo "<td class='wrap' data-name='".$spilt_vacabulary."".$chinese."' width=435px height=500px style='border-radius: 15px; border:10px #FFBB66 solid;background-color:#FFFFFF; ' align='center' ><h1>".$spilt_vacabulary." ".$chinese."</h1>".
                                
								"<audio controls controlsList='nodownload'><source src = '$voicePath' type='audio/mp3'></audio>".
                                
								"<form method='POST' id='formDelete' name='formDelete' action='deletePage.php' onsubmit=' return deleteCard();'>".
								"<input type='text' hidden='hidden' name='fileName' value='$filename'/>".
                                "<input type='image' class='formDelete' title='我不要這張卡' src='material/userpage_delete.png'/>".
								"</form>".
                                
								"<form method='GET' id='formModify' name='formModify' action='modifyCard.php' onsubmit=' return modifyCard();'>".
								"<input type='text' hidden='hidden' name='english' value='$spilt_vacabulary'/>".
								"<input type='text' hidden='hidden' name='chinese' value='$chinese'/>".
								"<input type='text' hidden='hidden' name='fileName' value='$filename'/>".
								"<input type='text' hidden='hidden' name='imgPath' value='$string'/>".
								"<input type='text' hidden='hidden' name='voicePath' value='$voicePath'/>".
								"<input type='image' class='modify' title='把卡改成我想要的樣子'  src='material/userpage_edit.png'/>".
								"</form>".
                                "</br><img src = '$images[$i]' width=400px height=400px border='1' style='border:3px #FFD382 dashed;'/>"."</td>";
                            if($control%3==2){
                                echo "</tr>";
                            }
                            $control++;
						}
					}
                    $pdo = null;    //關閉以省資源。
				?>
        </table>
</body>

</html>
