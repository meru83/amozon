<?php
for($i = 0; $i < count($_POST['buyProductId']); $i++){
    $product_id = $_POST['buyProductId'][$i];
    $color_size_id = $_POST['buyColorSize'][$i];
    echo $product_id."<br>";
    echo $color_size_id."<br>";
}
?>