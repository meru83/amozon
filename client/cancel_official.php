<?php 
    include '../db_config.php';

    $sql = "SELECT seller_id, sellerName
            FROM seller
            WHERE official = true
            ORDER BY create_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h1>承認済み</h1>";
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
        <a href="cancel.php?seller_id=$sellerId">
            取り消す
        </a>
        <br>
        END;
    }
?>