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

$arrayProductId = array();
$count = 0;
$buyDetail = "購入明細<br>";
$totalTtext = "";
$colorSizeText = "";
$buyDetailFlag = false;

$orderSql = "SELECT o.order_id, o.total, o.order_status, o.create_at, d.order_pieces, d.detail_total, 
p.product_id, p.productname, p.seller_id, s.color_size_id, s.color_code, s.size, s.price, s.service_status, f.user_id AS favorite_product
            FROM orders o 
            LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
            LEFT JOIN products p ON (d.product_id = p.product_id)
            LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
            LEFT JOIN favorite f ON (p.product_id = f.product_id) && (s.color_size_id = f.color_size_id) && (f.user_id = ?)
            WHERE o.user_id = ?
            ORDER BY o.create_at DESC";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("ss",$user_id,$user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
if($orderResult && $orderResult->num_rows > 0){
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

        if(!in_array($product_id, $arrayProductId)){
            if($buyDetailFlag){
                echo $buyDetail;
            }else{
                $buyDetailFlag = true;
            }

            echo $colorSizeText;
            
            echo $totalTtext;

            //明細内容
            if($service_status){
                //お気に入りボタン設置の有無
                $colorSizeText .= <<<END
                <input type="hidden" id="product_id$count" name="product_id" value="$product_id">
                <input type="hidden" id="color_size_id$count" name="color_size_id" value="$color_size_id">
                $productname $colorName $size  単価$price ＊ $order_pieces  　計　$detail_total 販売者：<a href="seller_profile.php?seller_id=$seller_id">$seller_id</a><br>
                END;
            }else{
                //お気に入りボタン未設置
                $colorSizeText .= <<<END
                $productname $colorName $size  単価$price ＊ $order_pieces  　計　$detail_total 販売者：$seller_id<br>
                END;
            }

            //箱の右下
            //$favorite_product null か $user_id
            if(!($favorite_product === null) && isset($_SESSION['user_id'])){
                //ログイン済みでお気に入り商品があった場合
                $totalTtext = <<<END
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count" checked>
                    <span class="spanHeart"></span>
                </label>
                END;
            }else if(isset($_SESSION['user_id'])){
                //ログインはしてるけどお気に入り商品ではない
                $totalTtext = <<<END
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count">
                    <span class="spanHeart"></span>
                </label>
                END;
            }else{
                //未ログイン状態のとき
                $totalTtext = <<<END
                <button type="button" class="heartBtn" onclick="heartButton()"><img src="img/heart2.png" style="height: 100%;"></button>
                END;
            }
            $totalTtext .= <<<END
            <br>
            合計購入金額：$total<br>
            配達状況　　：<a href="huzai/huzai.php">$order_status</a><br>
            購入日時　　：$create_at<br>
            <hr>
            END;

            $arrayProductId[] = $product_id;
            $count++;
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
function heartButton(){
    alert("お気に入り登録にはログインを完了させてください。");
}
</script>