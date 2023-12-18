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
            echo "<p>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があり、ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
        } elseif (!$is_valid_password) {
            echo "<p>パスワードは大文字、小文字、数字をすべて含む8文字以上である必要があります。</p>";
        } elseif (!$is_valid_seller_id) {
            echo "<p>ユーザーIDには半角英数字、ハイフン、アンダーバーが利用できます。</p>";
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
                echo "<p>ユーザーIDがすでに存在します。</p>";
            } else {
                // パスワードをハッシュ化
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // ユーザーをデータベースに挿入
                $insert_sql = "INSERT INTO seller (seller_id, pass, sellerName) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sss", $seller_id, $hashed_password, $sellerName);
                if ($insert_stmt->execute()) {
                    //登録成功時の処理
                    echo "<p>新しいユーザーが登録されました。</p>";

                    //ユーザー情報をセッションに保存
                    session_start();
                    $_SESSION['seller_id'] = $seller_id;
                    $_SESSION['sellerName'] = $sellerName;

                    //リダイレクト先のページに移動
                    //今はチャットルームの一覧に飛ぶけど将来的にはトップページに飛ばしたい
                    header("Location: seller_top.php");
                    exit();

                } else {
                    echo "<p>登録中にエラーが発生しました。</p>";
                }
            }
        }
    }
    ?>
    <!---新規登録用の情報入力フォーム--->
    <form method="POST" id ='form' class="seller_form">
        <img src="../img/Re.ReaD2blue.svg" class="seller_brand">
        <h1 class="seller_h1">売り手新規登録</h1>
        <label for="seller_id"></label>
        <input type="text" id="seller_id" class="seller_textbox" placeholder="販売専用アカウントID:" name="seller_id" required><br>
        
        <label for="password"></label>
        <input type="password" id="password" class="seller_textbox" placeholder="パスワード:" name="password"  required><br>

        <label for="sellerName"></label>
        <input type="text" id="sellerName" class="seller_textbox" placeholder="ユーザー名:" name="sellerName" required><br>

        <input type="submit" name="register" class="seller_botton" value="新規登録">
    </form>
</body>
</html>
