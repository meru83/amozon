<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーのトップ</title>
</head>
<body>
    <h1>ユーザー側</h1>
    <?php
    if(isset($_GET['error_message'])){
        $error_message = $_GET['error_message'];
        echo "<p>".$error_message."</p>";
    }
    if(isset($_SESSION['user_id'])){
        echo <<<END
        <form action="logout.php" method="post">
            <input type="submit" name="logout" value="ログアウト">
        </form>
        END;
    }else{
        echo <<<END
        <a href="register.php">新規登録</a>
        <a href="login.php">ログイン</a>
        <br>
        <br>
        END;
    }
    ?>

    <a href="search.php">検索</a><br>
    <a href="cartContents.php">カート</a><br>
    <a href="chat_rooms.php">チャットルーム一覧</a>
</body>
</html>