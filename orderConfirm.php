<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    header("Location:login.php");
    exit();
}


// try{
    //select insert チャットなかったら作成してチャットへあったらチャットへ
    $maxPrice = $_GET['maxPrice'];
    $arraySellerId = array();
    $order_status = "出荷準備中";

    $insertSql = "INSERT INTO orders (user_id, total, order_status) VALUES(?, ?, ?)"; 
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sis",$user_id,$maxPrice,$order_status);
    if(!$insertStmt->execute()){
        if(!alert("問題が発生しました。カート画面に戻ります。")){
            echo <<<HTML
            <script>
            if(!alert("問題が発生しいました。カート画面に戻ります。")){
                window.location.href = "cartContents.php";
            }
            </script>      
            HTML;
        }
    }
    $last_id = $conn->insert_id;//order_id取得


    $cartSql = "SELECT c.product_id, c.color_size_id, c.pieces AS cartPieces, p.seller_id, s.pieces AS maxPieces, s.price, s.service_status FROM cart c
            LEFT JOIN products p ON (c.product_id = p.product_id)
            LEFT JOIN color_size s ON (c.color_size_id = s.color_size_id)
            WHERE c.user_id = ?";
    $cartStmt = $conn->prepare($cartSql);
    $cartStmt->bind_param("s",$user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    if($cartResult && $cartResult->num_rows > 0){
        while($cartRow = $cartResult->fetch_assoc()){
            $service_status = $cartRow['service_status'];
            $seller_id = $cartRow['seller_id'];
            $cartPieces = $cartRow['cartPieces'];
            $maxPieces = $cartRow['maxPieces'];
            $price = $cartRow['price'];
            $product_id = $cartRow['product_id'];
            $color_size_id = $cartRow['color_size_id'];
            $price *= $cartPieces;
            if(!($service_status) || ($cartPieces > $maxPieces)){
                echo <<<HTML
                <script>
                if(!alert("在庫が不足している商品がありました。カート画面に戻ります。")){
                    window.location.href = "cartContents.php";
                }
                </script>      
                HTML;
            }

            $insert2Sql = "INSERT INTO orders_detail (order_id, product_id, color_size_id, order_pieces, detail_total) VALUES (?, ?, ?, ?, ?)";
            $insert2Stmt = $conn->prepare($insert2Sql);
            $insert2Stmt->bind_param("iiiii",$last_id,$product_id,$color_size_id,$cartPieces,$price);
            $insert2Stmt->execute();

            // チャットルームが既に存在するか確認
            $check_sql = "SELECT room_id FROM chatrooms WHERE (user_id = ? AND seller_id = ?)";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $user_id, $seller_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if (!$check_result->num_rows > 0) {
                // チャットルームを新しく作成
                $insert_sql = "INSERT INTO chatrooms (user_id, seller_id) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ss", $user_id, $seller_id);
                $insert_stmt->execute();
            }

            if(!in_array($seller_id, $arraySellerId)){
                $chatroomSql = "SELECT room_id FROM chatrooms WHERE user_id = ? && seller_id = ?";
                $chatroomStmt = $conn->prepare($chatroomSql);
                $chatroomStmt->bind_param("ss",$user_id,$seller_id);
                $chatroomStmt->execute();
                $chatroomResult = $chatroomStmt->get_result();
                if($chatroomResult && $chatroomResult->num_rows > 0){
                    $chatroomRow = $chatroomResult->fetch_assoc();
                    $room_id = $chatroomRow['room_id'];
                    $nullVar = null;
                    $message_text = "商品を購入しました！";

                    $chatSql = "INSERT INTO messages (room_id, user_id, seller_id, message_text)
                                VALUES(?, ?, ?, ?)";
                    $chatStmt = $conn->prepare($chatSql);
                    $chatStmt->bind_param("isss",$room_id,$user_id,$nullVar,$message_text);
                    $chatStmt->execute();
                }
                $arraySellerId[] = $seller_id;
                
            }

            $deleteSql = "DELETE FROM cart WHERE user_id = ? && product_id = ? && color_size_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("sii",$user_id,$product_id,$color_size_id);
            $deleteStmt->execute();
        }
        
        //残高修正
        $selectSql = "SELECT user_id FROM pay WHERE user_id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("s",$user_id);
        $selectStmt->execute();
        $selectResult = $selectStmt->get_result();
        if($selectResult && $selectResult->num_rows > 0){
            $sql = "UPDATE pay SET total_pay = total_pay - ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si",$maxPrice,$user_id);
        }else{
            echo <<<HTML
            <script>
            if(!alert("購入処理が強制終了しました。カート画面に戻ります。2")){
                window.location.href = "cartContents.php";
            }
            </script>
            HTML;
        }
        if($stmt->execute()){
            $insertSql = "INSERT INTO pay_history2 (user_id, pay_pay) VALUES(?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("si", $user_id, $maxPrice);
            $insertStmt->execute();
        }
        
    }

    echo <<<HTML
    <script>
    if(!alert("チャットで販売者と配送料の負担者を決めましょう")){
        window.location.href = "chat_rooms.php";
    }
    </script>  
    HTML;
// }catch(Exception $e){
//     error_log("Error in orderConfirm.php: " . $e->getMessage() . PHP_EOL);
//     echo <<<HTML
//     <script>
//     if(!alert("購入処理が強制終了しました。カート画面に戻ります。")){
//         window.location.href = "cartContents.php";
//     }
//     </script>
//     HTML;
// }
?>