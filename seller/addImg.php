<?php
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

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $color_size_id = $_POST['color_size_id'];
    $imgTmpArray = $_FILES['imgFile']['tmp_name'];
    $imgNameArray = $_FILES['imgFile']['name'];
}else{
    header("Location:seller_products.php");
    exit();
}
$imgFileLength = count($imgTmpArray);
// error_log($imgFileLength);

try{
    $sql = "INSERT INTO products_img(color_size_id, img_url) VALUES(?, ?)";
    for($i = 0; $i < $imgFileLength; $i++){
        $imgPath = $imgNameArray[$i];
        $imgTmp = $imgTmpArray[$i];
        $imgPathId = add_filename($imgPath, $color_size_id);
        if(move_uploaded_file($imgTmp,"p_img/".$imgPathId)){
            $insertSql = "INSERT INTO products_img(color_size_id, img_url)
                            VALUE(?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("is",$color_size_id,$imgPathId);
            if($insertStmt->execute()){
                $error_message = true;
            }
        }
    }
}catch (Exception $e) {
    error_log("error : ".$e->getMessage());
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
exit();

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