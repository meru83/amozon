<?php
session_start();

$_SESSION['seller_id'] = null;
$_SESSION['sellerName'] = null;
$_SESSION['user_id'] = null;
$_SESSION['username'] = null;

$flag = false;

// データベースへの接続情報を設定します
include 'db_config.php'; // データベース接続情報を読み込む

// フォームから送信されたユーザー名とパスワードを取得します
$userid = $_POST['user_id'];
$password = $_POST['password'];

// パスワードの検証
$sql = "SELECT user_id, username, pass FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['pass']; 
    //ハッシュ化されたパスワードと平文のパスワードを比較する。
    if (password_verify($password, $hashed_password)) {
        // 認証成功
        //ユーザー情報をセッションに保存
        //今はチャットルームの一覧に飛ぶけどトップに飛ばすようにする
        // $_SESSION['user_id'] = $row['user_id'];
        // $_SESSION['username'] = $row['username'];
        // if(isset($_SESSION['cart'])){
        //     try{
        //         for($i = 0; $i < count($_SESSION['cart']['product_id']); $i++){
        //             if($_SESSION['cart']['product_id'][$i] !== null){
        //                 $product_id = $_SESSION['cart']['product_id'][$i];
        //                 $color_size_id = $_SESSION['cart']['color_size_id'][$i];
        //                 $pieces = $_SESSION['cart']['pieces'][$i];
                        
        //                 $insertSql = "INSERT INTO cart(user_id, product_id, color_size_id, pieces) VALUES(?,?,?,?)";
        //                 $insertStmt = $conn->prepare($insertSql);
        //                 $insertStmt->bind_param("siii",$_SESSION['user_id'], $product_id, $color_size_id, $pieces);
        //                 $insertStmt->execute();
        //             }
        //         }
        //         $_SESSION['cart'] = null;
        //     }catch(Exception $e){
        //         $error_message = "カートの中身が失われました。";
        //     }
        // }
        // header("Location: user_top.php?error_message=".$error_message);
        // exit();
        // 6桁の2ファクタ認証コード生成
        $verificationCode = sprintf("%06d", mt_rand(0, 999999));

        $emailSql = "SELECT email FROM users WHERE user_id = ?";
        $emailStmt = $conn->prepare($emailSql);
        $emailStmt->bind_param("s", $userid);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();
        if($emailResult && $emailResult->num_rows > 0){
            $emailRow = $emailResult->fetch_assoc();
            $email = $emailRow['email'];
            if(sendVerificationCodeByEmailLocal($email, $verificationCode)){
                //メール送信成功
                $flag = true;
            }else{
                $error_message = "認証コードの送信に失敗しました。";
            }
        }
    }else{
        $error_message = "ユーザー名もしくはパスワードが間違えています。";
        header("Location: login.php?error=".urlencode($error_message));
        exit();
    }
}else{
    $error_message = "ユーザー名もしくはパスワードが間違えています。";
    header("Location: login.php?error=".urlencode($error_message));
    exit();
}

if(isset($_POST['submit'])){
    $verification_code = $_POST['verification_code'];
    if($verificationCode === $verification_code){
        // 認証成功
        //ユーザー情報をセッションに保存
        //今はチャットルームの一覧に飛ぶけどトップに飛ばすようにする
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        if(isset($_SESSION['cart'])){
            try{
                for($i = 0; $i < count($_SESSION['cart']['product_id']); $i++){
                    if($_SESSION['cart']['product_id'][$i] !== null){
                        $product_id = $_SESSION['cart']['product_id'][$i];
                        $color_size_id = $_SESSION['cart']['color_size_id'][$i];
                        $pieces = $_SESSION['cart']['pieces'][$i];
                        
                        $insertSql = "INSERT INTO cart(user_id, product_id, color_size_id, pieces) VALUES(?,?,?,?)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bind_param("siii",$_SESSION['user_id'], $product_id, $color_size_id, $pieces);
                        $insertStmt->execute();
                    }
                }
                $_SESSION['cart'] = null;
            }catch(Exception $e){
                $error_message = "カートの中身が失われました。";
            }
        }
        header("Location: user_top.php?error_message=".$error_message);
        exit();
    }
}

if($flag){
    echo <<<HTML
    <form method="post">
        <label>6桁の認証コード<label><br>
        <input type="text" name="verification_code" required><br>
        <input type="submit" name="submit" value="確定">
    </form>
    HTML;
}

$conn->close();
// 認証失敗
// header("Location: login.php");
// exit();
function sendVerificationCodeByEmailLocal($userEmail, $verificationCode) {
    $to = $userEmail;
    $subject = '認証コード';
    $message = '認証コードです。第三者には絶対に教えないでください。';
    $message .= '認証コード: ' . $verificationCode;
    $message .= '心当たりがない場合は無視してください';
    $headers = "From: 2312067@i-seifu.jp";

    // メール送信
    return mail($to, $subject, $message, $headers);
}
?>
