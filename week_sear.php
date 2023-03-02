<?php
require_once("data/week_util.php");
$_POST = es($_POST);
$sear = $_POST['sear'];
$fav = 0;
if(isset($_POST['fav'])){
    $fav = $_POST['fav'];
}
require_once("data/week_dbinfo.php");
    
try{
    $pdo = new PDO("mysql:host={$SERV};dbname={$DBNM}", $USER, $PASS);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
}catch(Exception $e){
    echo '<span clsss="error">データベースエラー</span>';
    echo $e->getMessage();
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
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST">
                            <div class="sear">
                                <label><input type="checkbox" name="fav" value="1">お気に入りブランド名から検索</label>
                                <input type="text" name="sear" placeholder="ユーザー名・ブランド名でトーク検索" class="search" value="<?php echo $_POST['sear'];?>">
                                <input type="submit" value="Search" class="sub">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="column2">
                            <p class="subtitle">Result</p>
                            <div class="menu">
                                <ul class="list">
                                    <?php
                                    if($fav == 0){
                                        if(!isset($_POST["sear"]) || ($_POST["sear"] === "")){
                                            echo "<li>検索ワードを入力してください</li>";
                                            
                                        }else{
                                            
                                            $sql = "SELECT * FROM talk WHERE name LIKE(:sear) AND status != 3";
                                            $stm = $pdo->prepare($sql);
                                            $stm->bindValue(':sear', "%{$sear}%", PDO::PARAM_STR);
                                            $stm->execute();
                                            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
                                            $a = 0;
                                            if(count($result)>0){
                                                foreach($result as $row){
                                                    echo '<form method="POST" name="form_se'. $a . '" action="week_talk.php">';
                                                    echo '<li class="inlink">';
                                                    echo '<input type="hidden" name="num" value="'. $row['number'] . '">';
                                                    echo '<a href="javascript:form_se' . $a . '.submit()"></a>';
                                                    echo '<p style="font-size: larger">' . $row['name'] . '</p>';
                                                    echo '<p style="color: gray">' . $row['userId'] . '</p>';
                                                    echo '</form>';
                                                    echo '</li>';
                                                    $a++;
                                                }
                                            }else{
                                                echo '<li>検索結果がありません</li>';
                                            }
                                        }
                                    }else{
                                        if(!isset($_POST["sear"]) || ($_POST["sear"] === "")){
                                            echo "<li>検索ワードを入力してください</li>";
                                            
                                        }else{
                                            
                                            $sql = "SELECT * FROM user WHERE favo = :sear";
                                            $stm = $pdo->prepare($sql);
                                            $stm->bindValue(':sear', $sear, PDO::PARAM_STR);
                                            $stm->execute();
                                            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
                                            $i = 0;
                                            if(count($result)>0){
                                                foreach($result as $row){
                                                    echo '<form method="POST" name="form_us'. $i . '" action="week_pro.php">';
                                                    echo '<li class="inlink">';
                                                    echo '<input type="hidden" name="userId" value="' . $row['userId'] . '">';
                                                    //echo '<img src="'. $row['icon'] . '" class="icon">';
                                                    echo '<a href="javascript:form_us'. $i . '.submit()"></a>';
                                                    echo '<p style="font-size: larger">' . $row['name'] . '</p>';
                                                    echo '<p style="color: gray">' . $row['userId'] . '</p>';
                                                    echo '</form>';
                                                    echo '</li>';
                                                    $i++;
                                                }
                                            }else{
                                                echo '<li>検索結果がありません</li>';
                                            }
                                        }
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
            <a href="">ブランドスレッド作成依頼</a>
        </footer>
    </body>
</html>