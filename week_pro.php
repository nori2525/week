<?php
require_once("data/week_util.php");
session_start();
if(!isset($_POST['userId'])){
    $userId = $_SESSION['userId'];
}else{
    $userId = es($_POST['userId']);

}

$del = 0;
$ins = 0;

require_once("data/week_dbinfo.php");
try{
    $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
    echo '<span clsss="error">データベースエラー</span>';
    echo $e->getMessage();
}

$sql="SELECT * FROM user WHERE userId = :id";
$stmt=$pdo->prepare($sql);
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$sql="SELECT * FROM picture WHERE userId = :id";
$stmt=$pdo->prepare($sql);
$stmt->bindValue(':id', $userId, PDO::PARAM_STR);
$stmt->execute();
$pic = $stmt->fetch(PDO::FETCH_ASSOC);

$sql_fav="SELECT * FROM favorite WHERE userId = :id AND toId = :tid";
$stm_f=$pdo->prepare($sql_fav);
$stm_f->bindValue(':id', es($_SESSION['userId']), PDO::PARAM_STR);
$stm_f->bindValue(':tid', $userId, PDO::PARAM_STR);
$stm_f->execute();
$fav = $stm_f->fetch(PDO::FETCH_ASSOC);

$sql_fri="SELECT * FROM friends WHERE userId = :id AND toUserId = :tid";
$stm_fr=$pdo->prepare($sql_fri);
$stm_fr->bindValue(':id', es($_SESSION['userId']), PDO::PARAM_STR);
$stm_fr->bindValue(':tid', $userId, PDO::PARAM_STR);
$stm_fr->execute();
$fri = $stm_fr->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['fav'])){
    if($fav == false){
        $sql="INSERT INTO favorite (userId, toId) VALUES (:id, :tid)";
        $stm=$pdo->prepare($sql);
        $stm->bindValue(':id', es($_SESSION['userId']), PDO::PARAM_STR);
        $stm->bindValue(':tid', $userId, PDO::PARAM_STR);
        $stm->execute();
        $del = 1;
        $sql="UPDATE user SET star = :star WHERE userId = :id";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':star', es($user['star'])+1, PDO::PARAM_INT);
        $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
        $stmt->execute();
    }else{
        $sql="DELETE FROM favorite WHERE userId = :id AND toId = :tid";
        $stm=$pdo->prepare($sql);
        $stm->bindValue(':id', es($_SESSION['userId']), PDO::PARAM_STR);
        $stm->bindValue(':tid', $userId, PDO::PARAM_STR);
        $stm->execute();
        $del = 2;
        $sql="UPDATE user SET star = :star WHERE userId = :id";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':star', es($user['star'])-1, PDO::PARAM_INT);
        $stmt->bindValue(':id', $userId, PDO::PARAM_STR);
        $stmt->execute();
    }
}else if(isset($_POST['fri'])){
    if($fri == false){
        $sql="INSERT INTO friends (userId, toUserId, status) VALUES (:id, :tid, default)";
        $stm=$pdo->prepare($sql);
        $stm->bindValue(':id', es($_SESSION['userId']), PDO::PARAM_STR);
        $stm->bindValue(':tid', $userId, PDO::PARAM_STR);
        $stm->execute();
        $ins = 1;
    }
}

if(isset($_POST['reason']) && $_POST['reason'] !== ""){
    $sql = "insert into form(userId, text) values(default, :text)";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':text', es($_POST['userId']) . "：" . es($_POST['reason']), PDO::PARAM_STR);
    $stm->execute();
    $error="通報しました。";
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
        <script type="text/javascript" src="js/bootstrap.min.js">
        </script>
    </head>
    <body class="container">
        <header>
            <div class="fixed-top">
                <a href="week_top.php" style = "color: white;margin-left: 20px;">&#9665;</a>
                <a href="week_top.php" class="logo">Week</a>
                <div class="gnav">
                    <ul>
                        <?php
                            if($userId === $_SESSION['userId']){
                                echo '<li></li>';
                                echo "<li><a href='week_pro_edit.php'>Edit</a></li>";
                            }
                        ?>
                    </ul>
                </div>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <p class="subtitle"><?php echo $userId?></p>
                <div class="pronav">
                    <?php
                    if($userId !== $_SESSION['userId']){
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="userId" value="' . $userId . '">';
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
                        echo '</li>';
                        if($fri == false){
                            if($ins==1){
                                echo '<li><p class="wait">承認待ち</p></li></form>';
                            }else{
                                echo '<li><button type="submit" name="fri" class="friend">友達申請</button></li></form>';
                            }
                        }else if($fri['status'] == 2){
                            echo '<li><p class="wait">承認待ち</p></li></form>';
                        }else if($fri['status'] == 1){
                            echo '</form>';
                            $sql_dm="SELECT * FROM talk WHERE userId = :id1 AND toId = :tid1 OR userId = :tid2 AND toId = :id2";
                            $stm_dm=$pdo->prepare($sql_dm);
                            $stm_dm->bindValue(':id1', $userId, PDO::PARAM_STR);
                            $stm_dm->bindValue(':tid1', es($_SESSION['userId']), PDO::PARAM_STR);
                            $stm_dm->bindValue(':id2', $userId, PDO::PARAM_STR);
                            $stm_dm->bindValue(':tid2', es($_SESSION['userId']), PDO::PARAM_STR);
                            $stm_dm->execute();
                            $dm = $stm_dm->fetch(PDO::FETCH_ASSOC);
                            echo '<li><form method="POST" action="week_talk.php">';
                            echo '<input type="hidden" name="num" value="'. es($dm['number']) . '">';
                            echo '<button type="submit"class="dm"><p>DM</p></button>';
                            echo '</form></li>';
                        }else{
                            echo '</form>';
                        }
                    }
                    ?>
                </div>
                <div class="column1">
                    <div class="row" style="clear: both;">
                        <div class="col-md-6">
                            <img src="<?php echo $user['icon'];?>" class="icon">
                        </div>
                        <div class="col-md-6, pro">
                            <p>名前<br><span>　<?php echo $user['name']; ?>　</span></p>
                            <p>お気に入りブランド<br><span>　<?php echo $user['favo']; ?>　</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic1'];?>" class="pic">
                        </div>
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic2'];?>" class="pic">
                        </div>
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic3'];?>" class="pic">
                        </div>
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic4'];?>" class="pic">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic5'];?>" class="pic">
                        </div>
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic6'];?>" class="pic">
                        </div>
                        <div class="col-md-3">
                            <img src="<?php echo $pic['pic7'];?>" class="pic">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="txt">
                                <p><?php echo $user['text']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <hr sizer="2" color="skyblue">
            <p>2020 情報システム工学実験Ⅱ　「Week」</p>
            <?php
            if($userId !== $_SESSION['userId']){
                echo '<button class="btn-form btn">ユーザーの通報</button>';
                echo '<p>' . $error . '</p>';
                echo '<div class="hid is-hidden">';
                echo '<form method="POST">';
                echo '<ul style="list-style:none;">';
                echo '<input type="hidden" name="userId" value="' . $userId . '">';
                echo '<li><textarea name="reason" cols="30" rows="5" placeholder="理由"></textarea></li>';
                echo '<li><input type="submit" value="送信" class="btn"></li>';
                echo '</ul>';
                echo '</form>';
                echo '<script src="js/index.js"></script>';
            }
            ?>
        </footer>
    </body>
</html>