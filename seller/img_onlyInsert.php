<?php
//写真を後から追加できる要にするためのファイル

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['seller_id'])) {
    header("Location: seller_log.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$sellerName = $_SESSION['sellerName'];

$htmlText = "";
$i = 0;

$selectSql = "SELECT p.productname, p.quality, p.create_at, p.seller_id, c.color_size_id, c.color_code, c.size 
                FROM color_size c
                LEFT JOIN products p ON c.product_id = p.product_id
                WHERE color_size_id = ?";
$selectStmt = $conn->prepare($selectSql);
foreach($insertId as $rowId){
    $selectStmt->bind_param("i",$rowId);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    if($selectResult->num_rows > 0){
        $htmlText .= "<input type='text' name='img[$i][id]' value='$rowId' hidden><br>";    
        foreach($selectResult as $row){
            $productname = $row['productname'];
            $quality = $row['quality'];
            $size = $row['size'];
            $create_at = $row['create_at'];
            $seller_id = $row['seller_id'];

            $colorCode = $row['color_code'];
            $colorCode = str_replace("#","",$colorCode);
            $response = file_get_contents("https://www.thecolorapi.com/id?hex=" . $colorCode);//カラーコードから色を取得するAPI
            $colorData = json_decode($response);
            $colorName = $colorData->name->value;
            $htmlText .= <<<END
            商品名：$productname<br>
            カラー：$colorName<br>
            サイズ：$size<br>
            品質　：$quality<br>
            登録日：$create_at<br>
            販売者：$seller_id<br>
            <input type="file" name="img[$i][]" multiple accept="image/*"><br>
            <hr>
            END;
        }
        $i++;
    }
}

$p = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $postId = $_POST['img'];
    foreach($postId as $key => $row){
        $id = $row['id'];//登録したてのcolor_size_id    
        $imgNameArray = $_FILES['img']['name'][$key];
        $imgTmpArray = $_FILES['img']['tmp_name'][$key];
        $imgFileLength = count($imgNameArray);
        for($i = 0; $i < $imgFileLength; $i++){
            $imgPath = $imgNameArray[$i];
            $imgTmp = $imgTmpArray[$i];
            $imgPathId = add_filename($imgPath,$id);

            if(move_uploaded_file($imgTmp,"p_img/".$imgPathId)){
                $insertSql = "INSERT INTO products_img(color_size_id, img_url)
                                VALUE(?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("is",$id,$imgPathId);
                if($insertStmt->execute()){
                    $p = "登録に成功しました";
                }
            }
        }
    }
}
if(!($p === "")){
    echo $p;
}

function add_filename($filename,$addtext){
    //拡張子の前に文字列を追加
    $pos = strrpos($filename, '.'); // .が最後に現れる位置
    if ($pos){
        return(substr($filename, 0, $pos).$addtext.substr($filename, $pos));
    }else{
        return($filename.$addtext);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録途中</title>
</head>
<body>
<a href="p2_insert.php">登録画面に戻る</a>
<form method="post" enctype="multipart/form-data">
    <?=$htmlText?>
    <input type="submit" value="登録"><br>
</form>
</body>
</html>