<?php
session_start();
require_once("data/week_util.php");
$userId = es($_SESSION['userId']);
$num = es($_POST['num']);
require_once("data/week_dbinfo.php");

try{
    $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
}catch(Exception $e){
    echo '<span clsss="error">データベースエラー</span>';
    echo $e->getMessage();
}

if(isset($_POST['message']) && $_POST['message'] !== ''){
    $message = es($_POST['message']);
    $sql = "insert into comment (number, userId, message, date) values (:num, :id, :mes, now())";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':num', $num, PDO::PARAM_INT);
    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
    $stm->bindValue(':mes', $message, PDO::PARAM_STR);
    $stm->execute();
    $com = $stm->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT * FROM talk WHERE number = :num";
$stm = $pdo->prepare($sql);
$stm->bindValue(':num', $num, PDO::PARAM_INT);
$stm->execute();
$talk = $stm->fetch(PDO::FETCH_ASSOC);

$sql = "select comment.userId, comment.message, comment.date, user.name, user.icon from comment join user on comment.userId = user.userId where comment.number=:num";
$stm = $pdo->prepare($sql);
$stm->bindValue(':num', $num, PDO::PARAM_INT);
$stm->execute();
$com = $stm->fetchAll(PDO::FETCH_ASSOC);

$del=0;
$sql = "SELECT * FROM fav_talk WHERE userId = :id";
$stm = $pdo->prepare($sql);
$stm->bindValue(':id', $userId, PDO::PARAM_INT);
$stm->execute();
$fav = $stm->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['fav'])){
    if($fav == false){
        $sql="INSERT INTO fav_talk (userId, num) VALUES (:id, :num)";
        $stm=$pdo->prepare($sql);
        $stm->bindValue(':id', $userId, PDO::PARAM_STR);
        $stm->bindValue(':num', $num, PDO::PARAM_INT);
        $stm->execute();
        $del = 1;
    }else{
        $sql="DELETE FROM fav_talk WHERE userId = :id AND num = :num";
        $stm=$pdo->prepare($sql);
        $stm->bindValue(':id', $userId, PDO::PARAM_STR);
        $stm->bindValue(':num', $num, PDO::PARAM_INT);
        $stm->execute();
        $del = 2;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Week</title>
        <link rel="stylesheet" href="css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">  
        <script type="text/javascript" src="js/jquery-3.5.0.min.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js">
        </script>
    </head>
    <body class="container">
        <header>
            <div class="fixed-top">
                <a href="week_top.php" class="logo">Week</a>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if($talk['status'] == 2 ||$talk['status'] == 1){
                            echo '<div class="talknav"><form method="POST"><ul>';
                            echo '<li><p class="subtitle">' . es($talk['name']). '</p></li>';
                            echo '<input type="hidden" name="num" value="' . $num . '">';
                            echo '<li>';
                            if($fav == false){
                                if($del == 1){
                                    echo '<button type="submit" name="fav" style="color: yellow" class="star"><p>★</p></button>';
                                }else{
                                    echo '<button type="submit" name="fav" class="star"><p>★</p></button>';
                                }
                            }else{
                                if($del == 2){
                                    echo '<button type="submit" name="fav" class="star"><p>★</p></button>';
                                }else{
                                    echo '<button type="submit" name="fav" style="color: yellow" class="star"><p>★</p></button>';
                                }
                            }
                            echo '</ul></li></form></div>';
                        }else{
                            if($talk['userId'] === $userId){
                                echo '<p class="subtitle">' . $talk['toId']. '</p>';
                            }else if($talk['toId'] === $userId){
                                echo '<p class="subtitle">' . $talk['userId']. '</p>';
                            }
                        }
                        ?>
                        <div class="column1" style="clear: both;">
                            <div class="column2">
                                <div class="menu" id="scroll">
                                    <ul class="list">
                                        <?php
                                        if($talk['status'] == 1){
                                            foreach($com as $row){
                                                echo '<li style="padding:2%">';
                                                echo '<div style="float:left;width10%"><img src="' . es($row['icon']) . '" class="icon" style="width:80px;height:80px;">';
                                                echo '<p>' . es($row['name']).'</p></div>';
                                                echo '<div style="width:90%"><p style="text-align:left;padding-left:30%;font-size:120%;">' . $row['message'] . '</p>';
                                                echo '<p style="color: lightgray;text-align: right;">' . es($row['date']) . '</p></div>';
                                                echo '</li>';
                                            }
                                        }else if($talk['status'] == 2){
                                            foreach($com as $row){
                                                if($talk['userId'] === $row['userId']){
                                                    echo '<li style="clear:both;padding:2% 2% 0 2%; width:80%;float:left;">';
                                                    echo '<div style="float:right;width10%"><img src="' . es($row['icon']) . '" class="icon" style="width:80px;height:80px;">';
                                                    echo '<p>' . es($row['name']).'</p></div>';
                                                    echo '<div style="width:90%"><p style="text-align:center;padding-left:20%;font-size:120%;">' . $row['message'] . '</p>';
                                                    echo '<p style="color: lightgray;text-align: left;">' . es($row['date']) . '</p></div>';
                                                    echo '</li>';
                                                }else{
                                                    echo '<li style="clear:both;padding: 2% 0 0 2%;width:80%;float:right;">';
                                                    echo '<div style="float:left;width10%;"><img src="' . es($row['icon']) . '" class="icon" style="width:80px;height:80px;">';
                                                    echo '<p>' . es($row['name']).'</p></div>';
                                                    echo '<div style="width:90%"><p style="text-align:center;padding-right:20%;font-size:120%;">' . $row['message'] . '</p>';
                                                    echo '<p style="color: lightgray;text-align: right;">' . es($row['date']) . '</p></div>';
                                                    echo '</li>';
                                                }
                                            }
                                        }else{
                                            foreach($com as $row){
                                            
                                                if((es($row['userId']) === $userId)){
                                                    echo '<div style="clear:both;float:right;width10%;padding-right:2%;margin-top:2%"><img src="' . $row['icon'] . '" class="icon" style="width:80px;height:80px;">';
                                                    echo '<p>' . es($row['name']).'</p></div>';
                                                    echo '<li style="clear:both;width:80%;float:right;margin:0">';
                                                    echo '<div style="width:100%;padding-right:2%"><p style="font-size:120%">' . es($row['message']) . '</p>';
                                                    echo '<p style="color: lightgray;text-align: right;">' . es($row['date']) . '</p></div>';
                                                    echo '</li>';
                                                }else{
                                                    echo '<div style="clear:both;float:left;width10%;padding-left:2%;margin-top:2%"><img src="' . es($row['icon']) . '" class="icon" style="width:80px;height:80px;">';
                                                    echo '<p>' . es($row['name']).'</p></div>';
                                                    echo '<li style="clear:both;width:80%;float:left;margin:0">';
                                                    echo '<div style="width:100%;padding-left:2%"><p style="font-size:120%;">' . es($row['message']) . '</p>';
                                                    echo '<p style="color: lightgray;text-align: left;">' . es($row['date']) . '</p></div>';
                                                    echo '</li>';
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <form method="POST">
                                <input type="text" name="message" class="search" style="width:80%;">
                                <input type="hidden" name = "num" value= "<?php echo $num;?>">
                                <input type="submit" value="Send" class="sub">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <hr sizer="2" color="skyblue">
            <p>2020 情報システム工学実験Ⅱ　「Week」</p>
        </footer>
    </body>
</html>