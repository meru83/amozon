<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post" class="normal">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <link rel="stylesheet" href="css/search_results.css">
    <title>商品検索</title>
    <style>
    .swiper {
        width: 300px;
        max-width: 100%; 
        height: 200px; 
    }
    .swiper-slide img {
        width: 300px;
        height: 200px;
    }
</style>
</head>

<body>
<div id="header" class="header">
    <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
    <h1 class="h1_White">検索結果</h1>
    <?=$foo2?>
</div>
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href="user_top.php"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                    <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href="favoriteProduct.php"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                    <li class="menu-item"><a href="buyHistory.php"><img src="img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                    <li class="menu-item"><a href="user_profile.php"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                </ul>
            </div>
        </div>
        
        <div class="right-content">
            <form id="form" action="search_results.php" method="GET">
            <div class="flexBox">
                <label for="search">商品を検索</label>
                <input type="text" id="search" name="search">
                <button type="submit" id="submit" class="btn-img"></button>
            </div>
            </form>
<?php
// データベース接続
include "db_config.php";

// 検索キーワードを取得し、空白で分割
$searchText = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = "INSERT INTO search(searchText) VALUES(?)";//要修正
$searchStmt = $conn->prepare($searchSql);
$searchStmt->bind_param("s", $searchText);
$searchStmt->execute();
$count = 0;

$sizeArray = array('FREE','フリー','フリーサイズ','ふりー','ふりーさいず','FREEサイズ','FREEさいず','XS','XSサイズ','XSさいず','S','S','Sサイズ','Sさいず','えす','えすさいず','M','Mサイズ','Mさいず','えむ','えむさいず','L','Lサイズ','Lさいず','える','えるさいず','XL','XLサイズ','XLさいず','2XL','2XLサイズ','2XLさいず');
$colorArray = array('ホワイト','白','白色','白っぽい','white','しろ','しろいろ','黒','黒色','ブラック','黒っぽい','black','くろ','くろいろ','グレー','灰色','灰','灰っぽい','gray','はい','はいいろ','ブラウン','茶','茶色','茶っぽい','brown','ちゃ','ちゃいろ','ベージュ','オフホワイト','クリーム色','クリームイエロー','薄い黄色','薄黄色','くりーむいろ','beige','グリーン','緑','緑色','深緑','みどり','みどりいろ','ふかみどり','green','ブルー','青色','青','あお','あおいろ','blue','パープル','紫','紫色','むらさき','むらさきいろ','purple','イエロー','黄色','黄','きいろ','yellow','ピンク','ピンク色','ピンクいろ','ぴんくいろ','ぴんく','pink','レッド','赤','赤色','red','あか','あかいろ','オレンジ','オレンジ色','オレンジいろ','おれんじ','おれんじいろ','orange');
$qualityArray = array('新品', '未使用', '新品未使用', '新品、未使用',  'しんぴん' ,'みしよう', '中古', '中古品', 'ちゅうこ','良品', 'やや傷あり', '不良', '傷あり');
//検索された文字列が品質のみか否かのif文
if(!empty($searchText)  && !in_array($searchText,$qualityArray) && !in_array($searchText,$colorArray) && !in_array($searchText,$sizeArray)){
    // if(!empty($searchText)  && !in_array($searchText,$qualityArray)){
    if(preg_match('/[|]+/u',$searchText)){
        //`|`があったらOR検索として扱いそこで区切る。
        $orKeywords = preg_split('/[|]+/u', $searchText);
    }else{
        //`|`がない場合検索文字列をそのまま扱う。
        $orKeywords = array($searchText);
    }

    echo "<div class='all'>";//全体
    foreach($orKeywords as $orKeyword){
        $conditions = array();
        $qualityConditions = array();
        $colorConditions = array();
        $sizeCondition = array();
        $keywords = preg_split('/\s+/u',$orKeyword);
        foreach ($keywords as $keyword) {
            //品質で検索された場合品質の項目を品質の配列($qualityConditions[])に格納
            if(in_array($keyword,$qualityArray)){
                if (in_array($keyword, ['中古', '中古品', 'ちゅうこ'])) {
                    $qualityConditions[] = "(p.quality = '良品' OR p.quality = 'やや傷あり' OR p.quality = '不良')";
                }else if(in_array($keyword, ['新品', '未使用', '新品未使用', 'しんぴん' ,'みしよう'])) {
                    $qualityConditions[] = "p.quality = '新品・未使用'";
                }else if(in_array($keyword, ['傷あり'])) {
                    $qualityConditions[] = "p.quality = 'やや傷あり'";
                }else{
                    $qualityConditions[] = "p.quality = '$keyword'";
                }
            }else if(in_array($keyword,$colorArray)){
                //色
                if(in_array($keyword,['ホワイト','白','白色','白っぽい','white','しろ','しろいろ'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#FFFFFF'";
                }else if(in_array($keyword,['黒','黒色','ブラック','黒っぽい','black','くろ','くろいろ'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#313131'";
                }else if(in_array($keyword,['グレー','灰色','灰','灰っぽい','gray','はい','はいいろ'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#AAB2BE'";
                }else if(in_array($keyword,['ブラウン','茶','茶色','茶っぽい','brown','ちゃ','ちゃいろ'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#81604C'";
                }else if(in_array($keyword,['ベージュ','オフホワイト','クリーム色','クリームイエロー','薄い黄色','薄黄色','くりーむいろ','beige'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#E0D1AD'";
                }else if(in_array($keyword,['グリーン','緑','緑色','深緑','みどり','みどりいろ','ふかみどり','green'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#9ED563'";
                }else if(in_array($keyword,['ブルー','青色','青','あお','あおいろ','blue'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#4DBEE9'";
                }else if(in_array($keyword,['パープル','紫','紫色','むらさき','むらさきいろ','purple'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#AD8EEF'";
                }else if(in_array($keyword,['イエロー','黄色','黄','きいろ','yellow'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#FED14C'";
                }else if(in_array($keyword,['ピンク','ピンク色','ピンクいろ','ぴんくいろ','ぴんく','pink'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#F8AFD7'";
                }else if(in_array($keyword,['レッド','赤','赤色','red','あか','あかいろ'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#EF5663'";
                }else if(in_array($keyword,['オレンジ','オレンジ色','オレンジいろ','おれんじ','おれんじいろ','orange'])){
                    //sql
                    $colorConditions[] = "s.color_code = '#F98140'";
                }else{
                    //sql
                    $colorConditions[] = "s.color_code = '$keyword'";
                }
            }else if(in_array($keyword,$sizeArray)){
                //サイズ
                if(in_array($keyword,['FREE','フリー','フリーサイズ','ふりー','ふりーさいず','FREEサイズ','FREEさいず'])){
                    $sizeCondition[] = "s.size = 'FREE'";
                }else if(in_array($keyword,['XS','XSサイズ','XSさいず'])){
                    $sizeCondition[] = "s.size = 'XS'";
                }else if(in_array($keyword,['S','S','Sサイズ','Sさいず','えす','えすさいず','エス','エスサイズ'])){
                    $sizeCondition[] = "s.size = 'S'";
                }else if(in_array($keyword,['M','Mサイズ','Mさいず','えむ','えむさいず','エム','エムサイズ'])){
                    $sizeCondition[] = "s.size = 'M'";
                }else if(in_array($keyword,['L','Lサイズ','Lさいず','える','えるさいず','エル','エルサイズ'])){
                    $sizeCondition[] = "s.size = 'L'";
                }else if(in_array($keyword,['XL','XLサイズ','XLさいず'])){
                    $sizeCondition[] = "s.size = 'XL'";
                }else if(in_array($keyword,['2XL','2XLサイズ','2XLさいず'])){
                    $sizeCondition[] = "s.size = '2XL'";
                }else{
                    $sizeCondition[] = "s.size = '$keyword'";
                }
            }else{
                //品質以外の検索はここへ入る
                //マッチ文字数の多い文字を検索上位に表示させたい
                //初っ端のORのところをANDにして商品名での検索がないときだけカテゴリのみの検索ができるようにしたい
                $conditions[] = "(p.productname LIKE '%$keyword%' OR 
                                p.big_category_id IN (SELECT big_category_id FROM big_category WHERE big_category_name LIKE '%$keyword%') OR
                                p.category_id IN (SELECT category_id FROM category WHERE category_name LIKE '%$keyword%') OR
                                p.small_category IN (SELECT small_category FROM small_category WHERE small_category_name LIKE '%$keyword%'))";
            }
        }
        if(!empty($qualityConditions)){
            $conditions[] = "(" . implode(' OR ', $qualityConditions) . ")";
        }
        if(!empty($colorConditions)){
            $conditions[] = "(" . implode(' OR ', $colorConditions) . ")";
        }
        if(!empty($sizeCondition)){
            $conditions[] = "(" . implode(' OR ', $sizeCondition) . ")";
        }
        $andConditions = implode(' AND ', $conditions);

        // 検索結果を取得するクエリを作成
        $sql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.seller_id, p.big_category_id, p.category_id, p.small_category, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, c.category_name, i.img_url, f.user_id AS favorite_product
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                LEFT JOIN favorite f ON (p.product_id = f.product_id) && (s.color_size_id = f.color_size_id) && (f.user_id = ?)
                WHERE $andConditions && s.service_status = true";

        echo "クエリ：".$sql."<br>";

        // クエリを実行
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $htmlText = "";
        // クエリの実行結果を確認
        if ($result) {    
            // 検索結果を表示
            if ($result->num_rows > 0) {
                $lastImg = array();
                echo '<div class="productAll none">';
                echo '<div class="imgAll swiper">';
                echo '<div class="swiper-wrapper">';
                while ($row = $result->fetch_assoc()) {
                    $imgText = null;
                    $colorCode = $row['color_code'];
                    $colorName = getColor($conn, $colorCode);
                    $product_id = $row['product_id'];
                    $size = $row['size'];
                    $pieces = $row['pieces'];
                    $productname = $row['productname'];
                    $category_name = !is_null($row['category_name'])?$row['category_name']:"";
                    $price = $row['price'];
                    $commaPrice = number_format($price);
                    $color_size_id = $row['color_size_id'];
                    $img_url = is_null($row['img_url'])?null:$row['img_url'];
                    $favorite_product = is_null($row['favorite_product'])?null:$row['favorite_product'];
                    if(!is_null($img_url)){
                        $imgText = <<<END
                        <div class="swiper-slide">
                            <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">
                                <img src="seller/p_img/$img_url">
                            </a>
                        </div>
                        END;
                    }else{
                        $imgText = <<<END
                        <div class="swiper-slide">
                            <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">
                                <img src="img/noImg.jpg">
                            </a>
                        </div>
                        END;
                    }
                    //画像にサイズと色の説明が出るようにする。
                    if(!in_array($color_size_id, $lastImg)){
                        echo <<< HTML
                            </div>
                            <!-- If we need pagination -->
                            <div class="swiper-pagination"></div>

                            <!-- If we need navigation buttons -->
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>

                            </div>
                        HTML;
                        echo $htmlText;
                        echo '</div>';
                        echo '<div class="productAll">';
                        echo '<div class="imgAll swiper">';
                        echo '<div class="swiper-wrapper">';
                        echo $imgText;
                        $lastImg[] = $color_size_id;
                        $htmlText = <<<END
                        <br>
                        <div class="setumei">
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                        <!----商品名　　:------> $productname<br>
                        <!----カテゴリ名: $category_name<br>------>
                        <!---価格　　　: ------>¥$commaPrice<br>
                        <!---サイズ: ------>$size サイズ<br>
                        <!---色: ---->$colorName
                        </a>
                        </div>
                        END;
                        //$favorite_product null か $user_id
                        if(!($favorite_product === null) && isset($_SESSION['user_id'])){
                            //ログイン済みでお気に入り商品があった場合
                            $htmlText .= <<<END
                            <label class="checkHeart" for="favorite$count">
                                <input type="checkbox" id="favorite$count" checked>
                                <span class="spanHeart"></span>
                            </label>
                            END;
                        }else if(isset($_SESSION['user_id'])){
                            //ログインはしてるけどお気に入り商品ではない
                            $htmlText .= <<<END
                            <label class="checkHeart" for="favorite$count">
                                <input type="checkbox" id="favorite$count">
                                <span class="spanHeart"></span>
                            </label>
                            END;
                        }else{
                            //未ログイン状態のとき
                            $htmlText .= <<<END
                            <button type="button" class="heartBtn" onclick="heartButton()"><img src="img/heart2.png" style="height: 100%;"></button>
                            END;
                        }
                        if($pieces > 0){
                            $htmlText .= <<<END
                            <form action="innerCart.php" method="post">
                                <input type="hidden" id="product_id$count" name="product_id" value="$product_id">
                                <input type="hidden" id="color_size_id$count" name="color_size_id" value="$color_size_id">
                                <button type="submit" name="submit" class="cart_btn">カートに入れる</button>
                            </form>
                            END;
                        }else{
                            $htmlText .= "<p class='sen'>カートに入れる</p>";//商品がないときは灰色のただの文字列にしてカートにする<<<<<<<<<  CSS  >>>>>>>>>>
                        }
                        // 他の情報も必要に応じて表示
                        $count++;
                    }else{
                        echo $imgText;
                    }
                }
                echo <<< HTML
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>

                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>

                </div>
                HTML;
                echo $htmlText;
                echo '</div>';
                //ここ
                echo <<< HTML
                    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
                    <script>
                        const swiper = new Swiper('.swiper', {
                            // Optional parameters
                            direction: 'horizontal',
                            loop: true,
                            speed: 1000,
                            effect: 'coverflow',

                            // // If we need pagination
                            // pagination: {
                            //     el: '.swiper-pagination',
                            //     type: 'progressbar',
                            // },

                            // Navigation arrows
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },

                            // // And if we need scrollbar
                            // scrollbar: {
                            //     el: '.swiper-scrollbar',
                            //     hide:true,
                            // },
                        });
                    </script>
                    HTML;
            } else {
                echo "該当する商品がありません。<br>";
            }
        } else {
            die('クエリ実行に失敗しました: ' . $conn->error);
        }
    }
    echo "</div>";//全体
    echo "該当商品が" . $count . "件見つかりました。";
    echo '<a href="#" id="topButton">トップへ</a>';
    echo "</div>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
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
<!-- <script>スライダー・Jquery</script> -->
<script>
const search = document.getElementById('search');
const submit = document.getElementById('submit');
const form = document.getElementById('form');
form.addEventListener('submit',(e) => {
    let searchValue = search.value;
    let str = searchValue.replace(/\s+/g, "");
    if(str === null || str === ""){
        e.preventDefault();
        return false;
    }else{
        return true;
    }
});

function heartButton(){
    alert("お気に入り登録にはログインを完了させてください。");
}
</script>
