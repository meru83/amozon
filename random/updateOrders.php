<?php
include "db_config.php";
for($i = 1; $i <= 764; $i++){
    $total = 0;
    $sql = "SELECT detail_total FROM orders_detail
            WHERE order_id = $i
            ORDER BY detail_total";
    echo "sql:$sql<br>";
    $result = $conn->query($sql);
    if($result && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $total += $row['detail_total'];
            echo $row['detail_total'];
            echo "<br>";
        }
        echo "合計".$total."<br>";
        $sql2 = "UPDATE orders SET total = $total WHERE order_id = $i";
        $conn->query($sql2);
    }
}
?>