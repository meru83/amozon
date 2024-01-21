<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>売り手新規登録</title>
    <link rel="stylesheet" href="../css/Amozon_seller.css">
</head>
<body class="seller_body">
    
    <?php
    include '../db_config.php'; // データベース接続情報を読み込む
    include '../password_policy.php'; // パスワードポリシーファイルを読み込む

    if (isset($_POST['register'])) {
        // フォームから送信されたデータを取得
        //受け取ったデータを変数に格納
        $seller_id = $_POST['seller_id'];
        $password = $_POST['password'];
        $sellerName = $_POST['sellerName'];
        $sellerphone = $_POST['number'];
        $email = $_POST['email'];


        // ユーザーIDの正規表現パターンを定義
        $seller_id_pattern = '/^[a-zA-Z0-9\-_]+$/';

        // パスワードの強度を検証
        //password_policy.phpで定義された変数。
        //返り値はtrueかfalse
        $is_valid_password = is_strong_password($password); 

        //正規表現と比較
        //trueかfalse
        $is_valid_seller_id = preg_match($seller_id_pattern, $seller_id);

        //falseの場合にエラーメッセージ表示
        if (!$is_valid_password && !$is_valid_seller_id) {
            echo "<p class='center'>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があり、ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
        } elseif (!$is_valid_password) {
            echo "<p class='center'>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があります。</p>";
        } elseif (!$is_valid_seller_id) {
            echo "<p class='center'>ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
        } else {
            // ユーザーIDの重複をチェック
            $check_sql = "SELECT seller_id FROM seller WHERE seller_id = ?
             UNION 
             SELECT user_id FROM users WHERE user_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $seller_id, $seller_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result && $check_result->num_rows > 0) {
                echo '<div class="existence">ユーザーIDがすでに存在します。</div>';
            } else {
                // パスワードをハッシュ化
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // ユーザーをデータベースに挿入
                $insert_sql = "INSERT INTO seller (seller_id, pass, sellerName, sellerPhone, email) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sssss", $seller_id, $hashed_password, $sellerName, $sellerphone, $email);
                if ($insert_stmt->execute()) {
                    //登録成功時の処理
                    echo "<p>新しいユーザーが登録されました。</p>";

                    //ユーザー情報をセッションに保存
                    session_start();
                    $_SESSION['seller_id'] = $seller_id;
                    $_SESSION['sellerName'] = $sellerName;

                    //リダイレクト先のページに移動
                    //今はチャットルームの一覧に飛ぶけど将来的にはトップページに飛ばしたい
                    header("Location: ../address_insert.php");
                    exit();

                } else {
                    echo "<p>登録中にエラーが発生しました。</p>";
                }
            }
        }
    }
    ?>
    <!---新規登録用の情報入力フォーム--->
    <div>
    <form method="POST" id ='form' class="seller_form">
        <img src="../img/Re.ReaD2blue.svg" class="seller_brand">
        <h1 class="seller_h1">新規登録</h1>
        <label for="seller_id">販売専用アカウント ID</label>
        <div id="his" style="display:none; color:red">必須</div>
        <input type="text" id="seller_id" class="seller_textbox" placeholder="re_bank" name="seller_id" required><br>
        
        <label for="number">電話番号</label><br>
        <div id="his1" style="display:none; color:red">必須</div>
        <input type="text" name="number" id="number" class="seller_textbox" class="address_textbox" placeholder="ハイフンなし" required><br>

        <label for="email">メールアドレス</label><br>
        <div id="his2" style="display:none; color:red">必須</div>
        <input type="text" name="email" id="email" class="seller_textbox" class="address_textbox" placeholder="read@gmail.com" required><br>

        <label for="password">パスワード</label>
        <div id="his3" style="display:none; color:red">必須</div>
        <input type="password" id="password" class="seller_textbox" placeholder="8桁以上" name="password"  required><br>
        <input type="password" id="rePassword" class="seller_textbox" name="rePassword" placeholder="パスワード再確認" required><br>

        <label for="sellerName">アカウント名</label>
        <div id="his4" style="display:none; color:red">必須</div>
        <input type="text" id="sellerName" class="seller_textbox" placeholder="リ・リード" name="sellerName" required><br>

        <input type="submit" id="submit" name="register" class="seller_botton" onclick="hisButton()" value="新規登録">
                <div class="seller_text">
                    <a href="seller_log.php">ログイン</a>
                </div>
                <div class="seller_text">
                    ユーザーの方は<a href="../login.php">こちら</a>
                </div>
        </form>
    </div>
</body>
</html>
<script>
const form = document.getElementById('form');
const seller_id = document.getElementById('seller_id');
const number = document.getElementById('number');
const email = document.getElementById('email');
const password = document.getElementById('password');
const rePassword = document.getElementById('rePassword');
const sellerName = document.getElementById('sellerName');
const submit = document.getElementById('submit');

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
        if(act === 'seller_id'){
            number.focus();
        }else if(act === 'number'){
            email.focus();
        }else if(act === 'email'){
            password.focus();
        }else if(act === 'password'){
            rePassword.focus();
        }else if(act === 'rePassword'){
            sellerName.focus();
        }else if(act === 'sellerName'){
            form.submit();
        }
        return false;
    }
});

function is_empty(){
    if(seller_id.value === "" || password.value === "" || rePassword.value === "" || sellerName.value === ""){
        return false;
    }else{
        return true;
    }
}

function hisButton(){
    var seller_idElement = document.getElementById('seller_id');
    var seller_id = seller_idElement.value

    var numberElement = document.getElementById('number');
    var number = numberElement.value

    var emailElement = document.getElementById('email');
    var email = emailElement.value

    var passwordElement = document.getElementById('password');
    var password = passwordElement.value

    var sellerNameElement = document.getElementById('sellerName');
    var sellerName = sellerNameElement.value
    
    if(seller_id === ""){
        var his = document.getElementById('his');
        his.style.display = 'block';
        return false;
    } else if(number === ""){
        var his = document.getElementById('his1');
        his.style.display = 'block';
        return false;
    } else if(email === ""){
        var his = document.getElementById('his2');
        his.style.display = 'block';
        return false;
    } else if(password === ""){
        var his = document.getElementById('his3');
        his.style.display = 'block';
        return false;
    } else if(sellerName === ""){
        var his = document.getElementById('his4');
        his.style.display = 'block';
        return false;
    }
}
</script>