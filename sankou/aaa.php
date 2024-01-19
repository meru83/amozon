<?php

if (mail("2312062@i-seifu.jp", "TEST MAIL", "This is a test message.", "From: 2312067@i-seifu.jp")) {
  echo "メールが送信されました。";
} else {
  echo "メールの送信に失敗しました。";
}

?>
