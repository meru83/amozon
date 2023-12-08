<?php
    session_start();

    $_SESSION['seller_id'] = null;
    $_SESSION['sellerName'] = null;
    $_SESSION['user_id'] = null;
    $_SESSION['username'] = null;

    // データベースへの接続情報を設定します
    include '../db_config.php'; // データベース接続情報を読み込む

    // フォームから送信されたユーザー名とパスワードを取得します
    $sellerid = $_POST['seller_id'];
    $password = $_POST['password'];

    // パスワードの検証
    $sql = "SELECT seller_id, sellerName, pass FROM seller WHERE seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sellerid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['pass']; 
        //ハッシュ化されたパスワードと平文のパスワードを比較する。
        if (password_verify($password, $hashed_password)) {
            // 認証成功
            //ユーザー情報をセッションに保存
            //今はチャットルームの一覧に飛ぶけどトップに飛ばすようにする
            $_SESSION['seller_id'] = $row['seller_id'];
            $_SESSION['sellerName'] = $row['sellerName'];
            header("Location: seller_top.php");
            exit();
        }else{
            $error_message =  "ユーザー名またはパスワードが間違えています。";
            header("Location: seller_log.php?error=".urlencode($error_message));
            exit();
        }
    }else{
        $error_message = "ユーザー名またはパスワードが間違えています。";
        header("Location: seller_log.php?error=".urlencode($error_message));
        exit();
    }

    // 認証失敗
    header("Location: seller_log.php");
    exit();
?>