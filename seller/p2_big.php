<?php
header('Content-Type: application/json'); // JSONレスポンスであることを指定

include "../db_config.php";

// エラーログをerror.logファイルに保存
ini_set('error_log', 'error.log');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $b_id = $_POST['big_category'];

    $b_cate = array(); // JSON 形式のデータを格納する配列

    $tyu_sql = "SELECT c.category_id, c.category_name, b.big_category_name
                FROM category c
                INNER JOIN big_category b ON (c.big_category_id = b.big_category_id)
                WHERE c.big_category_id = ?";
    $tyu_stmt = $conn->prepare($tyu_sql);
    $tyu_stmt->bind_param("i", $b_id);
    $tyu_stmt->execute();
    $tyu_result = $tyu_stmt->get_result();

    if ($tyu_result->num_rows > 0) {
        while ($row = $tyu_result->fetch_assoc()) {
            $category_id = $row['category_id'];
            $category_name = $row['category_name'];
            $big_category_name = $row['big_category_name'];
            $b_cate[] = array(
                'value' => $category_id,
                'text' => $big_category_name . ' - ' . $category_name
            );
        }
    } else {
        // エラー時の処理を追加（例: エラーメッセージをログに記録）
        error_log("No results found for big_category_id: $b_id");
    }

    echo json_encode($b_cate); // JSON 形式のデータを出力
}

$conn->close();
?>
