<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/Amozon_login.css">
    <title>ログイン</title>
</head>
<body>
<div class="a">
    <main class="login_mainb">
        <div class="login_form">
            <div class="login_border">
                <img src="../img/Re.ReaD2blue.svg" class="login_brand">
                <h1 class="login_h1">販売者のログイン</h1>
                <div class="error-message">
                    <?php 
                    if(isset($_GET['error'])){
                        $error_message = $_GET['error'];
                        echo htmlspecialchars($error_message); 
                    }
                    ?>
                </div>
                <form action="seller_logback.php" method="POST">
                    <input type="text" name="seller_id" placeholder="ユーザーID" class="login_input_size" required><br>
                    <input type="password" name="password" placeholder="パスワード"  class="login_input_size" required><br>
                    <input type="submit" name="log" value="ログイン" class="login_button">
                </form>
                <div class="register">
                    <a href="register.php">新規登録</a>
                </div>
                <div class="hanbai">
                    ユーザーの方は<a href="../login.php">こちら</a>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
