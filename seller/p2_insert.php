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

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$sellerName = $_SESSION['sellerName'];
?>

<h1>商品登録画面</h1>
<?php
if(isset($_GET['error'])){
    $error_message = $_GET['error'];
    echo $error_message;
}
?>
<form action="p2Insert.php" method="post" name="form" id="form">
    <p><?=$sellerName?></p>
    <label for="productname" class="p2_label">
        商品名　：
        <input type="text" name="productname" id="productname" placeholder="商品名" required>
    </label><br>
    <label for="view" class="p2_label">
        　概要　：
        <textarea name="view" id="view" cols="25" rows="10" placeholder="概要"></textarea>
    </label><br>
    <label for="quality" class="p2_label">
        　品質　：
        <select name="quality" id="quality" required>
            <option value="" hidden>選択してください</option>
            <option value="新品・未使用">新品・未使用</option>
            <option value="良品">良品</option>
            <option value="やや傷あり">やや傷あり</option>
            <option value="不良">不良</option>
        </select>
    </label><br>

    <label for="big_category" class="p2_label">
        大カテゴリ：
        <select name="big_category" id="big_category">
            <option value="" hidden>選択してください</option>
            <?php 
            $big_sql = "SELECT big_category_id, big_category_name FROM big_category";
            $big_stmt = $conn->query($big_sql);
            if ($big_stmt) {
                while($row = $big_stmt->fetch_assoc()){
                    $big_category_id = $row['big_category_id'];
                    $big_category_name = $row['big_category_name'];
                    echo '<option value="'.$big_category_id.'">'.$big_category_name.'</option>';
                }
            } 
            ?>
        </select>
    </label><br>
    <label for="category" id="categoryLabel" class="p2_label" style="display:none;">
        中カテゴリ：
        <select name="category" id="category">
        </select>
    </label><br>
    <label for="small_category" id="smallCategoryLabel" class="p2_label" style="display:none;">
        小カテゴリ：
        <select name="small_category" id="small_category">
        </select>
    </label><br><br><br>

    <div id="selectBoxesContainer">
        <div id="selector0">    
            <label for="sizeSelect0">商品のサイズ</label>
            <select name="selectorArray[]" id="sizeSelect0" required>
                <option value="" hidden>選択してください</option>
                <option value="FREE">FREE</option>
                <option value="XS">XS</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="2XL">2XL</option>
            </select>
            <!--サイズの削除ボタン設置（２個目以上から）-->
            <!-- 新しいラジオボタンを生成するボタンの設置 -->
            <!-- ラジオボタン挿入 -->
        </div>
        <!-- セレクトボックス挿入。以下同文 -->
    </div><br>
    <hr>
    <button type="button" id="addSelectButton" onclick="addSelectBox()" style="display:none;">サイズを追加</button><br><br>

    <input type="submit" name="register" id="register" value="登録">
</form>

<script>
const form = document.getElementById('form');
const productname = document.getElementById('productname');
const view = document.getElementById('view');
const quality = document.getElementById('quality');
const big_category = document.getElementById('big_category');
const categoryLabel = document.getElementById('categoryLabel');
const category = document.getElementById('category');
const small_category = document.getElementById('small_category');
const smallCategoryLabel = document.getElementById('smallCategoryLabel');


//セレクトボックスの値が選択されたときの処理
//一番最初だけ通る
var selectorArray = document.getElementsByName("selectorArray[]");
const addSelectButton = document.getElementById('addSelectButton');
var flag = true;
selectorArray[0].addEventListener('change',function(){
    if(flag === true){
        addRadioColor(selectorArray[0]);
        addRadio(selectorArray[0]);
        addSelectButton.style.display = "block";
        flag = false;
    }else{
        var selector0 = document.getElementById('selector0');
        var childElements = selector0.children;
        for(var i = childElements.length -1; i >= 3; i--){
            childElements[i].remove();            
        }
        addRadio(selectorArray[0]);
    }
});

function addRadioColor(selectorNumber){
    var selectorId = selectorNumber.id;
    var selectorName = selectorNumber.name;
    //どこのセレクトボックスか判断
    var childSelectId = document.getElementById(selectorId);
    var selector = childSelectId.parentNode;

    var addColorButton = document.createElement('button');
    addColorButton.type = "button";
    addColorButton.id = "addColor"+divRadioCount;
    addColorButton.innerHTML = "カラーを追加";
    selector.appendChild(addColorButton);
    addColorButton.addEventListener('click', function() {
        addRadio(selectorNumber);
    });
}


var divRadioCount = 0;//ラジオボタンと数量の入ったdivの箱の数
//radioボタンが押された時もしくは初めてselectBoxが変更されたとき
function addRadio(selectorNumber){
    var selectorId = selectorNumber.id;
    var selectorName = selectorNumber.name;
    var selectorValue = selectorNumber.value;
    //どこのセレクトボックスか判断
    var childSelectId = document.getElementById(selectorId);
    var selector = childSelectId.parentNode;

    //ラジオボタンの要素の配列
    var radioValues = ["#FFFFFF","#313131","#AAB2BE","#81604C","#E0D1AD","#9ED563","#4DBEE9","#AD8EEF","#FED14C","#F8AFD7","#EF5663","#F98140"];
    var radioOptions = ["ホワイト","ブラック","グレー","ブラウン","ベージュ","グリーン","ブルー","パープル","イエロー","ピンク","レッド","オレンジ"];
    var radioLength = radioValues.length;

    //div生成
    var divRadio = document.createElement('div');
    divRadio.id = "radioContainer"+divRadioCount;
    divRadio.classList.add("radioContainer");
    //ラジオボタン生成
    for(var i=0; i<radioLength; i++){
        var colorRadio = document.createElement('input');
        colorRadio.type = "radio";
        colorRadio.name = "colorArray["+selectorValue+"]["+divRadioCount+"][color]";
        colorRadio.value = radioValues[i];
        colorRadio.id = selectorId+"radio"+divRadioCount+i;//ラベルつけるため
        colorRadio.classList.add("radio"+i);
        if(i === 0){
            colorRadio.required = true;
        }
        divRadio.appendChild(colorRadio);

        var colorSpan = document.createElement('span');
        colorSpan.id 
        divRadio.appendChild(colorSpan);
        
        //radioボタンのラベル生成。
        var radioLabel = document.createElement('label');
        radioLabel.setAttribute('for', selectorId+"radio"+divRadioCount+i);
        radioLabel.innerHTML = radioOptions[i];
        divRadio.appendChild(radioLabel);//(span)
        //span.appendChild(label);

        //ラジオボタンが選択された時のイベントリスナーを追加する場合はここ
    }
    //改行タグ作成。
    var radioBr = document.createElement('br');
    divRadio.appendChild(radioBr);

    //数量のラベル生成
    var piecesLabel = document.createElement('label');
    piecesLabel.setAttribute('for',"pieces"+divRadioCount);
    piecesLabel.innerHTML = "数量";
    divRadio.appendChild(piecesLabel);

    //在庫数の入力フォーム生成
    var pieces = document.createElement('input');
    pieces.type = "text";
    pieces.name = "colorArray["+selectorValue+"]["+divRadioCount+"][pieces]";
    pieces.id = "pieces"+divRadioCount;//ラベルのため
    pieces.placeholder = "数値のみ";
    pieces.required = true;
    divRadio.appendChild(pieces);

    //価格のラベル
    var priceLabel = document.createElement('label');
    priceLabel.setAttribute('for',"price"+divRadioCount);
    priceLabel.innerHTML = "価格";
    divRadio.appendChild(priceLabel);

    //価格の入力フォーム生成
    var price = document.createElement('input');
    price.type = "text";
    price.name = "colorArray["+selectorValue+"]["+divRadioCount+"][price]";
    price.id = "price"+divRadioCount;
    price.placeholder = "数値のみ　単位：円/着"
    price.required = true;
    divRadio.appendChild(price);

    var radioBr = document.createElement('br');
    divRadio.appendChild(radioBr);

    //削除ボタン生成
    var deleteRadio = document.createElement('button');
    deleteRadio.type = "button";
    deleteRadio.id = "delete"+divRadioCount;
    deleteRadio.name = selectorName+"optionArray["+divRadioCount+"]delete";
    deleteRadio.innerHTML = "削除";
    divRadio.appendChild(deleteRadio);
    deleteRadio.addEventListener('click', function(){
        divRadio.remove();
    });

    selector.appendChild(divRadio);
    divRadioCount++;//radioContainerのインクリメント
};


//サイズの追加ボタンを押された後の処理。
//buttonのid取得済み
var divSelectCount = 1;
function addSelectBox(){
    //セレクトボックスの追加
    const selectBoxesContainer = document.getElementById('selectBoxesContainer');

    var divSelect = document.createElement('div');
    divSelect.id = "selector"+divSelectCount;
    selectBoxesContainer.appendChild(divSelect);

    var selectHr = document.createElement('hr');
    divSelect.appendChild(selectHr);

    var selectLabel = document.createElement('label');
    selectLabel.setAttribute('for','sizeSelect'+divSelectCount);
    selectLabel.innerHTML = "商品のサイズ";
    divSelect.appendChild(selectLabel);

    var selectBox = document.createElement('select');
    selectBox.name = 'selectorArray[]';
    selectBox.id = 'sizeSelect'+divSelectCount;
    selectBox.required = true;
    divSelect.appendChild(selectBox);

    //optionの追加
    //hidden枠
    var selectOption = document.createElement('option');
    selectOption.innerHTML = "選択してください";
    selectOption.value = "";
    selectOption.selected = true;
    selectOption.hidden = true;
    selectBox.appendChild(selectOption);
    //選択肢
    const selectOptions = ['FREE','XS','S','M','L','XL','2XL'];
    for(var i = 0; i < selectOptions.length; i++){
        var selectOption = document.createElement('option');
        selectOption.value = selectOptions[i];
        selectOption.innerHTML = selectOptions[i];
        selectBox.appendChild(selectOption);
    }

    //サイズ削除ボタンの追加
    var deleteSelect = document.createElement('button');
    deleteSelect.type = "button";
    deleteSelect.id = "deleteSelect"+divSelectCount;
    deleteSelect.name = "selectorArray[]delete";
    deleteSelect.innerHTML = "サイズを削除";
    divSelect.appendChild(deleteSelect);
    deleteSelect.addEventListener('click',function(){
        divSelect.remove();
        if(selectBox.id === 'sizeSelect'+(divSelectCount-1)){
            addSelectButton.style.display = "block";
            divSelectCount--;
        }
    });

    selectBox.setAttribute("data-custom-attribute", "true");//判定(trueは文字列)
    selectBox.addEventListener('change',function(){
        var customAttribute = selectBox.getAttribute("data-custom-attribute");
        if(customAttribute === "true"){
            addRadioColor(selectBox);
            addRadio(selectBox);
            addSelectButton.style.display = "block";
            selectBox.setAttribute("data-custom-attribute", "false");
        }else{
            var childElements = divSelect.children;
            console.log(childElements);
            for(var i = childElements.length - 1; i >= 5; i--){
                childElements[i].remove();
            }
            addRadio(selectBox);
        }
    });

    addSelectButton.style.display = "none";
    divSelectCount++;
}

//カテゴリの処理
var b_id;
big_category.addEventListener('change', (e) => {
    var num = big_category.selectedIndex;
    b_id = big_category.options[num].value;

    const formData = new FormData();
    formData.append('big_category', b_id);
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'p2_big.php', true);
    xhr.send(formData);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response) {
                        // 成功した場合の処理を記述
                        category.innerHTML = '<option value="" hidden>選択してください</option>';//+=は前の選択されてたぶん残る
                        response.forEach(function(row) {
                            category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        categoryLabel.style.display = "block";
                    } else {
                        console.error("Invalid or empty response data");
                    }
                } catch (error) {
                    console.error("Error parsing JSON response: " + error.message);
                }
            } else {
                console.error("Error: " + xhr.status);
            }
        }
    }
});

category.addEventListener('change', (e) => {
    var num = category.selectedIndex;
    var c_id = category.options[num].value;

    const formData = new FormData();
    formData.append('category', c_id);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'p2_cate.php',true);
    xhr.send(formData);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4){
            if(xhr.status === 200){
                try{
                    const response = JSON.parse(xhr.responseText);
                    if(response){
                        small_category.innerHTML = '<option hidden>選択してください</option>';
                        response.forEach(function(row){
                            small_category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        if(!(b_id === "1" && c_id === "19" || b_id === "2" && c_id === "33")){
                            smallCategoryLabel.style.display = "block";
                        }
                    }else{
                        console.error("Error parsing JSON response data");
                    }
                }catch(error){
                    console.error("Error parsing JSON response: " + error.message);
                }
            }else{
                console.error("Error: " + xhr.status);
            }
        }
    }
});


//最終フォーム送信時に登録内容を確認する
form.addEventListener('submit', function(event) {
    event.preventDefault(); // フォームのデフォルトの送信をキャンセル
    
    // 入力フォームの値を取得
    let productnameValue = productname.value;
    let viewValue = "";
    if(view && view.value !== ""){
        viewValue = view.value;
    }

    let qualityValue = quality.options[quality.selectedIndex].value;

    let bigCategoryText = "";
    if(big_category && big_category.selectedIndex !== 0){
        console.log(big_category.selectedIndex);
        bigCategoryText = big_category.options[big_category.selectedIndex].text;
    }

    let categoryText = "";
    if(category && category.selectedIndex !== -1 && category.selectedIndex !== 0){
        console.log(category.selectedIndex);
        categoryText = category.options[category.selectedIndex].text;
    }

    let smallCategoryText = "";
    if(small_category && small_category.selectedIndex !== -1 && small_category.selectedIndex !== 0){
        smallCategoryText = small_category.options[small_category.selectedIndex].text
    }
    
    let selectorArray = document.getElementsByName("selectorArray[]");
    
    let regexPattern = /colorArray\[[A-Z]+\]\[\d+\]\[color\]/;
    let elements = document.querySelectorAll('[name^="colorArray["][name$="][color]"]');
    let matchedElements = Array.from(elements).filter(element => regexPattern.test(element.name));

    let regexPieces = /colorArray\[[A-Z]+\]\[\d+\]\[pieces\]/;
    let piecesElements = document.querySelectorAll('[name^="colorArray["][name$="][pieces]"]');
    let matchedPieces = Array.from(piecesElements).filter(element => regexPieces.test(element.name));
    
    let regexPrice = /colorArray\[[A-Z]+\]\[\d+\]\[price\]/;
    let priceElements = document.querySelectorAll('[name^="colorArray["][name$="][price]"]');
    let matchedPrice = Array.from(priceElements).filter(element => regexPrice.test(element.name));


    // サイズとカラーの選択肢をテキストで取得
    let sizesAndColors = "";
    var jFlag = 0;
    for (var i = 0; i < selectorArray.length; i++) {
        var variable = selectorArray[i].value;
        sizesAndColors += "\nサイズ: " + variable;
        var regexPatternString = `colorArray\\[${variable}\\]\\[\\d+\\]\\[color\\]`;
        var regexPatternRadio = new RegExp(regexPatternString);
        var matchRadioElement = Array.from(matchedElements).filter(element => regexPatternRadio.test(element.name));
        
        var piecesName = `colorArray\\[${variable}\\]\\[\\d+\\]\\[pieces\\]`;
        var regexPatternPieces = new RegExp(piecesName);
        var matchPiecesElement = Array.from(matchedPieces).filter(element => regexPatternPieces.test(element.name));

        var priceName = `colorArray\\[${variable}\\]\\[\\d+\\]\\[price\\]`;
        var regexPatternPrice = new RegExp(priceName);
        var matchPriceElement = Array.from(matchedPrice).filter(element => regexPatternPrice.test(element.name))
        for(var k = 0; k < matchPiecesElement.length; k++){
            for (var j = jFlag; j < matchRadioElement.length; j++) {
                if (matchRadioElement[j].checked) {
                    sizesAndColors += ", カラー: " + matchRadioElement[j].nextElementSibling.textContent;
                    jFlag = j + 1;
                    break;
                }
            }
            sizesAndColors += ", 在庫数: " + matchPiecesElement[k].value;
            sizesAndColors += ", 価格: " + matchPriceElement[k].value;
        }
        jFlag = 0;
    }

    // 入力内容を確認するダイアログボックスを表示
    var confirmationMessage = "商品名: " + productnameValue + "\n概要: " + viewValue + "\n品質: " + qualityValue +
        "\n大カテゴリ: " + bigCategoryText + "\n中カテゴリ: " + categoryText +
        "\n小カテゴリ: " + smallCategoryText + "\nサイズとカラー: " + sizesAndColors;

    if (confirm(confirmationMessage)) {
        // ダイアログでOKがクリックされた場合、フォームを送信
        this.submit();
    } else {
        // ダイアログでキャンセルがクリックされた場合、何もしない
    }
});
</script>
