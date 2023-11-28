<?php
//includeで使う。
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(isset($_GET['product_id'] && isset($_GET['pieces']))){
    $product_id = $_GET['product_id'];
    $pieces = $_GET['pieces'];

    if(!isset($_SESSION['user_id'])){
        $_SESSION['cart'][$product_id] = $pieces;
    }else{
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO cart(user_id, product_id, pieces)
                VALUE(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii",$user_id,$product_id,$pieces);
        $stmt->execute();
    }
}
?>