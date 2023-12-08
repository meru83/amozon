<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['seller_id']) || isset($_SESSION['user_id'])){
    $userId = isset($_SESSION['user_id'])?$_SESSION['user_id']:null;
    $sellerId = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
}

// データベース接続情報を読み込む
include 'db_config.php';

// HTTPリクエストのメソッドがGETであり、room_idのパラメータが存在する場合に処理が行われる
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // クライアントから送信された前回のメッセージ数を取得
    $lastMessageCount = isset($_GET['last_message_count']) ? intval($_GET['last_message_count']) : 0;

    
    // トランザクションを開始
    $conn->begin_transaction();

    // メッセージを取得
    // ORDER 日付の昇順
    $sql = "SELECT m.user_id, u.username, m.seller_id, s.sellerName, m.message_text, m.img_url, m.timestamp 
            FROM messages m 
            LEFT JOIN users u ON m.user_id = u.user_id 
            LEFT JOIN seller s ON m.seller_id = s.seller_id
            WHERE m.room_id = ? 
            ORDER BY m.timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // チャットログと前の日付を初期化
    $chatLog = array();
    $prevDate = null;

    // チャットログの生成
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $username = $row['username'];
        $seller_id = $row['seller_id'];
        $sellerName = $row['sellerName'];
        $message_text = $row['message_text']; // メッセージ
        $img_url = $row['img_url']; 

        $sent_at = new DateTime($row['timestamp']); // 送信日時をDateTimeオブジェクトに変換

        $currentDate = $sent_at->format('m/d');
        $currentTime = $sent_at->format('H:i'); // 時刻をフォーマット

        // 前回のメッセージ数より新しいメッセージがある場合、フラグを設定
        if ($lastMessageCount < $result->num_rows) {
            $hasNewMessages = true;
        } else {
            $hasNewMessages = false;
        }

        // 前回の日付と比較して日付が変わった場合、新しい日付を表示
        if ($prevDate !== $currentDate) {
            if (!is_null($prevDate)) {
                $chatLog[] = "</div>";
            }
            $chatLog[] = "<div class='chat-date'>$currentDate</div>";//日付が変わったら日付表示させる
            $chatLog[] = "<div class='chat-messages'>";//日付が変わる間のメッセージすべて
            $prevDate = $currentDate;
        }

        // チャットログにメッセージを追加
        if (!is_null($message_text)) {
            if(!is_null($userId)){
                if(!empty($user_id)){
                    $chatLog[] = "<div class='my-message'>
                    <div class='chat-time_user'>{$currentTime}</div><div class='chat-message_user'> {$message_text}</div></div>";
                    //user側のメッセージ
                }else if(!empty($seller_id)){
                    $chatLog[] = "<div class='seller-message'>
                    <div class='chat-message_seller'> {$message_text}</div><div class='chat-time_seller'>{$currentTime}</div></div>";
                    //seller側のメッセージ
                }
            }else if(!is_null($sellerId)){
                if(!empty($user_id)){
                    $chatLog[] = "<div class='my-message'>
                    <div class='chat-message_user'> {$message_text}</div><div class='chat-time_user'>{$currentTime}</div></div>";
                    //user側のメッセージ
                }else if(!empty($seller_id)){
                    $chatLog[] = "<div class='seller-message'>
                    <div class='chat-time_seller'>{$currentTime}</div><div class='chat-message_seller'> {$message_text}</div></div>";
                    //seller側のメッセージ
                }
            }
        }

        // 画像が存在する場合、imgタグで表示
        if (!is_null($img_url)) {
            if(!is_null($userId)){
                if(!empty($user_id)){
                    $img_file = "img/{$img_url}";
                    list($width, $height, $type, $attr) = getimagesize($img_file);
                    if($width > $height){
                        //幅の方がでかいとき
                        $chatLog[] = "<div class='my-message'>
                        <div class='chat-time_user'>{$currentTime}</div><div class='chat-message_user_width_img'><img src='img/{$img_url}' alt='Image' /></div></div>";
                    }else{
                        //高さのほうがでかいとき
                        $chatLog[] = "<div class='my-message'>
                        <div class='chat-time_user'>{$currentTime}</div><div class='chat-message_user_height_img'><img src='img/{$img_url}' alt='Image' /></div></div>";
                    }
                }else if(!empty($seller_id)){
                    $img_file = "img/{$img_url}";
                    list($width, $height, $type, $attr) = getimagesize($img_file);
                    if($width > $height){
                        //幅の方がでかいとき
                        $chatLog[] = "<div class='seller-message'>
                        <div class='chat-message_seller_width_img'><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time_seller'>{$currentTime}</div></div>";
                    }else{
                        //高さのほうがでかいとき
                        $chatLog[] = "<div class='seller-message'>
                        <div class='chat-message_seller_height_img'><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time_seller'>{$currentTime}</div></div>";
                    }
                }
            } else if(!is_null($sellerId)){
                if(!empty($user_id)){
                    $img_file = "img/{$img_url}";
                    list($width, $height, $type, $attr) = getimagesize($img_file);
                    if($width > $height){
                        //幅の方がでかいとき
                        $chatLog[] = "<div class='my-message'>
                        <div class='chat-message_user_width_img'><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time_user'>{$currentTime}</div></div>";
                    }else{
                        //高さのほうがでかいとき
                        $chatLog[] = "<div class='my-message'>
                        <div class='chat-message_user_height_img'><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time_user'>{$currentTime}</div></div>";
                    }
                }else if(!empty($seller_id)){
                    $img_file = "img/{$img_url}";
                    list($width, $height, $type, $attr) = getimagesize($img_file);
                    if($width > $height){
                        //幅の方がでかいとき
                        $chatLog[] = "<div class='seller-message'>
                        <div class='chat-time_seller'>{$currentTime}</div><div class='chat-message_seller_width_img'><img src='img/{$img_url}' alt='Image' /></div></div>";
                    }else{
                        //高さのほうがでかいとき
                        $chatLog[] = "<div class='seller-message'>
                        <div class='chat-time_seller'>{$currentTime}</div><div class='chat-message_seller_height_img'><img src='img/{$img_url}' alt='Image' /></div></div>";
                    }
                }
            }
            // if(!empty($user_id)){
            //     $img_file = "img/{$img_url}";
            //     list($width, $height, $type, $attr) = getimagesize($img_file);
            //     if($width > $height){
            //         //幅の方がでかいとき
            //         $chatLog[] = "<div class='my-message'>
            //         <div class='chat-message_user_width_img'><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time'>{$currentTime}</div></div>";
            //     }else{
            //         //高さのほうがでかいとき
            //         $chatLog[] = "<div class='my-message'>
            //         <div class='chat-message_user_height_img'><br><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time'>{$currentTime}</div></div>";
            //     }
            // }else if(!empty($seller_id)){
            //     $img_file = "img/{$img_url}";
            //     list($width, $height, $type, $attr) = getimagesize($img_file);
            //     if($width > $height){
            //         //幅の方がでかいとき
            //         $chatLog[] = "<div class='seller-message'>
            //         <div class='chat-message_seller_width_img'><br><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time'>{$currentTime}</div></div>";
            //     }else{
            //         //高さのほうがでかいとき
            //         $chatLog[] = "<div class='seller-message'>
            //         <div class='chat-message_seller_height_img'><br><img src='img/{$img_url}' alt='Image' /></div><div class='chat-time'>{$currentTime}</div></div>";
            //     }
            // }
        }
    }

    // チャットログをJSON形式で出力
    //json_encode():連想配列や配列などのPHPのデータ構造をJSON形式に変換する。
    //今回はarrayで連想配列を渡している。=>は連想配列のキーと値を関連づける。
    //"messages" => implode("", $chatLog):配列内の全ての要素を空の文字列で連結した結果を取得します。つまりメッセージの文字列そのもの。
    //"messageCount" => $result->num_rows:メッセージの数
    //"hasNewMessages" => $hasNewMessages:新しいメッセージがあるか確認するフラグ。
    echo json_encode(array("messages" => implode("", $chatLog), "messageCount" => $result->num_rows, "hasNewMessages" => $hasNewMessages));

    // トランザクションをコミット
    $conn->commit();
} 

// データベース接続を閉じる
$conn->close();
?>



