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
    <title>売り手のトップ</title>
</head>
<body>
    <h1>売り手側</h1>
    <?php
    if(isset($_SESSION['seller_id'])){
        echo <<<END
        <form action="seller_out.php" method="post">
            <input type="submit" name="logout" value="ログアウト">
        </form>
        END;
    }else{
        echo <<<END
        <a href="seller.php">新規登録</a>
        <a href="seller_log.php">ログイン</a>
        <br>
        <br>
        END;
    }
    ?>

    <?php
    if(isset($_SESSION['seller_id'])){
        echo <<<END
        <a href="p2_insert.php">商品情報登録</a><br>
        <a href="seller_products.php">登録済み商品一覧</a><br>
        <a href="../chat_rooms.php">チャットルーム一覧</a>
        END;
    }else{
        echo "ユーザー登録またはログインを完了させてください。";
    }
    ?>
</body>
</html>