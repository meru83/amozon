<?php
$host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "complete";

// データベースに接続
$conn = new mysqli($host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("データベースへの接続に失敗しました: " . $conn->connect_error);
}
?>
