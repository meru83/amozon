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
    ?>
    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="ログアウト">
    </form>

    <a href="search.php">検索</a><br>
    <a href="chat_rooms.php">チャットルーム一覧</a>
</body>
</html>