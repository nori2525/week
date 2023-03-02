<?php
session_start();
require_once("data/week_util.php");
$userId = es($_SESSION['userId']);
require_once("data/week_dbinfo.php");
    
try{
    $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
}catch(Exception $e){
    echo '<span clsss="error">データベースエラー</span>';
    echo $e->getMessage();
}

$_POST = es($_POST);
if(isset($_POST['bname']) && $_POST['bname'] !== ""){
    $sql = "select * from brand where name = :name";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':name', $_POST['bname'], PDO::PARAM_STR);
    $stm->execute();
    $br = $stm->fetch(PDO::FETCH_ASSOC);
    if($br == false){
        $sql = "insert into form(userId, text) values(default, :text)";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':text', $_POST['bname'] . "　：トークルーム作成依頼", PDO::PARAM_STR);
        $stm->execute();
        $error="作成依頼を送信しました。";
    }else{
        $sql = "select * from talk where name = :name AND status = 1";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':name', $_POST['bname'], PDO::PARAM_STR);
        $stm->execute();
        $br_ac = $stm->fetch(PDO::FETCH_ASSOC);
        if($br_ac == false){
            $sql = "insert into talk(userId, toId, name, date, status) values(default, NULL, :name, now(), 1)";
            $stm = $pdo->prepare($sql);
            $stm->bindValue(':name', $_POST['bname'], PDO::PARAM_STR);
            $stm->execute();
            $error="作成されました。";
        }else{
            $error = "既に存在しています";
        }
    }
}

if(isset($_POST['come'])){
    $sql = "update friends set status = 1 where userId = :tid and toUserId = :id";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':tid', $_POST['id'], PDO::PARAM_STR);
    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
    $stm->execute();
    $sql = "insert into friends(userId, toUserId, status) values (:id, :tid, 1)";
    $stm = $pdo->prepare($sql);
    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
    $stm->bindValue(':tid', $_POST['id'], PDO::PARAM_STR);
    $stm->execute();
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
        <script type="text/javascript" src="js/bootstrap.js">
        </script>
    </head>
    <body class="container">
        <header>
            <div class="fixed-top">
                <a href="week_top.php" class="logo">Week</a>
                <div class="gnav">
                    <ul>
                        <li>
                            <form method="POST" name="form5" action="week_pro.php">
                                <input type="hidden" name="userId" value="<?php echo $userId;?>">
                                <a href="javascript:form5.submit()">Profile</a>
                            </form>
                        </li>
                        <li><a href="week_sear.php">Search</a></li>
                    </ul>
                </div>
                <div>
                    </header>
                    <div id="wrap">
                        <div class="content">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="column1">
                                        <p class="subtitle">Friends</p>
                                        <div class="menu">
                                            <ul class="list">
                                            <?php
                                            $sql = "select * from friends join user on friends.toUserId = user.userId where friends.userId = :id and status = 1";
                                            $stm = $pdo->prepare($sql);
                                            $stm->bindValue(':id', $userId, PDO::PARAM_STR);
                                            $stm->execute();
                                            $friend = $stm->fetchAll(PDO::FETCH_ASSOC);
                                            $a = 0;
                                            foreach ($friend as $row){
                                                echo '<form method="POST" name="form_fri' . $a . '" action="week_pro.php">';
                                                echo '<li class="inlink">';
                                                
                                                echo '<input type="hidden" name="userId" value="'. $row['toUserId'] . '">';
                                                echo '<a href="javascript:form_fri' . $a . '.submit()"></a>';
                                                echo '<p style="font-size: larger">' . $row['name'] . '</p>';
                                                echo '<p style="color: gray">' . $row['toUserId'] . '</p>';
                                                echo '</form>';
                                                echo '</li>';
                                                $a++;
                                            }
                                            ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="column1">
                            <p class="subtitle">Favorite Users</p>
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $sql = "select * from favorite join user on favorite.toId = user.userId where favorite.userId = :id";
                                    $stm = $pdo->prepare($sql);
                                    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
                                    $stm->execute();
                                    $fav_user = $stm->fetchAll(PDO::FETCH_ASSOC);
                                    $i = 0;
                                    foreach ($fav_user as $row){
                                        echo '<form method="POST" name="form_fav' .$i  . '" action="week_pro.php">';
                                        echo '<li class="inlink">';
                                        echo '<input type="hidden" name="userId" value="' . $row['toId'] . '">';
                                        echo '<a href="javascript:form_fav' . $i . '.submit()"></a>';
                                        echo '<p style="font-size: larger">' . $row['name'] . '</p>';
                                        echo '<p style="color: gray">' . $row['toId'] . '</p>';
                                        echo '</form>';
                                        echo '</li>';
                                        $i++;
                                    }
                                    ?>    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="column1">
                            <p class="subtitle">Favorite Talks</p>
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $sql = "SELECT fav_talk.userId, fav_talk.num, talk.userId, talk.name FROM fav_talk JOIN talk ON fav_talk.num = talk.number WHERE fav_talk.userId = :id";
                                    $stm = $pdo->prepare($sql);
                                    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
                                    $stm->execute();
                                    $fav_talk = $stm->fetchAll(PDO::FETCH_ASSOC);
                                    $c = 0;
                                    foreach ($fav_talk as $row){
                                        echo '<form method="POST" name="form_talk' .$c  . '" action="week_talk.php">';
                                        echo '<li class="inlink">';
                                        echo '<input type="hidden" name="num" value="' . es($row['num']) . '">';
                                        echo '<a href="javascript:form_talk' . $c . '.submit()"></a>';
                                        echo '<p style="font-size: larger">' . es($row['name']) . '</p>';
                                        echo '<p style="color: gray">' . es($row['userId']) . '</p>';
                                        echo '</li>';
                                        echo '</form>';
                                        $c++;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="column1">
                            <p class="subtitle">Popular Users</p>
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $sql = "SELECT userId, name, icon, star FROM user WHERE userId != :id ORDER BY star DESC";
                                    $stm = $pdo->prepare($sql);
                                    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
                                    $stm->execute();
                                    $pop = $stm->fetchAll(PDO::FETCH_ASSOC);
                                    $b = 0;
                                    foreach ($pop as $row){
                                        echo '<form method="POST" name="form_pop' . $b  . '" action="week_pro.php">';
                                        echo '<li class="inlink">';
                                        echo '<input type="hidden" name="userId" value="' . $row['userId'] . '">';
                                        echo '<a href="javascript:form_pop' . $b . '.submit()"></a>';
                                        echo '<p style="font-size: larger">' . $row['name'] . '</p>';
                                        echo '<p style="color: gray">' . $row['userId'] . '</p>';
                                        echo '</form>';
                                        echo '</li>';
                                        $b++;
                                    }
                                    ?>     
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="column1">
                        <p class="subtitle">Friend Request</p>
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $sql = "select friends.userId, friends.toUserId, friends.status, user.name from friends join user on friends.userId = user.userId where friends.toUserId = :tid and status = 2";
                                    $stm = $pdo->prepare($sql);
                                    $stm->bindValue(':tid', $userId, PDO::PARAM_STR);
                                    $stm->execute();
                                    $come = $stm->fetchAll(PDO::FETCH_ASSOC);
                                    $o = 0;
                                    foreach ($come as $row){
                                        echo '<li class="inlink" style="width:74%;float:left;">';            
                                        echo '<form method="POST" name="form_come'. $o . '"action="week_pro.php">';
                                        echo '<input type="hidden" name="userId" value="'. $row['userId'] . '">';
                                        echo '<a href="javascript:form_come' . $o . '.submit()"></a>';
                                        echo '<p style="font-size: larger; text-align:left; padding-left:10%">' . $row['name'] . '</p>';
                                        echo '<p style="color: gray; text-align:left;padding-left:10%">' . $row['userId'] . '</p>';
                                        echo '</form>';
                                        echo '</li>';
                                        echo '<form method="POST">';
                                        echo '<input type="hidden" name="id" value="' . $row['userId'] . '">';
                                        echo '<button type="submit" name="come" class="aut"><p>承認</p></button>';
                                        echo '</form>';
                                        $o++;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <footer>
            <hr sizer="2" color="skyblue">
            <p>2020 情報システム工学実験Ⅱ　「Week」</p>
            <button class="btn-form btn">ブランドスレッド作成依頼</button>
            <p><?php echo $error; ?></p>
            <div class="hid is-hidden">
                <form method="POST">
                    <ul style="list-style:none;">
                        <li><input type="text" name="bname" placeholder="ブランド名"></li>
                        <li><input type="submit" value="送信" class="btn"></li>
                    </ul>
                </div>
            </div>
            <script src="js/index.js"></script>
        </footer>
    </body>
</html>