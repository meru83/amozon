<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

session_regenerate_id(TRUE);
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    header("Location:login.php");
    exit();
}

$sql = "SELECT f.product_id, f.color_size_id, p.productname, s.color_code, s.size, s.service_status, i.img_url
        FROM favorite f
        LEFT JOIN products p ON (f.product_id = p.product_id)
        LEFT JOIN color_size s ON (f.color_size_id = s.color_size_id)
        LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$htmlText = "";
if($result && $result->num_rows > 0){
    $lastImg = array();
    while($row = $result->fetch_assoc()){
        $product_id = $row['product_id'];
        $color_size_id = $row['color_size_id'];
        $productname = $row['productname'];
        $color_code = $row['color_code'];
        $colorName = getColor($conn,$color_code);
        $size = $row['size'];
        $service_status = $row['service_status'];
        $img_url = is_null($row['img_url'])?null:$row['img_url'];

        if(!is_null($img_url)){
            $imgText = "
            <div class='swiper-slide'>
            <img src='seller/p_img/$img_url'>
            </div>";
        }else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
            $imgText = "
            <div class='swiper-slide'>
            <img src='img/noImg.jpg'>
            </div>";
        }
        if(!in_array($color_size_id, $lastImg)){
            $lastImg[] = $color_size_id;
            echo $htmlText;
            echo "<br>";
            echo $imgText;
            $htmlText = <<<END
            $productname<br>
            $colorName<br>
            END;
        }else{
            echo $imgText;
        }
    }
}

function getColor($conn, $color_code){
    $colorSql = "SELECT * FROM color_name
                WHERE color_code = ?";
    $colorStmt = $conn->prepare($colorSql);
    $colorStmt->bind_param("s",$color_code);
    $colorStmt->execute();
    $colorResult = $colorStmt->get_result();
    if ($row = $colorResult->fetch_assoc()) {
        $colorName = $row['colorName']; // ここで正しいカラム名を使用
        return $colorName;
    } 
}
?>