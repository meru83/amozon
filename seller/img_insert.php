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

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$sellerName = $_SESSION['sellerName'];

$insertId = json_decode(urldecode($_GET['insertId']),true);

if(isset($_SESSION['seller_id'])){
    $foo = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="seller_out.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
    END;
}else{
    $foo = <<<END
    <div class="New_log">
        <a href="seller.php"><div class="log_style">新規登録</div></a>
        <a href="seller_log.php"><div class="log_style rightM">ログイン</div></a>
    </div>
    END;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="../css/Amozon_insta.css">
<link rel="stylesheet" href="../css/insertStyle.css">
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">トップページ</h1>
        <?=$foo?>
    </div>

        <div class="Amozon-container">

        <!-- Left Side Menu -->
            <div class="left-menu">
                <div>
                    <ul class="menu-list">
                        <li class="menu-item-logo"><a href="#"><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href="user_top.php"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                        <li class="menu-item"><a href="search.php"><img src="../img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                        <li class="menu-item"><a href="cartContents.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                        <li class="menu-item"><a href="chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                        <li class="menu-item"><a href="favoriteProduct.php"><img src="../img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                        <li class="menu-item"><a href="user_profile.php"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    </ul>
                </div>
                <div>
                    <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><img src="../img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="right-content">
<?php

$htmlText = "";
$i = 0;

$selectSql = "SELECT p.productname, p.quality, p.create_at, p.seller_id, c.color_size_id, c.color_code, c.size 
                FROM color_size c
                LEFT JOIN products p ON c.product_id = p.product_id
                LEFT JOIN big_category b ON p.product_id = b.big_category_id
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
            $colorName = getColor($conn, $colorCode);
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
    $postId = isset($_POST['img'])?$_POST['img']:null;
    try{
        if($postId !== null){
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
                            $p = "登録に成功しました。<br>";
                        }
                    }
                }
            }
        }else{
            // エラーメッセージを表示
            echo "エラーが発生しました。申し訳ありませんが、後でもう一度試してください。<br>";
        }
    }catch(Exception $e){
        error_log("Error in create.php: " . $e->getMessage(), 3);

        // エラーメッセージを表示
        echo "エラーが発生しました。申し訳ありませんが、後でもう一度試してください。";
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
<form method="post" enctype="multipart/form-data">
    <?=$htmlText?>
    <input type="submit" class="styleBtn" value="登録"><br>
</form>
</div>
</div>
</html>

