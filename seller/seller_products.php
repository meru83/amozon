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
$seller_name = $_SESSION['sellerName'];

echo "<h1>登録済み商品一覧</h1>";
echo "<h2>$seller_name 様</h2>";
echo "<div id='errorMessage'></div>";

//alertで表示に変更
// if(isset($_GET['errorMessage'])){
//     $errorMessage = $_GET['errorMessage'];
//     echo $errorMessage."<br>";
// }

$selectSql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.quality, s.color_code, s.size, b.big_category_name, c.category_name, sc.small_category_name, i.img_url 
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.seller_id = ? && s.service_status = true";

$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("s",$seller_id);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();

$htmlText = "";
$productText = "";
$divStart = "";
$formFlag = false;
$checkFlag = true;
$count = 0;
if($selectResult && $selectResult->num_rows > 0){
    $lastImg = array();
    $colorArray = array();
    $color_codeArray = array();
    $sizeArray = array();
    $productArray = array();
    echo "<div id='div$count'>";
    while ($row = $selectResult->fetch_assoc()) {
        $imgText = null;
        $product_id = $row['product_id'];
        $productname = $row['productname'];
        $view = isset($row['view'])?$row['view']:"未登録";
        $quality = $row['quality'];
        $create_at = $row['create_at'];
        $color_code = $row['color_code'];
        $colorName = getColor($conn, $color_code);
        $size = $row['size'];
        $big_category_name = !is_null($row['big_category_name'])?$row['big_category_name']:"未登録";
        $category_name = !is_null($row['category_name'])?$row['category_name']:"未登録";
        $small_category_name = !is_null($row['small_category_name'])?$row['small_category_name']:"未登録";
        $img_url = is_null($row['img_url'])?null:$row['img_url'];
        $countId = $count - 1;
        if(!is_null($img_url)){
            $imgText = "
            <!---<a href='edit.php?product_id=$product_id>--->
            <img src='p_img/$img_url'>
            <!---</a>----->";
        }//else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
        //}
        
        if(in_array($product_id,$lastImg)){
            for($i = 0; $i < count($colorArray); $i++){
                if($colorArray[$i] === $colorName && $sizeArray[$i] === $size){
                    $checkFlag = false;
                }
            }
            if($checkFlag === true){
                $colorArray[] = $colorName;
                $color_codeArray[] = $color_code;
                $sizeArray[] = $size;
            }else{
                $checkFlag = true;
            }
        }
        if($formFlag === true){
            $productText = <<<END
            <form action="edit.php" method="post">
                <select id="select$countId" required>
                    <option value="" hidden>選択してください</option>
            END;
            for($i = 0; $i < count($colorArray); $i++){
                $productText .= "<option value='$color_codeArray[$i]|$sizeArray[$i]'>$colorArray[$i] - $sizeArray[$i]</option>";
            }
            $productText .= <<<END
                </select>
                <input type="hidden" value="$product_id">
                <input type="submit" value="登録内容変更">
            </form>
            <div id="addColorSizeDiv$countId">
            <!------ここに色とカラー追加のフォームを作る------->
            </div>
            <input type="hidden" id="$countId" value="$lastImg[$countId]">
            <button type="button" name="aCS" id="aCS$countId" onclick="addColorSize($countId)">カラー・サイズの追加</button>
            <button type="button" onclick="deleteProducts($countId)">商品の削除</button>
            <hr>
            </div>
            END;
            $divStart = "<div id= 'div$count'>";
        }else{
            $formFlag = true;
        }

        //違う商品になったタイミング
        if(!in_array($product_id, $lastImg)){
            echo $htmlText;

            //form
            echo $productText;
            $colorArray = array();
            $color_codeArray = array();
            $sizeArray = array();
            $colorArray[] = $colorName;
            $color_codeArray[] = $color_code;
            $sizeArray[] = $size;

            echo $divStart;
            echo $imgText;
            $lastImg[] = $product_id;
            $htmlText = <<<END
            <br>
            <br>
            商品名　　: $productname<br>
            カテゴリ名: $big_category_name - $category_name - $small_category_name<br>
            概要　　　: $view<br>
            品質　　　: $quality<br>
            出品日　　: $create_at<br>
            <br>
            END;
            // 他の情報も必要に応じて表示
            $count++;
        }else{
            echo $imgText;
        }
    }
    echo $htmlText;
    $countId = $count - 1;
    echo <<<END
            <form action="edit.php" method="post">
                <select id="select$countId" name="colorSize" required>
                    <option value="" hidden>選択してください</option>
            END;
            for($i = 0; $i < count($colorArray); $i++){
                echo "<option value='$colorArray[$i]|$sizeArray[$i]'>$colorArray[$i] - $sizeArray[$i]</option>";
            }
            echo <<<END
                </select>
                <input type="hidden" name="product_id" value="$product_id">
                <input type="submit" value="登録内容変更">
            </form>
            <div id="addColorSizeDiv$countId">
            <!------ここにフォーム作る------->
            </div>
            <input type="hidden" id="$countId" value="$lastImg[$countId]">
            <button type="button" name="aCS" id="aCS$countId" onclick="addColorSize($countId)" style="display:block">カラー・サイズの追加</button>
            <button type="button" onclick="deleteProducts($countId)">商品の削除</button>
            <hr>
            </div>
            END;
    echo "</div>";
    echo "登録商品は".$count."件です。";
}else{
    echo "登録されている商品がありません。";
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
<script>
const errorMessageDiv = document.getElementById('errorMessage');
function deleteProducts(deleteCount){
    var inputId = document.getElementById(deleteCount);
    var product_id = inputId.value;
    const formData = new FormData();
    formData.append('product_id',product_id);

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            var divCount = document.getElementById('div'+deleteCount);
            divCount.remove();

            alert("商品の削除に成功しました。");
        }
        if(xhr.status < 200 && xhr.status >= 300){
            alert("登録商品の削除に失敗しました。");
        }
    }
    xhr.open('POST','deleteProducts.php',true);
    xhr.send(formData);
}


const radioValues = ["#FFFFFF","#313131","#AAB2BE","#81604C","#E0D1AD","#9ED563","#4DBEE9","#AD8EEF","#FED14C","#F8AFD7","#EF5663","#F98140"];
const radioOptions = ["ホワイト","ブラック","グレー　","ブラウン","ベージュ","グリーン","ブルー　","パープル","イエロー","ピンク　","レッド　","オレンジ"];
const selectOptions = ['FREE','XS','S','M','L','XL','2XL'];
var radioLength = radioValues.length;

function addColorSize(addCount){
    var addDiv = document.getElementById('addColorSizeDiv'+addCount);
    var aCS = document.getElementsByName('aCS');
    for(let i = 0; i < (aCS.length); i++){
        aCS[i].style.display = "none";
    }

    for(var i=0; i<radioLength; i++){
        var colorRadio = document.createElement('input');
        colorRadio.type = "radio";
        colorRadio.name = "color";
        colorRadio.id = "radio"+i;
        colorRadio.value = radioValues[i];
        if(i === 0){
            colorRadio.required = true;
        }
        addDiv.appendChild(colorRadio);

        //radioボタンのラベル生成。
        var radioLabel = document.createElement('label');
        radioLabel.setAttribute('for', 'radio'+i);
        radioLabel.innerHTML = radioOptions[i];
        addDiv.appendChild(radioLabel);
    }

    
    var radioBr = document.createElement('br');
    addDiv.appendChild(radioBr);

    var selectLabel = document.createElement('label');
    selectLabel.setAttribute('for','sizeSelect');
    selectLabel.innerHTML = "商品のサイズ";
    addDiv.appendChild(selectLabel);

    var selectBox = document.createElement('select');
    selectBox.name = 'size';
    selectBox.id = 'sizeSelect';
    selectBox.required = true;
    addDiv.appendChild(selectBox);

    //optionの追加
    //hidden枠
    var selectOption = document.createElement('option');
    selectOption.innerHTML = "選択してください";
    selectOption.value = "";
    selectOption.selected = true;
    selectOption.hidden = true;
    selectBox.appendChild(selectOption);

    for(var i = 0; i < selectOptions.length; i++){
        var selectOption = document.createElement('option');
        selectOption.value = selectOptions[i];
        selectOption.innerHTML = selectOptions[i];
        selectBox.appendChild(selectOption);
    }

    var selectBr = document.createElement('br');
    addDiv.appendChild(selectBr);

    var piecesLabel = document.createElement('label');
    piecesLabel.setAttribute('for','pieces');
    piecesLabel.innerHTML = "数量";
    addDiv.appendChild(piecesLabel);

    var inputPieces = document.createElement('input');
    inputPieces.type = "text";
    inputPieces.name = "pieces";
    inputPieces.id = "pieces";
    inputPieces.placeholder = "数量";
    inputPieces.required = true;
    addDiv.appendChild(inputPieces);

    var afterPiecesBr = document.createElement('br');
    addDiv.appendChild(afterPiecesBr);

    var priceLabel = document.createElement('label');
    priceLabel.setAttribute('for','price');
    priceLabel.innerHTML = "価格";
    addDiv.appendChild(priceLabel);

    var inputPrice = document.createElement('input');
    inputPrice.type = "text";
    inputPrice.name = "price";
    inputPrice.id = "price";
    inputPrice.required = true;
    inputPrice.placeholder = "価格";
    addDiv.appendChild(inputPrice);

    var afterPriceBr = document.createElement('br');
    addDiv.appendChild(afterPriceBr);

    var submitButton = document.createElement('button');
    submitButton.type = "button";
    submitButton.innerHTML = "追加";
    addDiv.appendChild(submitButton);
    submitButton.addEventListener('click',function(){
        //関数化する↓
        var sendProductElement = document.getElementById(addCount);
        var sendProductValue = sendProductElement.value;
        var piecesElement = document.getElementById('pieces');
        var priceElement = document.getElementById('price');
        var piecesValue = piecesElement.value;
        var priceValue = priceElement.value;
        var num = selectBox.selectedIndex;
        var sizeValue = selectBox.options[num].value;
        var colorRadioLength = document.getElementsByName('color');
        var colorLen = colorRadioLength.length;
        // console.log(colorLen);
        var colorValue = '';
        for(let i = 0; i < colorLen; i++){
            if(colorRadioLength.item(i).checked){
                colorValue = colorRadioLength.item(i).value;
                // console.log(colorValue);
            }
        }

        const formData = new FormData();
        formData.append('product_id',sendProductValue);
        formData.append('size',sizeValue);
        formData.append('color',colorValue);
        formData.append('pieces', piecesValue);
        formData.append('price', priceValue);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'addColorSize.php', true);
        xhr.send(formData);

        xhr.onreadystatechange = function() {
            if(xhr.readyState === 4 && xhr.status === 200){
                try{
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(function(row) {
                        if(row.error_message === true){
                            // addDiv の子ノードをすべて削除
                            while (addDiv.firstChild) {
                                addDiv.removeChild(addDiv.firstChild);
                            }
                            for(let i = 0; i < (aCS.length); i++){
                                aCS[i].style.display = "block";
                            }
                            //selectにサイズ追加
                            var index = radioValues.indexOf(colorValue);
                            var selectI = document.getElementById('select' + addCount);
                            var addselectOption = document.createElement('option');
                            addselectOption.value = colorValue+'|'+sizeValue;
                            addselectOption.innerHTML = radioOptions[index] + ' - ' + sizeValue;
                            selectI.appendChild(addselectOption);

                            alert("カラー・サイズの追加が完了しました。");
                        }else{
                                alert(row.error_message);
                        }
                    });
                }catch (error) {
                    console.error("Error parsing JSON response:", error);
                    alert("リクエストが失敗しました。");
                }
            }
        }
    });
}
</script>