<?php

// パスワードがポリシーに合致しているかチェックする関数
function is_strong_password($password) {
    // パスワードが8文字以上かどうかをチェック
    if (strlen($password) < 8) {
        return false;
    }

    // パスワードに大文字、小文字、数字、特殊文字が含まれるかどうかをチェック
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return false;
    }

    return true;
}

?>