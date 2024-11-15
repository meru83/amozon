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
// ★★★ 変更ここから ★★★
//$python_script_path = "C:/xampp/htdocs/php/amo/py/rireki.py";  // ここに実際のPythonスクリプトのパスをセット
//$excel_file_path = "C:/xampp/htdocs/php/amo/py/rireki.xlsx";
$python_script_path = "rireki.py";  // ここに実際のPythonスクリプトのパスをセット
// ★★★ 変更ここまで ★★★

// Pythonスクリプトに引数を渡して実行
$command = "python $python_script_path $seller_id";
// echo $command;
// Pythonスクリプトをバックグラウンドで実行し、プロセスIDを取得
// ★★★ 変更ここから ★★★
// $pid = shell_exec(sprintf('%s > /dev/null 2>&1 & echo $!', $command));
// $pid = exec($command, $output, $return_var);
exec($command, $output, $return_var);
$path = $output[0];
if(strpos($path,'C:') !== false){
    echo "ファイルパス：$path に売り上げデータがダウンロードされました。";
}else{
    echo "売上データのファイルがダウンロードできませんでした。";
}
// ★★★ 変更ここまで ★★★

// プロセスが実行中の間待機
// ★★★ 変更ここから ★★★
// while (file_exists("/proc/$pid")) {
//     sleep(1);
// }
// ★★★ 変更ここまで ★★★

// ダウンロード用のヘッダーを送信
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="rireki.xlsx"');
// header('Cache-Control: max-age=0');

// Excelファイルを読み込んで出力
// readfile($output[0]);
?>
