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
    $images = glob("image/*.png");
    $num = count($images);
?>		
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
	<title>英語自繪王MyWords!</title>
	<style type="text/css">
		body{
			background-image: url('material/bg_rank.png');
            background-size: cover;
		}
        #total{
            margin:50px auto;
            text-align: center;
            font-size: 48px;
            font-weight: 600;
        }
        #total_num{
            margin:0 10px 0 20px;
            font-size: 60px;
            font-weight: 700;
            color: goldenrod;
        }
        #king{
            border: 2px solid #015;
            margin-left:auto; 
            margin-right:auto;
            font-size: 24px;
            font-weight: 400;
        }
        th,td{
            text-align: center;
            font-family: '微軟正黑體';
        }
        th{
            font-weight: 700;
            font-size: 50px;
            color: firebrick;
        }
        td{
            color: currentColor;
            font-weight: bolder;
            font-size: 40px;
            
        }
        .data{
            font-weight: 600;
            font-size: 30px;
            color: darkgreen;
        }
      @font-face{
            font-family:'support';
            src:url(font/support.ttf);
        }
        #createPage,#userPage,#guessPage,#watchPage,#mission,#rank{
            font-weight: 900;
            background-color: white;
            border-radius: 15px;
            font-size: 23px;
            padding: 10px 15px 0 0;
            margin: 4px;
            text-align: center;
            font-family: 'support';
            display: inline-block;
            transition: .5s;
        }
        #createPage:hover,#userPage:hover,#guessPage:hover,#watchPage:hover,#mission:hover,#rank:hover{
            background-color: #CCDDFF;
        }
        .btn{
            line-height: 170%;
            height:91px;
            float:left;
        }
	</style>

</head>
<body>
<audio autoplay loop>
   <source src="background_music/rank_music.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<div style="text-align:center;">			
    <img src="material/billboard.png" width="400px" height="200px">	
</div>
<div align="center">
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
<hr>
<div>
    <p id="total">大家一起創作了<? echo "<span id='total_num'>".$num."</span>"; ?> 張字卡</p>
    <table id="king" cellpadding="5" border="1">
        <tr>
            <th>排名</th>
            <th>天才小畫家</th>
            <th>單字發音王</th>
            <th>薛西弗斯的任務</th>
            <th>字卡收集王</th>
            <th>系統等級王</th>
        </tr>
    <?php
        $sql = "SELECT pi_name, total_heart_amount FROM new_card_system.personal_information ORDER BY total_heart_amount DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row_1 = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        
        $sql = "SELECT pi_name, total_star_amount FROM new_card_system.personal_information ORDER BY total_star_amount DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row_2 = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        
        $sql = "SELECT new_card_system.personal_information.pi_name, new_card_system.sisyphean_rank.level_num FROM new_card_system.personal_information INNER JOIN new_card_system.sisyphean_rank on new_card_system.sisyphean_rank.studentID = new_card_system.personal_information.pi_account ORDER BY new_card_system.sisyphean_rank.level_num DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row_3 = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        
        $sql = "SELECT pi_name, total_card_amount FROM new_card_system.personal_information ORDER BY total_card_amount DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row_4 = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        
        $sql = "SELECT pi_name, experience_amount FROM new_card_system.personal_information ORDER BY experience_amount DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row_5 = $stmt->fetchALL(PDO::FETCH_ASSOC); 
        
        for($i=0;$i<5;$i++){
            echo "<tr>";
            if($i == 0 || $i == 1 || $i == 2 ){
                echo "<td style='color:#FF3333;'><img src='images/NO".($i+1).".png' width='35px' height='45px'>NO.".($i+1)."&ensp;</td>";
            }else{
                echo "<td>&ensp;NO.".($i+1)."&ensp;</td>";
            }
            echo "<td class='data'>".$row_1[$i]['pi_name']."&ensp;(".$row_1[$i]['total_heart_amount']."顆愛心)</td>";
            echo "<td class='data'>".$row_2[$i]['pi_name']."&ensp;(".$row_2[$i]['total_star_amount']."顆星星)</td>";
            echo "<td class='data'>".$row_3[$i]['pi_name']."&ensp;(第".$row_3[$i]['level_num']."層)</td>";
            echo "<td class='data'>".$row_4[$i]['pi_name']."&ensp;(".$row_4[$i]['total_card_amount']."張字卡)</td>";
            echo "<td class='data'>".$row_5[$i]['pi_name']."&ensp;(".$row_5[$i]['experience_amount'].")</td>";
            echo "</tr>";
        }
    ?>
    </table>

    </div>
	
</body>
</html>