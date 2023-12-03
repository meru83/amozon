<?php
// if(session_status() == PHP_SESSION_NONE){
//     session_start();
// }

// if(!isset($_SESSION['user_id'])){
//     header("Location:login.php");
//     exit();
// }

include "db_config.php";

if(isset($_POST['add'])){
    // $user_id = $_SESSION['user_id'];
    $addressname = $_POST['addressname'];
    $post_code = $_POST['post_code'];
    $prefectures = $_POST['prefectures'];
    $city = $_POST['city'];
    $city_kana = $_POST['city_kana'];
    $street = $_POST['street'];
    $room_number = isset($_POST['room_number']) ? $_POST['room_number'] : null;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>住所登録</title>
</head>
<body>
<form method="post" id="form">
    <label for="post_code">郵便番号：</label><br>
    <input type="text" name="post_code" id="input" maxlength=8 placeholder="例)1234567" required>
    <button id="search" type="button">住所検索</button><br>
    <p id="error"></p>
    <label for="prefectures">都道府県：</label><br>
    <select name="prefectures" id="address1" required>
        <option hidden>選択してください</option>
    <?php 
        $row = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
        foreach($row as $item){
            echo '<option value="' . $item . '">' . $item . '</option>';
        }
    ?>
    </select><br>
    <label for="city">住所：</label><br>
    <input type="text" name="city" id="address2" placeholder="新宿区西新宿" value="" required><br>
    <input type="text" name="city_kana" id="kana2" placeholder="シンジュククニシシンジュク" value="" required><br>
    <label for="street">丁・番地：</label><br>
    <input type="text" name="street" id="tyou" placeholder="2-8-1" required><br>
    <label for="room_number">建物名・部屋番号：</label><br>
    <input type="text" name="room_number" id="room_number"><br>


    <label for="addressname">氏名：</label><br>
    <input type="text" name="addressname" id="addressname" placeholder="例）山田 太郎" required><br>
    <input type="submit" name="add" value="登録する">
</form>



<script src="js/post.js"></script>
<script>
let form = document.getElementById('form');
form.addEventListener('keydown', (e) => {
    if(is_empty()){
        return true;
    }else if (e.key === 'Enter'){
        event.preventDefault();
        let act = document.activeElement.id;
        if(act === 'tyou'){
            let addressname = document.getElementById('addressname');
            addressname.focus();
        }
        return false;
    }
});

function is_empty(){
    let addressname = document.getElementById('addressname');
    let input = document.getElementById('input');
    let address1 = document.getElementById('address1');
    let address2 = document.getElementById('address2');
    let kana2 = document.getElementById('kana2');
    let tyou = document.getElementById('tyou');

    if(addressname.value === "" || input.value === "" || address1.value === "" || address2.value === "" || kana2.value === "" || tyou.value === ""){
        return false;
    }else{
        return true;
    }
}

function callPostCode(){
    let api = 'https://zipcloud.ibsnet.co.jp/api/search?zipcode=';
    let error = document.getElementById('error');
    let input = document.getElementById('input');
    let address1 = document.getElementById('address1');
    let address2 = document.getElementById('address2');
    let kana2 = document.getElementById('kana2');
    let tyou = document.getElementById('tyou');
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
let search = document.getElementById('search');
search.addEventListener('click', ()=>{
    callPostCode();
}, false);

//Enterの時
const input = document.getElementById("input");
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