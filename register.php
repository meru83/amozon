<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="css/Amozon_register.css">
</head>
<body class="register_body">

    
    <?php
    include 'db_config.php'; // データベース接続情報を読み込む
    include 'password_policy.php'; // パスワードポリシーファイルを読み込む

    if (isset($_POST['register'])) {
        // フォームから送信されたデータを取得
        //受け取ったデータを変数に格納
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
        $username = $_POST['username'];

        // ユーザーIDの正規表現パターンを定義
        $user_id_pattern = '/^[a-z0-9\-_]+$/';

        // パスワードの強度を検証
        //password_policy.phpで定義された変数。
        //返り値はtrueかfalse
        $is_valid_password = is_strong_password($password); 

        //正規表現と比較
        //trueかfalse
        $is_valid_user_id = preg_match($user_id_pattern, $user_id);

        //falseの場合にエラーメッセージ表示
        if (!$is_valid_password && !$is_valid_user_id) {
            echo "<p>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があり、ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
        } elseif (!$is_valid_password) {
            echo "<p>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があります。</p>";
        } elseif (!$is_valid_user_id) {
            echo "<p>ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
        } else {
            // ユーザーIDの重複をチェック
            $check_sql = "SELECT seller_id FROM seller WHERE seller_id = ?
            UNION 
            SELECT user_id FROM users WHERE user_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $user_id, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result && $check_result->num_rows > 0) {
                echo "<p>ユーザーIDがすでに存在します。</p>";
            } else {
                // パスワードをハッシュ化
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // ユーザーをデータベースに挿入
                $insert_sql = "INSERT INTO users (user_id, pass, username) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sss", $user_id, $hashed_password, $username);
                if ($insert_stmt->execute()) {
                    //登録成功時の処理
                    echo "<p>新しいユーザーが登録されました。</p>";

                    //ユーザー情報をセッションに保存
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;

                    //リダイレクト先のページに移動
                    header("Location: address_insert.php");
                    exit();

                } else {
                    echo "<p>登録中にエラーが発生しました。</p>";
                }
            }
        }
    }
    ?>
    <!---新規登録用の情報入力フォーム--->
    <form method="POST" id ='form' class="register_form" >
        <img src="img/Re.ReaD2blue.svg" class="register_brand">
        <h1 class="register_h1">新規登録</h1>
        <label for="user_id"></label>
        <input type="text" id="user_id" class="register_textbox" name="user_id" placeholder="ユーザーID:" required><br>
        
        <label for="password"></label>
        <input type="password" id="password" class="register_textbox" name="password" placeholder="パスワード:" required><br>

        <label for="rePassword"></label>
        <input type="password" id="rePassword" class="register_textbox" name="rePassword" placeholder="パスワード再確認:" required><br>

        <label for="username"></label>
        <input type="text" id="username" class="register_textbox" name="username" placeholder="ユーザー名:" required><br>

        <input type="submit" name="register" class="register_botton" value="新規登録">
    </form>
</body>
</html>
<script>
const form = document.getElementById('form');
const user_id = document.getElementById('user_id');
const password = document.getElementById('password');
const rePassword = document.getElementById('rePassword');
const username = document.getElementById('username');
form.addEventListener('keydown',(e) => {
    if(is_empty() && (e.key === 'Enter')){
        //全てのフォームが入力済みの時
        if(password.value === rePassword.value){
            //passとrePassがイコール
            return true;
        }else{
            e.preventDefault();
            alert("パスワードが一致しません。");
            return false;
        }
    }else if(e.key === 'Enter'){
        e.preventDefault();
        let act = document.activeElement.id;
        if(act === 'user_id'){
            password.focus();
        }else if(act === 'password'){
            rePassword.focus();
        }else if(act === 'rePassword'){
            username.focus();
        }
        return false;
    }
});

function is_empty(){
    if(user_id.value === "" || password.value === "" || rePassword.value === "" || username.value === ""){
        return false;
    }else{
        return true;
    }
}
</script>