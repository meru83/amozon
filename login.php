<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/Amozon_login.css">
    <title>ログイン</title>
</head>
<body>
<div class="a">
    <main class="login_mainb">
        <div class="login_form">
            <div class="login_border">
                <img src="img/Re.ReaD2blue.svg" class="login_brand">
                <h1 class="login_h1">ユーザーのログイン</h1>
                <div class="error-message">
                    <?php 
                    if(isset($_GET['error'])){
                        $error_message = $_GET['error'];
                        echo htmlspecialchars($error_message); 
                    }
                    ?>
                </div>
                <form action="authenticate.php" id="user_login_form" method="post">
                    <input type="text" name="user_id" placeholder="ユーザーID" id="user_login_id" class="login_input_size" required><br>
                    <input type="password" name="password" placeholder="パスワード" id="user_login_password"  class="login_input_size" required><br>
                    <input type="submit" value="ログイン" id="user_login_submit" class="login_button">
                </form>
                <div class="register">
                    <a href="register.php">新規登録</a>
                </div>
                <div class="hanbai">
                    販売希望の方は<a href="seller/seller_log.php">こちら</a>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
let user_login_form = document.getElementById('user_login_form');
user_login_form.addEventListener('keydown', (e) => {
    if(is_empty()){
        return true;
    }else if (e.key === 'Enter'){
        e.preventDefault();
        let act = document.activeElement.id;
        if(act === 'user_login_id'){
            let user_login_password = document.getElementById('user_login_password');
            user_login_password.focus();
        }
        return false;
    }
});

function is_empty(){
    let user_login_id = document.getElementById('user_login_id');
    let user_login_password = document.getElementById('user_login_password');

    if(user_login_id.value === "" || user_login_password.value === ""){
        return false;
    }else{
        return true;
    }
}
</script>
</body>
</html>
