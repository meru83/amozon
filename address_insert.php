<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else if(isset($_SESSION['seller_id'])){
    $seller_id = $_SESSION['seller_id'];
}

include "db_config.php";

if(isset($_POST['add'])){
    $post_code = $_POST['post_code'];
    $prefectures = $_POST['prefectures'];
    $city = $_POST['city'];
    $city_kana = $_POST['city_kana'];
    $street = $_POST['street'];
    $room_number = isset($_POST['room_number']) ? $_POST['room_number'] : null;
    $number = $_POST['number'];
    $addressname = $_POST['addressname'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<link rel="stylesheet" href="css/Amozon_addres.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>住所登録</title>
</head>
<body class="address_body">
<form method="post" id="form" class="address_form">
    <label for="post_code" class="address_label">郵便番号：</label><br>
    <input type="text" name="post_code" id="input" class="address_textbox" maxlength=8 placeholder="例)1234567（ハイフンなし）" required>
    <button id="search" class="address_botton1" type="button">住所検索</button><br>
    <p id="error"></p>
    <label for="prefectures">都道府県：</label><br>
    <select name="prefectures" id="address1" class="address_select" required>
        <option hidden>選択してください</option>
    <?php 
        $row = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
        foreach($row as $item){
            echo '<option value="' . $item . '">' . $item . '</option>';
        }
    ?>
    </select><br>
    <label for="city">住所：</label><br>
    <input type="text" name="city" id="address2" class="address_textbox" placeholder="大阪市北区梅田" value="" required><br>
    <input type="text" name="city_kana" id="kana2" class="address_textbox" placeholder="オオサカシキタクウメダ" value="" required><br>
    <label for="street">丁・番地：</label><br>
    <input type="text" name="street" id="tyou" class="address_textbox" placeholder="3-1-1" required><br>
    <label for="room_number">建物名・部屋番号：</label><br>
    <input type="text" name="room_number" id="room_number" class="address_textbox"><br>
    <label for="number">電話番号：</label><br>
    <input type="text" name="number" id="number" class="address_textbox" placeholder="0612345678" required><br>


    <?php 
    if(isset($user_id)){
        echo <<<HTML
        <label for="addressname">氏名：</label><br>
        <input type="text" name="addressname" id="addressname" class="address_textbox" placeholder="例）山田 太郎" required><br>
        HTML;
    }else if(isset($seller_id)){
        echo <<<HTML
        <label for="addressname">会社名：</label><br>
        <input type="text" name="addressname" id="addressname" class="address_textbox" placeholder="例）Re.Read 株式会社" required><br>
        HTML;
    }
    //氏名のところをsellerなら会社名などに変える
    ?>
    <div class="addres_flex">
    <input class="addres_botton2" type="submit" id="add" name="add" value="登録">
    <?php
    if(isset($user_id)){
        echo '<input type="submit" value="スキップ" class="address_skp" onclick="buttonClick()">';
    }else if(isset($seller_id)){
        echo '<input type="submit" value="スキップ" class="address_skp" onclick="buttonClickSeller()">';
    }
    ?>
    </div>
</form>



<script src="js/post.js"></script>
<script>
const form = document.getElementById('form');
const search = document.getElementById('search');
const input = document.getElementById('input');
const tyou = document.getElementById('tyou');
const number = document.getElementById('number');
const addressname = document.getElementById('addressname');
const add = document.getElementById('add');
const address1 = document.getElementById('address1');
const address2 = document.getElementById('address2');
const kana2 = document.getElementById('kana2');
form.addEventListener('keydown', (e) => {
    if(is_empty()){
        return true;
    }else if (e.key === 'Enter'){
        e.preventDefault();
        let act = document.activeElement.id;
        if(act === 'input'){
            tyou.focus();
        }else if(act === 'tyou'){
            number.focus();
        }else if(act === 'number'){
            addressname.focus();
        }else if(act === 'addressname'){
            add.focus();
        }
        return false;
    } 
});

function buttonClick(){
    window.location = "user_top.php";
}
function buttonClickSeller(){
    window.location = "seller/seller_top.php";
}

function is_empty(){
    //どれか一つでも空ならfalseを返す
    if(addressname.value === "" || input.value === "" || address1.value === "" || address2.value === "" || kana2.value === "" || tyou.value === ""){
        return false;
    }else{
        return true;
    }
}

function callPostCode(){
    let api = 'https://zipcloud.ibsnet.co.jp/api/search?zipcode=';
    let error = document.getElementById('error');
    let pattern = /^[0-9]{3}[0-9]{4}$/;
    let param;
    if(!pattern.test(input.value)){
        param = input.value.replace("-",""); //入力された郵便番号から「-」を削除
    }else{
        param = input.value;
    }
    let url = api + param;

    fetchJsonp(url, {
        timeout: 10000, //タイムアウト時間
    })
    .then((response)=>{
        error.textContent = ''; //HTML側のエラーメッセージ初期化
        return response.json();  
    })
    .then((data)=>{
        if(data.status === 400){ //エラー時
            error.textContent = data.message;
        }else if(data.results === null){
            error.textContent = '郵便番号から住所が見つかりませんでした。';
        } else {
            address1.value = data.results[0].address1;  // 都道府県
            address2.value = data.results[0].address2 + data.results[0].address3; // 市郡区町村 + address1～2以下の住所
            kana2.value = data.results[0].kana2 + data.results[0].kana3;
            tyou.focus();
        }
    })
    .catch((ex)=>{ //例外処理
        console.log(ex);
    });
}

//クリックの時
search.addEventListener('click', ()=>{
    callPostCode();
}, false);

//Enterの時
input.addEventListener('keydown', (e) => {
    // 「13」== Enterキーの番号か判定
    if( e.key === 'Enter' ){
        // Enterが押された時
        callPostCode();
    }
});
</script>
</body>
</html>