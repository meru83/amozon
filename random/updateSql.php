<?php
include "db_config.php";
$sql = "SELECT order_id, product_id, color_size_id, order_pieces FROM orders_detail";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $order_id = $row['order_id'];
    $product_id = $row['product_id'];
    $order_pieces = $row['order_pieces'];
    $color_size_idBefore = $row['color_size_id'];
    // if($product_id > 179 || $product_id <= 0){
    //     echo $product_id;
    //     echo $order_pieces;
    // }
    $sql2 = "SELECT color_size_id, price FROM color_size WHERE product_id = $product_id";
    $result2 = $conn->query($sql2);
    if($result2 && $result2->num_rows > 0){
        $row2 = $result2->fetch_assoc();
        $color_size_id = $row2['color_size_id'];
        $price = $row2['price'];
        // echo $color_size_id;
        // echo $price;
        $detail_total = $price * $order_pieces;
        $sql3 = "UPDATE orders_detail SET color_size_id = $color_size_id, detail_total = $detail_total 
                WHERE order_id = $order_id && product_id = $product_id && color_size_id = $color_size_idBefore";
        $conn->query($sql3);
    }
}
?>