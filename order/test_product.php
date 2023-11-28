<?php
include "../db_config.php";

$sql = "SELECT DISTINCT product_id, productname, price, pieces FROM products";
$stmt = mysqli_query($conn, $sql);

if ($stmt) {
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $pieces = $_POST['pieces'];
    }
    while ($row = mysqli_fetch_assoc($stmt)) {
        $product_id = $row['product_id'];
        $productname = $row['productname'];
        $price = $row['price'];
        $pieces = $row['pieces'];

        echo <<<END
        $productname <br>
        $price <br>
        <form method="post">
            <select name="pieces">
                <option hidden>0</option>
END;
        for ($i = 1; $i <= $pieces; $i++) {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
        echo <<<END
            </select>
        </form>
        <a href="test_cart.php?product_id=$product_id&pieces=$pieces">カートに入れる</a><br><br>
END;
    }
} else {
    // クエリが失敗した場合のエラーハンドリング
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
