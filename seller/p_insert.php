<?php
include "../db_config.php";

if(isset($_POST['submit'])){
    // $name = $_POST['name'];
    // $price = $_POST['price'];
    // $pieces = $_POST['pieces'];
    $image_data = isset($_FILES['image']) ? $_FILES['image']['tmp_name']: null;
    // $viwe = isset($_POST['viwe']) ? $_POST['viwe'] : null;
    // $sellerId = $_POST['seller_id'];
    // $nullVar = null;
    // $quality = $_POST['quality'];

    if(move_uploaded_file($image_data,"img/" . $_FILES['image']['name'])){
        echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
    }
    


    $sql = "INSERT INTO products (img_url) VALUES(?)";
    $stmt = $conn->prepare($sql);
    // $sql = "INSERT INTO products (productname, price, pieces, img_url, viwe, seller_id, quality)
    //         VALUES (?, ?, ?, ?, ?, ?, ?)";
    // if($stmt){
    //     if(!is_null($image_data)){
    //         $stmt->bind_param("siisssss",$name,$price,$pieces,$nullVar,$viwe,$sellerId,$quality);
    //     }else{
    //         $stmt->bind_param("siisssss",$name,$price,$pieces,$nullVar,$viwe,$sellerId,$quality);
    //     }
    //     $stmt->execute();
    // }
    $stmt->bind_param("s",$_FILES['image']['name']);
    $stmt->execute();
}
?>




<form method="post" enctype="multipart/form-data">
    <!-- <input type="text" name="name" placeholder="商品名" required><br>
    <input type="text" name="price" placeholder="価格" required><br>
    <input type="text" name="pieces" placeholder="在庫数" required><br> -->
    <input type="file" name="image" accept="image/*"><br>
    <!-- <textarea name="viwe" rows="10" placeholder="概要"></textarea><br>
    <input type="text" name="seller_id" placeholder="販売者" required><br>
    <select name="quality">
        <option value="新品、未使用">新品、未使用</option>
        <option value="良品">良品</option>
        <option value="やや傷あり">やや傷あり</option>
        <option value="不良">不良</option>
    </select> -->
    <input type="submit" name="submit" value="送信">
</form>