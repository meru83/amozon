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

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/chargePay.css">
    <title>残金チャージ</title>

<!-- 金額の入力 --><body>


    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">残金チャージ</h1>
        <div class="space"></div>
    </div>

    <section>
        <!-- 金額の入力 -->
        <label for="chargePrice">
            金額
            <input name="chargePrice" class="charginput" id="chargePrice" type="text" placeholder="0">
            円
            <div id="his">必須</div>
        </label>
        <br>

        <button type="button" onclick="autoCharge(2000)">2,000</button>
        <button type="button" onclick="autoCharge(3000)">3,000</button>
        <button type="button" onclick="autoCharge(5000)">5,000</button>
        <button type="button" onclick="autoCharge(10000)">10,000</button>
        <button type="button" onclick="autoCharge(20000)">20,000</button>
        <button type="button" onclick="autoCharge(30000)">30,000</button>
        <button type="button" onclick="autoCharge(50000)">50,000</button>
        <button type="button" onclick="autoCharge(100000)">100,000</button>
        <br>

        <!-- チャージする銀行 -->
        
        <p>チャージ先：readPay</p>

        <!-- チャージ確定ボタン -->
        <button type="button" id="chargeButton" onclick="chargeButton()">チャージする</button>
    </section>

<!-- チャージする銀行 -->
<!-- もとから無限にチャージできる怪しい銀行を用意する。 -->
<!-- どんな銀行足してもいいよ -->
    <div class="chargebox">
        <label for="bank">
            選択した銀行からチャージ
            <br>
            <select name="bank" id="bank">
                <option value="" hidden>選択してください</option>
                <option value="西原bank">西原bank</option>
                <option value="readBank">readBank</option>
            </select>
            <div id="his2">必須</div>
        </label>
        <br>
    </div>

    <div class="chargebox">

        <?php
        //現在の残高取得
        $user_id = $_SESSION['user_id'];

        $sql = "SELECT total_pay FROM pay WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result && $result->num_rows > 0){
            $row = $result->fetch_assoc();
            $totalPay = $row['total_pay'];
            echo "<div class='chargemoney'><p>現在のreadPay残高：$totalPay 円</p></div>";
        }else{
            echo "<div class='chargemoney'><p>現在のreadPay残高：0 円</p></div>";
        }
        ?>

        <div class="charglocation">
            <p>チャージ先：</p>
            <p>readPay</p>
        </div>


        <!-- チャージ確定ボタン -->
        <button type="button" id="chargeButton" onclick="chargeButton()">チャージする</button>

    </div>

</body>
</html>
<!-- js -->
<script>
function chargeButton(){
    var chargePriceElement = document.getElementById('chargePrice');
    var chargePrice = chargePriceElement.value
    var bankElement = document.getElementById('bank');
    var num = bankElement.selectedIndex;
    var bank = bankElement.options[num].value;
    if(chargePrice === ""){
        alert("金額を入力してください");
        var his = document.getElementById('his');
        var his2 = document.getElementById('his2');
        his.style.display = 'block';
        his2.style.display = 'block';
        return false;
    }
    if(bank === ""){
        alert("銀行を選択してください");
        var his = document.getElementById('his');
        var his2 = document.getElementById('his2');
        his.style.display = 'block';
        his2.style.display = 'block';
        return false;
    }
    if(window.confirm(chargePrice+"円をチャージします。")){
        //はい
        const formData = new FormData();
        formData.append('chargePrice', chargePrice);
        formData.append('bank', bank);

        const xhr = new XMLHttpRequest();
        xhr.open('POST','chargePayBack.php',true);
        xhr.send(formData);

        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                try {
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(function(row) {
                        if(row.error_message === true){
                            chargePriceElement.value = "";
                            bankElement.options[0].selected = true;
                            if(window.confirm("OKでカート画面に戻ります。")){
                                //はい
                                window.location.href = 'cartContents.php';
                            }else{
                                //いいえ
                            }
                        }else{
                            alert("チャージに失敗しました。");
                        }
                    });
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    alert("リクエストが失敗しました。");
                }
            }
        }
    }else{
        //いいえ
        alert("チャージをキャンセルしました。")
    }
}

function autoCharge(autoPrice){
    var chargePriceElement = document.getElementById('chargePrice');
    chargePriceElement.value = autoPrice;
}
</script>
