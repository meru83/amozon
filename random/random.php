<?php
include "db_config.php";
//761 order
//160 product
//588
$insertSql = "INSERT INTO orders_detail VALUES(?,?,?,?,?)";
$insertStmt = $conn->prepare($insertSql);
$order_id = rand(1,761);
$product_id = rand(1,160);
$color_size_id = rand(1,588);
$order_pieces = rand(1,5);

$select = "SELECT price FROM color_size
            WHERE color_size_id = $color_size_id";
$selectResult = $conn->query($select);
$price;
while($row = $selectResult->fetch_assoc()){
    $price = $row['price'];
}
$detail_total = $price*$order_pieces;
$insertStmt->bind_param("iiiii",$order_id,$product_id,$color_size_id,$order_pieces,$detail_total);
$insertStmt->execute();
?>