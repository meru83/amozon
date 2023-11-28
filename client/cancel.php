<?php

include "../db_config.php";

if(isset($_GET['seller_id'])){
    $sellerId = $_GET['seller_id'];

    $sql = "UPDATE seller SET official = false
            WHERE seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$sellerId);
    if($stmt->execute()){
        $error_message = $sellerId."様の承認を取り消しました。";
        header("Location:cancel_official.php ? error=".urlencode($error_message));
    }else{
        $error_message = "取り消しに失敗しました。";
        header("Location:cancel_official.php ? error=".urlencode($error_message));
        exit();
    }
}else{
    $error_message = "取り消しに失敗しました。";
    header("Location:cancel_official.php ? error=".urlencode($error_message));
    exit();
}
?>