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

<!-- 金額の入力 -->
<label for="chargePrice">金額<input name="chargePrice" id="chargePrice" type="text" placeholder="0">円</label><br>

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
<!-- もとから無限にチャージできる怪しい銀行を用意する。 -->
<!-- どんな銀行足してもいいよ -->
<select name="bank" id="bank">
    <option value="" hidden>選択してください</option>
    <option value="西原bank">西原bank</option>
    <option value="readBank">readBank</option>
</select>
<br>


<?php
$user_id = $_SESSION['user_id'];

$sql = "SELECT total_pay FROM pay WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();
    $totalPay = $row['total_pay'];
}
?>

<!-- 現在の残高 -->
<p>現在のreadPay残高：<?=$totalPay?></p>
<p>チャージ先：readPay</p>

<!-- チャージ確定ボタン -->
<button type="button" id="chargeButton" onclick="chargeButton()">チャージする</button>

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
        return false;
    }
    if(bank === ""){
        alert("銀行を選択してください");
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
