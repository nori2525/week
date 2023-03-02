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


if(!empty($_POST)){
    $_POST = es($_POST);
    
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s');
    $image_dir = 'image/';
    $icon_name = basename(es($user['icon']));
    $pic1_name = basename(es($pic['pic1']));
    $pic2_name = basename(es($pic['pic2']));
    $pic3_name = basename(es($pic['pic3']));
    $pic4_name = basename(es($pic['pic4']));
    $pic5_name = basename(es($pic['pic5']));
    $pic6_name = basename(es($pic['pic6']));
    $pic7_name = basename(es($pic['pic7']));

    if( !empty($_FILES['icon']['tmp_name']) && is_uploaded_file($_FILES['icon']['tmp_name'])){
        $icon_info = pathinfo($_FILES['icon']['name']);
        $icon_name = $userId . '_icon.' . $icon_info['extension'];
        move_uploaded_file($_FILES['icon']['tmp_name'], $image_dir . $icon_name);
    }
    if( !empty($_FILES['pic1']['tmp_name']) && is_uploaded_file($_FILES['pic1']['tmp_name']) ) {
        $pic1_info = pathinfo($_FILES['pic1']['name']);
        $pic1_name = $userId . '_pic1.' . $pic1_info['extension'];
        move_uploaded_file($_FILES['pic1']['tmp_name'], $image_dir . $pic1_name);
    }
    if( !empty($_FILES['pic2']['tmp_name']) && is_uploaded_file($_FILES['pic2']['tmp_name']) ) {
        $pic2_info = pathinfo($_FILES['pic2']['name']);
        $pic2_name = $userId . '_pic2.' . $pic2_info['extension'];
        move_uploaded_file($_FILES['pic2']['tmp_name'], $image_dir . $pic2_name);
    }
    if( !empty($_FILES['pic3']['tmp_name']) && is_uploaded_file($_FILES['pic3']['tmp_name']) ) {
        $pic3_info = pathinfo($_FILES['pic3']['name']);
        $pic3_name = $userId . '_pic3.' . $pic3_info['extension'];
        move_uploaded_file($_FILES['pic3']['tmp_name'], $image_dir . $pic3_name);
    }
    if( !empty($_FILES['pic4']['tmp_name']) && is_uploaded_file($_FILES['pic4']['tmp_name']) ) {
        $pic4_info = pathinfo($_FILES['pic4']['name']);
        $pic4_name = $userId . '_pic4.' . $pic4_info['extension'];
        move_uploaded_file($_FILES['pic4']['tmp_name'], $image_dir . $pic4_name);
    }
    if( !empty($_FILES['pic5']['tmp_name']) && is_uploaded_file($_FILES['pic5']['tmp_name']) ) {
        $pic5_info = pathinfo($_FILES['pic5']['name']);
        $pic5_name = $userId . '_pic5.' . $pic5_info['extension'];
        move_uploaded_file($_FILES['pic5']['tmp_name'], $image_dir . $pic5_name);
    }
    if( !empty($_FILES['pic6']['tmp_name']) && is_uploaded_file($_FILES['pic6']['tmp_name']) ) {
        $pic6_info = pathinfo($_FILES['pic6']['name']);
        $pic6_name = $userId . '_pic6.' . $pic6_info['extension'];
        move_uploaded_file($_FILES['pic6']['tmp_name'], $image_dir . $pic6_name);
    }
    if( !empty($_FILES['pic7']['tmp_name']) && is_uploaded_file($_FILES['pic7']['tmp_name']) ) {
        $pic7_info = pathinfo($_FILES['pic7']['name']);
        $pic7_name = $userId . '_pic7.' . $pic7_info['extension'];
        move_uploaded_file($_FILES['pic7']['tmp_name'], $image_dir . $pic7_name);
    }
    
    $sql="UPDATE user SET name = :name, icon = :icon, text = :comm, favo = :favo, up_date = :dt WHERE userId = :id";
    $stm=$pdo->prepare($sql);
    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
    $stm->bindvalue(':name',$_POST['name'],PDO::PARAM_STR);
    $stm->bindvalue(':icon',$image_dir . $icon_name,PDO::PARAM_STR);
    $stm->bindvalue(':comm',$_POST['comm'],PDO::PARAM_STR);
    $stm->bindvalue(':favo',$_POST['favo'],PDO::PARAM_STR);
    $stm->bindValue(':dt', $date, PDO::PARAM_STR);
    $stm->execute();

    $sql="UPDATE picture SET pic1 = :pic1, pic2 = :pic2, pic3 = :pic3, pic4 = :pic4, pic5 = :pic5, pic6 = :pic6, pic7 = :pic7 WHERE userId = :id";
    $stm=$pdo->prepare($sql);
    $stm->bindValue(':id', $userId, PDO::PARAM_STR);
    $stm->bindvalue(':pic1',$image_dir . $pic1_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic2',$image_dir . $pic2_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic3',$image_dir . $pic3_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic4',$image_dir . $pic4_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic5',$image_dir . $pic5_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic6',$image_dir . $pic6_name,PDO::PARAM_STR);
    $stm->bindvalue(':pic7',$image_dir . $pic7_name,PDO::PARAM_STR);
    $stm->execute();

    header("Location: week_pro.php");
    exit;
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
        <script type="text/javascript" src="js/week_pre.js">
        </script>
    </head>
    <body class="container">
        <header>
            <div class="fixed-top">
                <a href="week_pro.php" style = "color: white;margin-left: 20px;">&#9665;</a>
                <a href="week_top.php" class="logo">Week</a>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <p class="subtitle">プロフィール編集</p>
                <div class="column1">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <img id="preview"><br>
                                <input type="file" name="icon" accept="image/jpeg, image/png" value="<?php echo $user['icon']; ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="name" value="<?php echo $user['name']; ?>"><br>
                                <input type="text" name="favo" placeholder="お気に入りブランド" value="<?php echo $user['favo']; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="file" name="pic1" accept="image/jpeg, image/png">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="pic2" accept="image/jpeg, image/png">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="pic3" accept="image/jpeg, image/png">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="pic4" accept="image/jpeg, image/png">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="file" name="pic5" accept="image/jpeg, image/png">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="pic6" accept="image/jpeg, image/png">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="pic7" accept="image/jpeg, image/png">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <textarea name="comm" cols="50" rows="5" placeholder="自己紹介文"><?php echo $user['text']; ?></textarea>
                            </div>
                        </div>
                        <input type="submit" value="Save">
                    </form>
                </div>
            </div>
        </div>
        <footer>
            <hr sizer="2" color="skyblue">
            <p>2020 情報システム工学実験Ⅱ　「Week」</p>
        </footer>
    </body>
</html>