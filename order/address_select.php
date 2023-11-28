<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>住所選択</title>
</head>
<body>
    <h2>配送先を選択してください。</h2>

    <form action="payment.php" method="post">
        <?php
        include "../db_config.php";
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }

        if(!isset($_POST['user_id'])){
            header("Location: login.php?cart_id");
            exit();
        }
        ?>
    </form>
</body>
</html>