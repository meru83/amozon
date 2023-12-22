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
                <h1 class="login_h1">販売専用アカウント ログイン</h1>
                <div class="error-message">
                    <?php 
                    if(isset($_GET['error'])){
                        $error_message = $_GET['error'];
                        echo htmlspecialchars($error_message); 
                    }
                    ?>
                </div>
                <form action="seller_logback.php" id="seller_login_form" method="POST">
                    <input type="text" name="seller_id" placeholder="ユーザーID" id="seller_login_id" class="login_input_size" required><br>
                    <input type="password" name="password" placeholder="パスワード"  id="seller_login_password" class="login_input_size" required><br>
                    <input type="submit" id="subLog" name="log" value="ログイン" class="login_button">
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
<script>
const user_login_id = document.getElementById('seller_login_id');
const user_login_password = document.getElementById('seller_login_password');
const subLog = document.getElementById('subLog');
const user_login_form = document.getElementById('seller_login_form');
user_login_form.addEventListener('keydown', (e) => {
    if(is_empty()){
        return true;
    }else if (e.key === 'Enter'){
        e.preventDefault();
        let act = document.activeElement.id;
        if(act === 'seller_login_id'){
            user_login_password.focus();
        }else if(act === 'seller_login_password'){
            subLog.focus();
        }
        return false;
    }
});

function is_empty(){
    if(seller_login_id.value === "" || seller_login_password.value === ""){
        return false;
    }else{
        return true;
    }
}
</script>
</body>
</html>
<!-- jsで正規表現と比較してからsubmit()  -->