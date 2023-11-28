<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <h1>売り手側のログイン</h1>
    <?php 
        if(isset($_GET['error'])){
            $error_message = $_GET['error'];
            echo $error_message;
        }
    ?>
    <form action="seller_logback.php" method="POST">
        <label for="seller_id">ユーザーID:</label>
        <input type="text" name="seller_id" required><br>
        <label for="password">パスワード:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="log" value="ログイン">
    </form>
</body>
</html>
