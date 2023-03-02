<?php
if(isset($_POST['id']) && isset($_POST['pass'])){

    session_start();
    require_once("data/week_util.php");
    $_POST = es($_POST);
    
    require_once("data/week_dbinfo.php");
    
    try{
        $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT *, count(*) FROM users WHERE userId = :id AND pass = MD5(:pass)";
        $stm = $pdo->prepare($sql);
        $stm->bindValue(':id', $_POST['id'], PDO::PARAM_STR);
        $stm->bindValue(':pass', $_POST['pass'], PDO::PARAM_STR);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        if($result['count(*)'] > 0){
            $_SESSION['userId'] = $result['userId'];
            header("Location: week_top.php");
            exit;
        }else{
            $error = "ログインエラー";
        }
    }catch(Exception $e){
        echo '<span clsss="error">データベースエラー</span>';
        echo $e->getMessage();
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
        <script type="text/javascript" src="js/bootstrap.min.js">
        </script>
    </head>
    <body class="backg">
        <div class="box">
            <div class="comm">
                <p class="title">Week<p>
                <p>自分の好きな服装の相手とつながる</p>
            </div>
            <div class="column3">
                <form method="POST">
                    <ul class="login">
                        <li><?php echo $error; ?></li>
                        <li><input type="text" name="id" placeholder="ユーザーID"></li>
                        <li><input type="password" name="pass" placeholder="パスワード"></li>
                        <li><input type="submit" value="ログイン" class="btn"></li>
                    </ul>
                </form>
                <a href="week_regis.php">新規会員登録はこちら</a>
            </div>
        </div>
    </body>
</html>


