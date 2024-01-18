<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

include '../db_config.php';
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(isset($_SESSION['seller_id'])){
    $seller_id = $_SESSION['seller_id'];
}else{
    header("Location: ../seller/seller_log.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

// Pythonスクリプトのパス
$python_script_path = "C:/xampp/htdocs/php/amo/py/rireki.py";  // ここに実際のPythonスクリプトのパスをセット
$excel_file_path = "C:/xampp/htdocs/php/amo/py/rireki.xlsx";

// Pythonスクリプトに引数を渡して実行
$command = "python $python_script_path $seller_id";
// Pythonスクリプトをバックグラウンドで実行し、プロセスIDを取得
$pid = shell_exec(sprintf('%s > /dev/null 2>&1 & echo $!', $command));

// プロセスが実行中の間待機
while (file_exists("/proc/$pid")) {
    sleep(1);
}

// ダウンロード用のヘッダーを送信
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rireki.xlsx"');
header('Cache-Control: max-age=0');

// Excelファイルを読み込んで出力
readfile($excel_file_path);
?>