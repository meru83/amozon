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
<link rel="stylesheet" href="../css/p2style.css">
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">商品登録画面</h1>
        <?=$foo?>
    </div>

    <div class="Amozon-container">

    <!-- Left Side Menu -->
    <div class="Amozon-container">
    <!-- Left Side Menu -->
    <div class="left-menu">
        <div>
            <ul class="menu-list">
                <li class="menu-item-logo"><a href=""><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                <li class="menu-item"><a href="seller_top.php"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                <li class="menu-item"> <a href="p2_insert.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                <li class="menu-item"> <a href="seller_products.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
                <?php
                if(isset($_SESSION['seller_id'])){
                    echo '<li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                }else{
                    echo '<li class="menu-item"><a href="seller.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                }
                ?>
                <li class="menu-item"><a href="seller_home.php"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                <!--log--->
            </ul>
        </div>
        <div>
            <ul class="menu-list-bottom">
            <li class="menu-item"><a href="../py/rireki.php"><img src="../img/home.png" class="logo"><span class="menu-item-text">売上管理</span></a></li>

            </ul>
        </div>
    </div>
    
    <div class="right-content">

    <?php
    if(isset($_GET['error'])){
        $error_message = $_GET['error'];
        echo $error_message;
    }
    ?>
<form action="p2Insert.php" method="post" name="form" id="form">
    <p><?=$sellerName?></p>
    <label for="productname" class="p2_label">
        商品名
        <input type="text" name="productname" id="productname" class="styleTextBox" placeholder="商品名" required>
    </label><br>
    <label for="view" class="p2_label">
        概要
        <textarea name="view" id="view" class="styleTextArea" cols="25" rows="10" placeholder="概要"></textarea>
    </label><br>
    <label for="quality" class="p2_label">
        品質
        <select name="quality" id="quality" class="styleSelect" required>
            <option value="" hidden>選択してください</option>
            <option value="新品・未使用">新品・未使用</option>
            <option value="良品">良品</option>
            <option value="やや傷あり">やや傷あり</option>
            <option value="不良">不良</option>
        </select>
    </label><br>

    <label for="big_category" class="p2_label">
        大カテゴリ：
        <select name="big_category" id="big_category" class="styleSelect">
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
        中カテゴリ
        <select name="category" id="category" class="styleSelect">
        </select>
    </label><br>
    <label for="small_category" id="smallCategoryLabel" class="p2_label" style="display:none;">
        小カテゴリ
        <select name="small_category" id="small_category" class="styleSelect">
        </select>
    </label><br><br><br>

    <div id="selectBoxesContainer">
        <div id="selector0">    
            <label for="sizeSelect0">商品のサイズ</label>
            <select name="selectorArray[]" id="sizeSelect0" class="styleSelect" required>
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

    <input type="submit" name="register" id="register" class="register"  value="登録">
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

//Enterで下に行く処理
form.addEventListener('keydown', (e) => {
    if (e.key === 'Enter'){
        e.preventDefault();
        let act = document.activeElement.id;
        if(act === 'productname'){
            view.focus();
        } else if (act === 'view'){
            quality.focus();
        } else if (act === 'quality'){
            big_category.focus();
        } else if (act === 'big_category'){
            category.focus();
        } else if (act === 'category'){
            small_category.focus();
        }

        for(let i = 0; i < divRadioCount; i++){
            let actPrice = document.getElementById('price'+i);
            if(act === 'pieces'+i){
                actPrice.focus();
            }
        }
        return false;
    }

    function is_empty(){
    if(productname.value === "" || view.value === "" || quality.value === "" || big_category.value === "" || category.value === ""){
        return false;
    }else{
        return true;
    }
}
});


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
    addColorButton.classList.add("styleAddcolor");
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
    var radioOptions = ["ホワイト","ブラック","グレー　","ブラウン","ベージュ","グリーン","ブルー　","パープル","イエロー","ピンク　","レッド　","オレンジ"];
    var radioLength = radioValues.length;

    //div生成 価格まで全部入れる
    var divRadio = document.createElement('div');
    divRadio.id = "radioContainer"+divRadioCount;
    divRadio.classList.add("radioContainer");

    //div生成2 カラーだけ
    var divRadioChild = document.createElement('div');
    divRadioChild.classList.add("styledivRadioChild");
    divRadio.appendChild(divRadioChild);

    //ラジオボタン生成
    for(var i=0; i<radioLength; i++){
        //div生成3　一色だけ
        var colorBox = document.createElement('div');
        colorBox.classList.add("styleColorBox");
        divRadioChild.appendChild(colorBox);

        //ラジオボタンとspanを入れるdivを追加
        var divRadioGrandchild = document.createElement('div');
        divRadioGrandchild.classList.add('styledivRadioGrandchild');
        colorBox.appendChild(divRadioGrandchild);

        var colorRadio = document.createElement('input');
        colorRadio.type = "radio";
        colorRadio.name = "colorArray["+selectorValue+"]["+divRadioCount+"][color]";
        colorRadio.value = radioValues[i];
        colorRadio.id = selectorId+"radio"+divRadioCount+i;//ラベルつけるため
        colorRadio.classList.add("radio"+i);
        if(i === 0){
            colorRadio.required = true;
        }
        divRadioGrandchild.appendChild(colorRadio);

        //spanを入れるlabelを生成
        var spanLabel = document.createElement('label');
        spanLabel.setAttribute('for', selectorId+"radio"+divRadioCount+i);

        //spanを生成
        var colorSpan = document.createElement('span');
        colorSpan.classList.add("span" + i);
        spanLabel.appendChild(colorSpan);
        divRadioGrandchild.appendChild(spanLabel);
        
        //radioボタンのラベル生成。
        var radioLabel = document.createElement('label');
        radioLabel.setAttribute('for', selectorId+"radio"+divRadioCount+i);
        radioLabel.innerHTML = radioOptions[i];
        radioLabel.classList.add("styleRadioLabel");
        colorBox.appendChild(radioLabel);



        //ラジオボタンが選択された時のイベントリスナーを追加する場合はここ
    }
    //改行タグ作成。
    var radioBr = document.createElement('br');
    divRadio.appendChild(radioBr);

    //数量のラベル生成
    var piecesLabel = document.createElement('label');
    piecesLabel.setAttribute('for',"pieces"+divRadioCount);
    piecesLabel.innerHTML = "数量";
    piecesLabel.classList.add('stylePieces');
    divRadio.appendChild(piecesLabel);

    //在庫数の入力フォーム生成
    var pieces = document.createElement('input');
    pieces.type = "text";
    pieces.name = "colorArray["+selectorValue+"]["+divRadioCount+"][pieces]";
    pieces.id = "pieces"+divRadioCount;//ラベルのため
    pieces.placeholder = "数値のみ";
    pieces.required = true;
    pieces.classList.add('styleTextBox');
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
    price.classList.add('styleTextBox');
    divRadio.appendChild(price);

    var radioBr = document.createElement('br');
    divRadio.appendChild(radioBr);

    //削除ボタン生成
    var delateRadio = document.createElement('button');
    delateRadio.type = "button";
    delateRadio.id = "delate"+divRadioCount;
    delateRadio.classList.add("styleDelate");
    delateRadio.name = selectorName+"optionArray["+divRadioCount+"]delate";
    delateRadio.innerHTML = "リセット";
    divRadio.appendChild(delateRadio);
    delateRadio.addEventListener('click', function(){
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
    selectBox.classList.add('styleSelect');
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
    var delateSelect = document.createElement('button');
    delateSelect.type = "button";
    delateSelect.id = "delateSelect"+divSelectCount;
    delateSelect.classList.add("styleDelateSelect");
    delateSelect.name = "selectorArray[]delate";
    delateSelect.innerHTML = "サイズを削除";
    divSelect.appendChild(delateSelect);
    delateSelect.addEventListener('click',function(){
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
                        category.innerHTML = '<option value="" selected hidden>選択してください</option>';//+=は前の選択されてたぶん残る
                        response.forEach(function(row) {
                            category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        categoryLabel.style.display = "block";
                        smallCategoryLabel.style.display = "none";
                        small_category.style.display = "none";
                        small_category.options[0].selected = true;
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
                        small_category.innerHTML = '<option value="" selected hidden>選択してください</option>';
                        response.forEach(function(row){
                            small_category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        //その他じゃないとき
                        if(!(b_id === "1" && c_id === "19" || b_id === "2" && c_id === "33")){
                            smallCategoryLabel.style.display = "block";
                            small_category.style.display = "block";
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
        //alert("キャンセルされました。");でもいい
    }
});
</script>
