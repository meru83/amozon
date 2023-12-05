<?php
// データベース接続
include "db_config.php";

// 検索キーワードを取得し、空白で分割
$searchText = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = "INSERT INTO search(searchText) VALUES(?)";
$searchStmt = $conn->prepare($searchSql);
$searchStmt->bind_param("s", $searchText);
$searchStmt->execute();
$count = 0;
if(!empty($searchText)  && !in_array($searchText, ['新品', '未使用', '新品未使用', '新品、未使用', '中古', '中古品', '良品', 'やや傷あり', '不良', '傷あり'])){
    if(preg_match('/[|]+/u',$searchText)){
        $orKeywords = preg_split('/[|]+/u', $searchText);
    }else{
        $orKeywords = array($searchText);
    }


    foreach($orKeywords as $orKeyword){
        $conditions = array();
        $qualityConditions = array();
        $keywords = preg_split('/\s+/u',$orKeyword);
        foreach ($keywords as $keyword) {
            if(in_array($keyword,['新品', '未使用', '新品未使用', '新品、未使用', '中古', '中古品', '良品', 'やや傷あり', '不良', '傷あり'])){
                if (in_array($keyword, ['中古', '中古品'])) {
                    $qualityConditions[] = "(p.quality = '良品' OR p.quality = 'やや傷あり' OR p.quality = '不良')";
                } elseif (in_array($keyword, ['新品', '未使用', '新品未使用'])) {
                    $qualityConditions[] = "p.quality = '新品、未使用'";
                } elseif (in_array($keyword, ['傷あり'])) {
                    $qualityConditions[] = "p.quality = 'やや傷あり'";
                } else {
                    $qualityConditions[] = "p.quality = '$keyword'";
                }
            }else{
                $conditions[] = "(p.productname LIKE '%$keyword%' OR 
                                p.big_category_id IN (SELECT big_category_id FROM big_category WHERE big_category_name LIKE '%$keyword%') OR
                                p.category_id IN (SELECT category_id FROM category WHERE category_name LIKE '%$keyword%') OR
                                p.small_category IN (SELECT small_category FROM small_category WHERE small_category_name LIKE '%$keyword%'))";
            }
        }
        if(!empty($qualityConditions)){
            $conditions[] = "(" . implode(' OR ', $qualityConditions) . ")";
        }
        $andConditions = implode(' AND ', $conditions);

        // 検索結果を取得するクエリを作成
        $sql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.seller_id, p.big_category_id, p.category_id, p.small_category, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, c.category_name, i.img_url FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE $andConditions";

        // echo "クエリ：".$sql."<br>";

        // クエリを実行a
        $result = $conn->query($sql);

        $htmlText = "";
        // クエリの実行結果を確認
        if ($result) {    
            // 検索結果を表示
            if ($result->num_rows > 0) {
                $lastImg = array();
                while ($row = $result->fetch_assoc()) {
                    $imgText = null;
                    $colorCode = $row['color_code'];
                    $colorName = getColor($conn, $colorCode);
                    $product_id = $row['product_id'];
                    $size = $row['size'];
                    $productname = $row['productname'];
                    $category_name = !is_null($row['category_name'])?$row['category_name']:"";
                    $price  = $row['price'];
                    $color_size_id = $row['color_size_id'];
                    $img_url = is_null($row['img_url'])?null:$row['img_url'];
                    if(!is_null($img_url)){
                        $imgText = "
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='seller/p_img/$img_url' alt='$colorName 色,".$row['size']."サイズ'>
                        </a>";
                    }//else{
                        //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
                    //}
                    //画像にサイズと色の説明が出るようにする。
                    if(!in_array($color_size_id, $lastImg)){
                        echo $htmlText;
                        echo $imgText;
                        $lastImg[] = $color_size_id;
                        $htmlText = <<<END
                        <br>
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                        色: $colorName
                        サイズ: $size<br>
                        商品名　　: $productname<br>
                        カテゴリ名: $category_name<br>
                        価格　　　: $price<br>
                        </a>
                        <form action="innerCart.php" method="post">
                            <input type="hidden" name="product_id" value="$product_id">
                            <input type="hidden" name="color_size_id" value="$color_size_id">
                            <button type="submit" name="submit">カートに入れる</button>
                        </form>
                        <hr>
                        END;
                        // 他の情報も必要に応じて表示
                        $count++;
                    }else{
                        echo $imgText;
                    }
                }
                echo $htmlText;
            } else {
                echo "該当する商品がありません。<br>";
            }
        } else {
            die('クエリ実行に失敗しました: ' . $conn->error);
        }
    }
    echo "該当商品が" . $count . "件見つかりました。";
}else if(empty($searchText)){
    echo "検索キーワードを入力してください。<br>";
}else{
    echo "有効な検索キーワードを入力してください。<br>";
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
?>
<!-- <script>スライダー・Jquery</script> -->