<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    header("Location:login.php");
    exit();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
    END;
}else{
    $user_id = "A";
    $foo2 = <<<END
    <div class="New_log">
        <a href="register.php"><div class="log_style">新規登録</div></a>
        <a href="login.php"><div class="log_style rightM">ログイン</div></a>
    </div>
    END;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/buyHistory.css">
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">購入履歴</h1>
        <?=$foo2?>
    </div>

        <div class="Amozon-container">
        <!-- Left Side Menu -->
            <div class="left-menu">
                <div>
                    <ul class="menu-list">
                        <li class="menu-item-logo"><a href="" class="a_link"><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href="user_top.php" class="a_link"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                        <li class="menu-item"><a href="search.php" class="a_link"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                        <li class="menu-item"><a href="cartContents.php" class="a_link"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                        <li class="menu-item"><a href="chat_rooms.php" class="a_link"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                        <li class="menu-item"><a href="favoriteProduct.php" class="a_link"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                        <li class="menu-item"><a href="buyHistory.php" class="a_link"><img src="img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                        <?php
                        if(isset($_SESSION['user_id'])){
                            $flagUserId = $_SESSION['user_id'];
                            echo <<<HTML
                            <li class="menu-item"><a href="user_profile.php?user_id=$flagUserId" class="a_link"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                            HTML;
                        }
                        ?>
                    </ul>
                </div>
                <div>
                    <ul class="menu-list-bottom">
                    </ul>
                </div>
            </div>
            <div class="right-content">

<?php
$arrayOrderId = array();
$count = 0;
$buyDetail = "<hr><div class='backG'><div>購入明細</div><br>";
$totalTtext = "";
$colorSizeText = "";
$buyDetailFlag = false;
$deliStatusSql = array();
$deliStatusFlag = array(false,false,false,false);

if(isset($_GET['deliStatus'])){
    $deliStatus = $_GET['deliStatus'];
    foreach($deliStatus as $value){
        if($value === "出荷準備中"){
            $deliStatusSql[] = "o.order_status = '出荷準備中'";
            $deliStatusFlag[0] = true;
        }else if($value === "発送済み"){
            $deliStatusSql[] = "o.order_status = '発送済み'";
            $deliStatusFlag[1] = true;
        }else if($value === "配達中"){
            $deliStatusSql[] = "o.order_status = '配達中'";
            $deliStatusFlag[2] = true;
        }else if($value === "配達完了"){
            $deliStatusSql[] = "o.order_status = '配達完了'";
            $deliStatusFlag[3] = true;
        }
    }

    $deliStatusSqlText = "(" . implode(' OR ', $deliStatusSql) . ")";

    $orderSql = "SELECT o.order_id, o.total, o.order_status, o.create_at, d.order_pieces, d.detail_total, 
    p.product_id, p.productname, p.seller_id, s.color_size_id, s.color_code, s.size, s.price, s.service_status, f.user_id AS favorite_product
                FROM orders o 
                LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                LEFT JOIN products p ON (d.product_id = p.product_id)
                LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
                LEFT JOIN favorite f ON (p.product_id = f.product_id) && (s.color_size_id = f.color_size_id) && (f.user_id = ?)
                WHERE o.user_id = ? && $deliStatusSqlText
                ORDER BY o.create_at DESC";
}else{
    $orderSql = "SELECT o.order_id, o.total, o.order_status, o.create_at, d.order_pieces, d.detail_total, 
    p.product_id, p.productname, p.seller_id, s.color_size_id, s.color_code, s.size, s.price, s.service_status, f.user_id AS favorite_product
                FROM orders o 
                LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                LEFT JOIN products p ON (d.product_id = p.product_id)
                LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
                LEFT JOIN favorite f ON (p.product_id = f.product_id) && (s.color_size_id = f.color_size_id) && (f.user_id = ?)
                WHERE o.user_id = ? && NOT o.total = 0 && NOT d.detail_total = 0
                ORDER BY o.create_at DESC";
}

$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("ss",$user_id,$user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
if($orderResult && $orderResult->num_rows > 0){
    echo <<<HTML
    配達状況で絞り込む
    <form id="deliStatusForm" method="get" action="">
    HTML;
    if($deliStatusFlag[0] === true){
        echo '<input type="checkbox" id="deli1" name="deliStatus[]" value="出荷準備中" checked><label for="deli1">出荷準備中</label>';
    }else{
        echo '<input type="checkbox" id="deli1" name="deliStatus[]" value="出荷準備中"><label for="deli1">出荷準備中</label>';
    }

    if($deliStatusFlag[1] === true){
        echo '<input type="checkbox" id="deli2" name="deliStatus[]" value="発送済み" checked><label for="deli2">発送済み</label>';
    }else{
        echo '<input type="checkbox" id="deli2" name="deliStatus[]" value="発送済み"><label for="deli2">発送済み</label>';
    }

    if($deliStatusFlag[2] === true){
        echo '<input type="checkbox" id="deli3" name="deliStatus[]" value="配達中" checked><label for="deli3">配達中</label>';
    }else{
        echo '<input type="checkbox" id="deli3" name="deliStatus[]" value="配達中"><label for="deli3">配達中</label>';
    }

    if($deliStatusFlag[3] === true){
        echo '<input type="checkbox" id="deli4" name="deliStatus[]" value="配達完了" checked><label for="deli4">配達済み</label>';
    }else{
        echo '<input type="checkbox" id="deli4" name="deliStatus[]" value="配達完了"><label for="deli4">配達済み</label>';
    }
        
    echo "</form>";
    while($orderRow = $orderResult->fetch_assoc()){
        $order_id = $orderRow['order_id'];
        $total = $orderRow['total'];
        $order_status = $orderRow['order_status'];
        $create_at = $orderRow['create_at'];
        $order_pieces = $orderRow['order_pieces'];
        $detail_total = $orderRow['detail_total'];
        $product_id = $orderRow['product_id'];
        $productname = $orderRow['productname'];
        $seller_id = $orderRow['seller_id'];
        $color_size_id = $orderRow['color_size_id'];
        $color_code = $orderRow['color_code'];
        $colorName = getColor($conn, $color_code);
        $size = $orderRow['size'];
        $price = $orderRow['price'];
        $service_status = $orderRow['service_status'];
        $favorite_product = $orderRow['favorite_product'];

        //明細内容
        if($service_status){
            //お気に入りボタン設置の有無
            $colorSizeText = <<<END
            <input type="hidden" id="product_id$count" name="product_id" value="$product_id">
            <input type="hidden" id="color_size_id$count" name="color_size_id" value="$color_size_id">
            <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">$productname $colorName $size</a>  <div>単価$price  ＊ $order_pieces  </div><div>計　$detail_total</div> <div>販売者：<a href="seller/seller_profile.php?other_id=$seller_id">$seller_id</a></div><br>
            END;
            //$favorite_product null か $user_id
            if(!($favorite_product === null) && isset($_SESSION['user_id'])){
                //ログイン済みでお気に入り商品があった場合
                $colorSizeText .= <<<END
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count" checked>
                    <span class="spanHeart"></span>
                </label>
                END;
            }else if(isset($_SESSION['user_id'])){
                //ログインはしてるけどお気に入り商品ではない
                $colorSizeText .= <<<END
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count">
                    <span class="spanHeart"></span>
                </label>
                END;
            }else{
                //未ログイン状態のとき
                $colorSizeText .= <<<END
                <button type="button" class="heartBtn" onclick="heartButton()"><img src="img/heart2.png" style="height: 100%;"></button>
                END;
            }
        }else{
            //お気に入りボタン未設置
            $colorSizeText = <<<END
            <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">$productname $colorName $size </a> <div>単価$price  ＊ $order_pieces  </div><div>計　$detail_total</div> <div>販売者：<a href="seller/seller_profile.php?seller_id=$seller_id">$seller_id</a></div><br>
            END;
        }

        if(!in_array($order_id, $arrayOrderId)){
            echo $totalTtext;

            echo $buyDetail;

            echo $colorSizeText;

            $totalTtext = <<<END
            <br>
            <hr class="hr_hasen">
            <div>合計購入金額：$total</div>
            <div>配達状況　　：<a href="huzai/huzai.php?order_id=$order_id">$order_status</a></div>
            <div>購入日時　　：$create_at</div>
            </div>
            <hr>
            <br>
            END;

            $arrayOrderId[] = $order_id;
            $count++;
        }else{
            echo $colorSizeText;
        }
    }
    echo $totalTtext;
}else{
    //購入履歴がないとき
    echo "<div class='buyHistory_no'><p class='p_history'>購入履歴がありません</p>";
    echo '<a href="user_top.php"><div class="home">ホームに戻る</div></a>';
    echo '</div>';
}

echo '</div>';//<div class="right-content">
echo '</div>';//<div class="Amozon-container">
echo '</body>';
echo '</html>';

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

// データベース接続を閉じる
$conn->close();

echo <<<END
<script>
var countMax = $count;
for(let i = 0; i < countMax; i++){
    var favorite_product = document.getElementById('favorite'+i);
    if(favorite_product !== null){
        favorite_product.addEventListener('change', function(){
            var checkState = this.checked;
            var product_id = document.getElementById('product_id'+i).value;
            var color_size_id = document.getElementById('color_size_id'+i).value;
            var favoriteChecked = checkState ? 1 : 0;
            const formData = new FormData();
            formData.append('product_id', product_id);
            formData.append('color_size_id', color_size_id);
            formData.append('favoriteChecked', favoriteChecked);
            fetch('changeFavorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if(!response.ok){
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error_message === 1) {
                    if (favoriteChecked === 1) {
                        this.checked = true;
                    } else {
                        this.checked = false;
                    }
                } else {
                    if (favoriteChecked === 1) {
                        alert("お気に入り登録に失敗しました。");
                        this.checked = false;
                    } else {
                        alert("お気に入り商品の削除に失敗しました。");
                        this.checked = true;
                    }
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                alert("リクエストが失敗しました。");
            });
        });
    }
}
</script>
END;
?>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const deliStatusForm = document.getElementById('deliStatusForm');
    const deli1 = document.getElementById('deli1');
    const deli2 = document.getElementById('deli2');
    const deli3 = document.getElementById('deli3');
    const deli4 = document.getElementById('deli4');
    deli1.addEventListener('change',function(){
        deliStatusForm.submit();
    });
    deli2.addEventListener('change',function(){
        deliStatusForm.submit();
    });
    deli3.addEventListener('change',function(){
        deliStatusForm.submit();
    });
    deli4.addEventListener('change',function(){
        deliStatusForm.submit();
    });
});
function heartButton(){
    alert("お気に入り登録にはログインを完了させてください。");
}
</script>