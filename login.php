<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <h1>ユーザーのログイン</h1>
    <?php 
        if(isset($_GET['error'])){
            $error_message = $_GET['error'];
            echo $error_message;
        }
    ?>
    <form action="authenticate.php" method="post">
        <label for="user_id">ユーザーID:</label>
        <input type="text" name="user_id" required><br>
        <label for="password">パスワード:</label>
        <input type="password" name="password" placeholder="半角8文字以上" required><br>
        <input type="submit" value="ログイン">
    </form>
</body>
</html>
