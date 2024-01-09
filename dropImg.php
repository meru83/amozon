<?php
include 'db_config.php';

$sql = "SELECT img_url FROM products_img";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $img_url = $row['img_url'];
    if(!file_exists("seller/p_img/$img_url")){
        $drop = "DELETE FROM products_img WHERE img_url = '$img_url'";
        $conn = query($img_url);
        echo "削除しました。";
    }
}
?>