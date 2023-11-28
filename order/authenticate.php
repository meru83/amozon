<?php
session_start();

// データベースへの接続情報を設定します
include 'db_config.php'; // データベース接続情報を読み込む

// フォームから送信されたユーザー名とパスワードを取得します
$userid = $_POST['user_id'];
$password = $_POST['password'];

// パスワードの検証
$sql = "SELECT user_id, username, pass FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userid);
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
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        header("Location: chat_rooms.php");
        exit();
    }else{
        $error_message = "ユーザー名もしくはパスワードが間違えています。";
        header("Location: login.php?error=".urlencode($error_message));
        exit();
    }
}else{
    $error_message = "ユーザー名もしくはパスワードが間違えています。";
    header("Location: login.php?error=".urlencode($error_message));
    exit();
}

// 認証失敗
header("Location: login.php");
exit();
?>
