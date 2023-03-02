<?php
require_once("data/week_util.php");
$_POST = es($_POST);

require_once("data/week_dbinfo.php");

$date = new DateTime();
$date = $date->format('Y-m-d H:i:s');

try{
    $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
    echo '<span clsss="error">データベースエラー</span>';
    echo $e->getMessage();
}

$sql="select count(mail) from users where mail=:email";
$stmt=$pdo->prepare($sql);
$stmt->bindvalue(':email',$_POST['e-mail'],PDO::PARAM_STR);
$stmt->execute();
$count=$stmt->fetch(PDO::FETCH_ASSOC);

$sql="select count(userId) from users where userId=:id";
$stmt1=$pdo->prepare($sql);
$stmt1->bindvalue(':id',$_POST['id'],PDO::PARAM_STR);
$stmt1->execute();
$result=$stmt1->fetch(PDO::FETCH_ASSOC);
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
                <a href="week_start.php" class="logo">Week</a>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="column1">
                        <?php
                        if(!isset($_POST["id"]) || ($_POST["id"] === "")){
                            $errors[] = "ユーザーIDを入力してください";
                        }
                        if(!isset($_POST["name"]) || ($_POST["name"] === "")){
                            $errors[] = "ユーザー名を入力してください";
                        }
                        if(!isset($_POST["e-mail"]) || ($_POST["e-mail"] === "")){
                            $errors[] = "メールアドレスを入力してください";
                        }
                        if(!filter_var($_POST['e-mail'], FILTER_VALIDATE_EMAIL)){
                            $errors[] = '正しくないメールアドレスです';
                        }
                        if(!isset($_POST["pass"]) || ($_POST["pass"] === "")){
                            $errors[] = "パスワードを入力してください";
                        }
                        if(mb_strlen($_POST['pass']) < 6){
                            $errors[] = "パスワードが短すぎます";
                        }
                        if(!isset($_POST["repass"]) || ($_POST["repass"] === "")){
                            $errors[] = "確認用パスワードを入力してください";
                        }
                        if($_POST["pass"] !== $_POST["repass"] && ($_POST["repass"] !== "")){
                            $errors[] = "パスワードが一致しません";
                        }
                        if($count['count(mail)']>0 && ($_POST["e-mail"] != "")){ 
                            $errors[] = "既に登録されたメールアドレスです";
                        }
                        if($result['count(userId)']>0 && ($_POST["id"] != "")){ 
                            $errors[] = "既に登録されたユーザーIDです";
                        }
                        if(count($errors) > 0){
                            echo '<ol class="error">';
                            foreach($errors as $value){
                                echo "<li>", $value, "</li>";
                            }
                            echo "</ol>";
                            echo '<a href="week_regis.php">戻る</a>';
                            exit();
                        }else{
                            $sql="INSERT INTO talk (userId, name, date, status) VALUES(:id, :name, :dt, 2)";
                            $stm=$pdo->prepare($sql);
                            $stm->bindValue(':id', $_POST['id'], PDO::PARAM_STR);
                            $stm->bindvalue(':name',$_POST['name'],PDO::PARAM_STR);
                            $stm->bindValue(':dt', $date, PDO::PARAM_STR);
                            $stm->execute();

                            $sql = "INSERT INTO users (userId, mail, pass, sex, date, status) VALUES (:id, :email, MD5(:pass), :sex, :dt, default)";
                            $stm3 = $pdo->prepare($sql);
                            $stm3->bindValue(':id', $_POST['id'], PDO::PARAM_STR);
                            $stm3->bindValue(':email', $_POST['e-mail'], PDO::PARAM_STR);
                            $stm3->bindValue(':pass', $_POST['pass'], PDO::PARAM_STR);
                            $stm3->bindValue(':sex', $_POST['sex'], PDO::PARAM_STR);
                            $stm3->bindValue(':dt', $date, PDO::PARAM_STR);
                            $stm3->execute();

                            $sql="INSERT INTO user (userId, name, icon, text, favo, up_date, star) VALUES(:id, :name, default, '', '', :dt, default)";
                            $stm4=$pdo->prepare($sql);
                            $stm4->bindValue(':id', $_POST['id'], PDO::PARAM_STR);
                            $stm4->bindvalue(':name',$_POST['name'],PDO::PARAM_STR);
                            $stm4->bindValue(':dt', $date, PDO::PARAM_STR);
                            $stm4->execute();
                            
                            $sql="INSERT INTO picture (userId, pic1, pic2, pic3, pic4, pic5, pic6, pic7) VALUES(:id, default, default, default, default, default, default, default)";
                            $stm5=$pdo->prepare($sql);
                            $stm5->bindValue(':id', $_POST['id'], PDO::PARAM_STR);
                            $stm5->execute();

                            echo '<p class="subtitle">登録が完了しました。</p>';
                            echo '<a href="week_start.php">ログインページへ</a>';
                        }
                        ?>
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