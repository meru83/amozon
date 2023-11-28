<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// セッション変数をすべて空にする
$_SESSION = array();

// セッションクッキーを削除する
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// セッションを破棄する
session_destroy();

// 新しいセッションIDを発行する
//攻撃者がセッションIDを予測しにくくなる。
session_regenerate_id(true);

// ログアウト後にリダイレクト
//ここログインページじゃなくてトップページに飛ぶようにしてもいい
header("Location: seller_log.php");
exit();
?>
