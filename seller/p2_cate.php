<?php
header('Content-Type: application/json'); // JSONレスポンスであることを指定

include "../db_config.php";

// エラーログをerror.logファイルに保存
ini_set('error_log', 'error.log');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c_id = $_POST['category'];

    $c_cate = array(); // JSON 形式のデータを格納する配列

    $small_sql = "SELECT s.small_category, s.small_category_name, c.category_name
                FROM small_category s
                INNER JOIN category c ON (s.category_id = c.category_id)
                WHERE s.category_id = ?";
    $small_stmt = $conn->prepare($small_sql);
    $small_stmt->bind_param("i", $c_id);
    $small_stmt->execute();
    $small_result = $small_stmt->get_result();

    if ($small_result->num_rows > 0) {
        while ($row = $small_result->fetch_assoc()) {
            $small_category = $row['small_category'];
            $small_category_name = $row['small_category_name'];
            $category_name = $row['category_name'];
            $c_cate[] = array(
                'value' => $small_category,
                'text' => $category_name . ' - ' . $small_category_name
            );
        }
    } else {
        // エラー時の処理を追加（例: エラーメッセージをログに記録）
        error_log("No results found for big_category_id: $c_id");
    }

    echo json_encode($c_cate); // JSON 形式のデータを出力
}

$conn->close();
?>
