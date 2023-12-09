<?php
include "db_config.php";

for ($i = 0; $i < 2000; $i++) {
    try {
        $order_id = rand(1, 761);
        $product_id = rand(1, 160);
        $color_size_id = rand(1, 588);

        // ここで重複がないか確認
        $testSql = "SELECT product_id FROM orders_detail
                    WHERE order_id = ? AND product_id = ? AND color_size_id = ?";
        $testStmt = $conn->prepare($testSql);
        $testStmt->bind_param("iii", $order_id, $product_id, $color_size_id);
        $testStmt->execute();
        $testResult = $testStmt->get_result();

        if (!($testResult->num_rows > 0)) {
            $order_pieces = rand(1, 5);

            // トータルの値段取得
            $selectSql = "SELECT price FROM color_size
                           WHERE color_size_id = ?";
            $selectStmt = $conn->prepare($selectSql);
            $selectStmt->bind_param("i", $color_size_id);
            $selectStmt->execute();
            $selectResult = $selectStmt->get_result();

            $price = 0;
            while ($row = $selectResult->fetch_assoc()) {
                $price = $row['price'];
            }
            $detail_total = $price * $order_pieces;

            // insert
            $insertSql = "INSERT INTO orders_detail VALUES(?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iiiii", $order_id, $product_id, $color_size_id, $order_pieces, $detail_total);
            
            if ($insertStmt->execute()) {
                echo "正常";
            } else {
                echo "Error in insert: " . $insertStmt->error;
            }
        }
    } catch (Exception $e) {
        echo "Error : " . $e->getMessage();
    }
}
?>
