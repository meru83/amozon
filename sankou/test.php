<?php
$email = "2312062@i-seifu.jp";
if(sendVerificationCodeByEmailLocal($email)){
    echo "成功";
}else{
    echo "失敗";
}
function sendVerificationCodeByEmailLocal($userEmail) {
    $to = $userEmail;
    $subject = '認証コード';
    $message = 'テスト';
    // $message .= '認証コード: ' . $verificationCode;
    // $message .= '心当たりがない場合は無視してください';
    // $headers = "From: 2312067@i-seifu.jp";

    // メール送信
    // return mb_send_mail($to, $subject, $message, $headers);
    return mb_send_mail($to, $subject, $message);
}
?>