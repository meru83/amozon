<?php
include "db_config.php";
for($i = 0; $i <= 764; $i++){
    $sql2 = "SELECT order_id FROM orders WHERE order_id = $i";
    $result2 = $conn->query($sql2);
    if($result2 && $result2->num_rows > 0){
        $random = rand(1,4);
        if($random === 1){
            $order_status = "出荷準備中";
        }else if($random === 2){
            $order_status = "発送済み";
        }else if($random === 3){
            $order_status = "配達中";
        }else if($random === 4){
            $order_status = "配達完了";
        }
        $sql = "UPDATE orders SET order_status = '$order_status' WHERE order_id = $i";
        $conn->query($sql);
    }
}
?>