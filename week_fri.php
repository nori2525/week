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

$sql = "select friends.userId, friends.toUserId, friends.status, user.name from friends join user on friends.userId = user.userId where friends.userId = :id and status = 2";
$stm = $pdo->prepare($sql);
$stm->bindValue(':id', $userId, PDO::PARAM_STR);
$stm->execute();
$sent = $stm->fetchAll(PDO::FETCH_ASSOC);

$sql = "select friends.userId, friends.toUserId, friends.status, user.name from friends join user on friends.userId = user.userId where friends.toUserId = :tid and status = 2";
$stm = $pdo->prepare($sql);
$stm->bindValue(':tid', $userId, PDO::PARAM_STR);
$stm->execute();
$come = $stm->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="week_top.php" class="logo">Week</a>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <div class="row">
                    <div class="col-md-6">
                        <p class="subtitle">あなたが申請したリスト</p>
                        <div class="column1">
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $a = 0;
                                    foreach ($sent as $row){
                                        echo '<form method="POST" name="form_sent' . $a . '"action="week_pro.php">';
                                        echo '<li class="inlink">';            
                                        echo '<input type="hidden" name="userId" value="'. $row['toUserId'] . '">';
                                        echo '<a href="javascript:form_sent' . $a . '.submit()"></a>';
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
                        <p class="subtitle">あなたへの申請リスト</p>
                        <div class="column1">
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    $b = 0;
                                    foreach ($come as $row){
                                        echo '<form method="POST" name="form_come'. $b . '"action="week_pro.php">';
                                        echo '<li class="inlink">';            
                                        echo '<input type="hidden" name="userId" value="'. $row['userId'] . '">';
                                        echo '<a href="javascript:form_come' . $b . '.submit()"></a>';
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
                </div>
            </div>
        </div>
        <footer>
            <hr sizer="2" color="skyblue">
            <p>2020 情報システム工学実験Ⅱ　「Week」</p>
        </footer>
    </body>
</html>