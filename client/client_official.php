<?php 
    include '../db_config.php';

    $sql = "SELECT seller_id, sellerName
            FROM seller
            WHERE official = false
            ORDER BY create_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h1>公式の承認待ち</h1>";
    if(isset($_GET['error'])){
        $error_message = $_GET['error'];
        echo "<p>" . $error_message . "</p>";
    }
    while($row = $result->fetch_assoc()){
        $sellerId = $row['seller_id'];
        $sellername = htmlspecialchars($row['sellerName']);

        echo <<<END
        $sellerId
        $sellername
        <a href="approval.php?seller_id=$sellerId">
            承認
        </a>
        <br>
        END;
    }
?>