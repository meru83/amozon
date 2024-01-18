<!-- 2FA_2.php -->
<!-- 現在にファクタ認証のメール送信ができない問題が発生 -->
<!DOCTYPE html>
<html lang="jp">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"href="../CSS/2FA_2.css">
        <title>2ファクタ認証</title>
    </head>
    <body>

        <h2 class="h2">2ファクタ認証</h2>
        <div class="box1">
            <h2 class="setu">認証コードをteamrem2fa@gmail.comからご登録いただいたメールに送信しました。<br>受信箱にない場合は迷惑メールフォルダの中などをご確認ください。</h2>
            <br>
            <h2>現在2ファクタ認証が動作しないためリンククリックでそのままトップページに遷移します。((｡´･ω･)｡´_ _))ﾍﾟｺﾘﾝ</h2>
            <h3><a href="top.php">トップページ</a></h3>
            <?php
            session_start();

            // セッションから認証コードを取得
            $verificationCode = isset($_SESSION['verification_code']) ? $_SESSION['verification_code'] : '';

            if (isset($_POST['submit'])) {
                // フォームが送信された場合の処理

                $userEnteredCode = $_POST['verification_code'];

                // 入力されたコードとセッションから取得したコードを比較
                if ($userEnteredCode == $verificationCode) {
                    // 認証成功

                    
                    header("Location: top.php");
                    exit;
                } else {
                    // 認証失敗
                    echo '<h2 style="color: red;">認証コードが正しくありません。</h2>';
                    echo "<h2><a href='../login.HTML'>ログインページよりもう一度実行してください</a></h2>";
                }
            }
            ?>
            <hr>
            <form method="post" action="" class="nin">
                <label for="verification_code" size>6桁の認証コードを入力</label>
                <h5 class="h5">↓　↓　↓</h5>
                <input type="text" id="verification_code" name="verification_code" required ><br>
                <button type="submit" name="submit" class="example">認証する</button>
            </form>
        </div>
    </body>
</html>
