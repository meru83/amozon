<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])) {
    header("Location: login.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

function htmlH1(){
    if(isset($_GET['sellerName'])){
        $h1 = htmlspecialchars($_GET['sellerName']);
        return $h1;
    }else if(isset($_GET['username'])){
        $h1 = htmlspecialchars($_GET['username']);
        return $h1;
    }
}

function cssLink(){
    if(!empty($_SESSION['seller_id'])){
        $chatCss = "<link rel='stylesheet' href='css/style_chat_room_seller.css'>";
        return $chatCss;
    }else if(!empty($_SESSION['user_id'])){
        $chatCss = "<link rel='stylesheet' href='css/style_chat_room.css'>";
        return $chatCss;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャットルーム</title>
    <?=cssLink()?>
</head>
<body>
<div class="chat_room_parent">
    <div id="header">
        <div class="header">
            <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
            <?="<h1 class='h1'>".htmlH1()."</h1>"?>
            <div class="space"></div>
        </div>
    </div>

    <!-- チャットログを表示するエリア -->
    <div id="chat-log">
        <!-- メッセージがリアルタイムで追加される -->
        <!---この中に存在しているクラス↓--->
        <!---class="chat-date"--->
        <!---class="chat-messages"--->
        <!---class="chat-message"--->
        <!---class="chat-image"--->
    </div>

    <div id="footer">
        <!-- メッセージ入力フォーム -->
        <form id="message-form" enctype="multipart/form-data">
            <input type="hidden" name="room_id" value="<?php echo $_GET['room_id']; ?>">
            <input type="text" name="message_text" id="message-text" class="chat_box" placeholder="メッセージを入力...">
            <label for="image-file" class="styleFile"><input type="file" name="image_file" id="image-file" accept="image/*"></label> <!-- 画像ファイルの選択 -->
            <button type="submit" class="send">送信</button>
        </form>
    </div>
</div>

    <script>
        // チャットログを表示するエリアを取得
        //const:定数、再代入不可
        //document:Webページのドキュメントオブジェクト 
        //getElementById('chat-log'):DOM(documentObjectModel)要素を取得するメソッド。DOMを操作すればページの要素を変更したりできる。引数のID属性を持つ範囲を取得。
        const chatLog = document.getElementById('chat-log');
        //let:変数宣言
        let firstLoad = true; // ページ最初の読み込み判定用フラグ
        let messageSent = false; // メッセージが送信されたか判定用フラグ
        let lastMessageCount = 0; // 最後に取得したメッセージの数
        //以上は状態を追跡するために使われる

        // チャットログを取得して表示する関数
        //function:新しい関数が定義されることを示す。
        function updateChatLog() {
            // PHPで取得したルームIDを取得。PHPの変数をjavascriptの変数へ代入。
            const room_id = <?php echo $_GET['room_id']; ?>;
            // XMLHttpRequestオブジェクトを作成。これを使うことで非同期つまり再読み込みなしでデータの送受信が可能。それらを実現するためのAPI(アプリケーション・プログラミング・インターフェース)
            const xhr = new XMLHttpRequest();
            //XHRオブジェクトのonreadystatechangeプロパティに無名の関数代入
            //この関数内にはサーバーからのデータを取得したり、エラーが発生した場合の処理を記述することができる。
            xhr.onreadystatechange = function() {
                //リクエストが完了した状態（readyStateプロパティ === 4）&&サーバーからのレスポンスに成功（status === 200）の場合に処理を実行
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // レスポンステキストをJSON形式にパース
                    //サーバーからのレスポンスデータはxhr.responseTextに格納されている
                    //JSON.parse():JSON形式のテキストデータを{JavaScriptオブジェクトに変換}ここが返り値
                    const response = JSON.parse(xhr.responseText);

                    // チャットログの表示エリアにメッセージをセット
                    //response.(ここはget_messages.phpで作られた連想配列が入る。)
                    //chatLog定数が示す場所の要素をresponse定数に格納されている連想配列のキー名(messages)の部分に置き換える。
                    chatLog.innerHTML = response.messages;
                    const messageCount = response.messageCount; // メッセージの総数
                    const hasNewMessages = response.hasNewMessages; // 新着メッセージがあるかどうかのフラグ

                    // ページが最初に読み込まれたとき、メッセージが送信されたとき、新着メッセージがある場合にスクロール
                    if (firstLoad || messageSent || hasNewMessages) {
                        setTimeout(() => {
                            scrollToBottom();    
                        }, 100); // チャットログを一番下までスクロール
                        firstLoad = false; // ページ最初の読み込みフラグをfalseに設定
                        messageSent = false; // メッセージ送信フラグをfalseに設定
                        lastMessageCount = messageCount; // 最後に取得したメッセージの数を更新
                    }
                }
            };

            // チャットログの取得用URLを生成し、非同期でGETリクエストを送信
            //xhr.openリクエストの内容を初期化
            //rom_idはこのファイルとget_messages.phpファイルでループさせている。
            //lastMessageCountはget_messages.phpで今のメッセージ総数と比較するために使いまたメッセージ総数がmessageCountとして帰ってくる。
            //3個目の引数は非同期リクエストを行うか否かを決める。非同期リクエストを使用するとほかの部分が読み込みをブロックせずに処理を続行できる。
            xhr.open('GET', `get_messages.php?room_id=${room_id}&last_message_count=${lastMessageCount}`, true);
            //リクエストをサーバーに送信する。
            xhr.send();
        }

        // チャットログを一番下までスクロールする関数
        function scrollToBottom() {
            //scrollTop:どれだけスクロールされているか示す。スクロールバーの位置によって値が変化する。
            //scrollheight:要素の全体の高さを示す、
            //scrollTopプロパティにscrollheightプロパティの値を入れるから一番下までスクロールされる。
            chatLog.scrollTop = chatLog.scrollHeight;
        }


        //JavaScriptでフォームのデータをとる場合以下の関数がテンプレ
        const messageForm = document.getElementById('message-form');
        //submit、送信されたときに呼び出される関数
        //addEventListener:イベントのリスナーを追加
        //イベントオブジェクトeを受け取る（フォームに入力された情報を受け取る）
        messageForm.addEventListener('submit', function(e) {
            //デフォルトのイベント動作をキャンセル。つまりフォームが送信されるとページが再読み込みされるがその挙動をキャンセルすることができる。これでAjaxで非同期でメッセージのやり取りができるようになる。
            e.preventDefault();
            const room_id = <?php echo $_GET['room_id']; ?>;
            const messageText = document.getElementById('message-text').value; // 入力されたメッセージ
            const imageFile = document.getElementById('image-file').files[0]; // 選択された画像ファイル
            //files[0]:type="file"のinputから選ばれたファイルを取得。[0]は複数ファイルが選ばれた場合の最初の画像。

            // フォームデータオブジェクトを作成し、room_id、message_text、image_fileを追加
            //HTML内のキーと値のペアを作成
            //appendで追加第一次引数がキー、第二次が値
            const formData = new FormData();
            formData.append('room_id', room_id);
            formData.append('message_text', messageText);
            formData.append('image_file', imageFile);

            // メッセージ送信用の非同期POSTリクエストを送信
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // 送信が成功したら、入力欄をクリアし、メッセージを更新
                    document.getElementById('message-text').value = '';
                    document.getElementById('image-file').value = ''; // ファイル選択をクリア

                    //trueにしてupdateCharlogを実行させることで送信後にメッセージの最下部を表示させれる。
                    messageSent = true; // メッセージ送信フラグをtrueに設定
                    updateChatLog(); // チャットログを更新
                }
            };
            xhr.open('POST', 'send_message.php', true);
            xhr.send(formData); // フォームデータを送信
        });

        // ページが最初に読み込まれたときにチャットログを取得し、画面をスクロール
        //この場合のイベントはloadでそれに対するリスナーを追加しloadがあった時に処理を実行させる
        window.addEventListener('load', function() {
            updateChatLog(); // チャットログを取得
        });

        // ページが最初に読み込まれたときに画面をスクロール
        //DOM ツリーが構築された後に発生するイベントです。
        window.addEventListener('DOMContentLoaded', function() {
            scrollToBottom(); // ページ読み込み時にスクロール
        });

        // 一定間隔でチャットログを更新
        //setInterrval();:指定された関数（第一次引数）を一定の間隔ミリ秒単位（第二引数）で実行させる。
        setInterval(updateChatLog, 1000);
    </script>
</body>
</html>