<?php
include '../db_config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else if(isset($_SESSION['seller_id'])){
    $seller_id = $_SESSION['seller_id'];
    echo <<<END
    <button onclick="locationButton()">プロフィールを編集</button><br>
    END;
}

if(isset($_GET['seller_id'])){
    $seller_id = $_GET['seller_id'];
}

$sql = "SELECT * FROM seller
        WHERE seller_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $sellerName = $row['sellerName'];
        echo $sellerName."<br>";
        if(isset($_SESSION['user_id'])){
            echo "<a href='../create.php?seller_id=$seller_id'>チャット</a>";
        }
    }
}
?>
<script>
function locationButton(){
    window.location.href = 'seller_edit.php';
}
</script>