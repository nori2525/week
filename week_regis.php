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
                <a href="week_start.php" class="logo">Week</a>
            <div>
        </header>
        <div id="wrap">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <p class="subtitle">Welcome</p>
                        <div class="column1">
                            <form method="POST" action="week_check.php">
                                <ul class="register">
                                    <li><input type="text" name="id" placeholder="ユーザーID" class="no"></li>
                                    <li><input type="text" name="name" placeholder="ユーザー名" class="no"></li>
                                    <li><input type="text" name="e-mail" placeholder="メールアドレス" class="no"></li>
                                    <li><input type="password" name="pass" placeholder="パスワード" class="no"></li>
                                    <li><input type="password" name="repass" placeholder="パスワードの確認" class="no"></li>
                                    <li><input type="radio" name="sex" value="1" checked class="ra">男性<input type="radio" name="sex" value="2" class="ra">女性</li>
                                    <input type="submit" value="登録" class="btn">
                                </ul>
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