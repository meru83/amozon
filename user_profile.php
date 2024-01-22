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

if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
END;
} else {
    $foo2 = <<<END
    <div class="New_log">
        <a href="register.php"><div class="log_style">新規登録</div></a>
        <a href="login.php"><div class="log_style rightM">ログイン</div></a>
    </div>
END;
}

// ログイン中のユーザーの情報を取得
if(isset($_SESSION['user_id'])){
    $user = $_SESSION['user_id'];
    $sql = "SELECT total_pay FROM pay WHERE user_id = '$user'";
    $result = $conn->query($sql);

    // クエリの実行にエラーがある場合
    if (!$result) {
        die("クエリの実行にエラーがあります: " . $conn->error);
    }

    // データが存在する場合、データを1行だけ取得して表示
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // "total_pay" キーが存在するか確認してから表示
        if (isset($row["total_pay"])) {
            // チャージ情報をHTMLに表示
            $zandaka =<<<END
                <div class='sub-content-item'>
                <div class="flexBox">
                    <h2>残高<h2>
                    <p>￥{$row["total_pay"]}</p>
                </div>
                </div>
            </a>
END;
        } else {
            //0円の時
            $zandaka =<<<END
            <a href='chargePay.php'>
                <div class='sub-content-item'>
                <div class="flexBox">
                    <h2>残高<h2>
                    <p>￥0</p>
                </div>
                </div>
            </a>
END;
        }
    } else {
        // チャージ情報が存在しない場合
        $zan =<<<END
            <a href='chargePay.php'>
                <div class='sub-content-item'>
                <div class="flexBox">
                    <h2>チャージする</h2>
                </div>
                </div>
            </a>
END;
    }
} 
// ログイン中のユーザーIDを取得
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    
    // ユーザーの住所情報を取得
    $sql = "SELECT * FROM address WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    // クエリの実行にエラーがある場合
    if (!$result) {
        die("クエリの実行にエラーがあります: " . $conn->error);
    }

    // 住所が登録されているか確認
    if ($result->num_rows > 0) {
        // 認証済みの場合
        $nisyou =<<<END
        <p>認証済み</p>
        END;
    } else {
        // 未登録の場合
        $jusyoNone =<<<END
        <a href='address_insert.php'>
                <div class='sub-content-item'>
                    <h2>(未登録)</h2>
                </div>
            </a>
        END;
    }
} else {
 // ログインしていない場合
 $addLogin =<<<END
 <p>ログインしていません</p>
 END;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="css/Amazon_profile.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">プロフィール</h1>
        <?=$foo2?>
    </div>
<div class="profile_container">    
    <div class="left-menu">
        <div>
            <ul class="menu-list">
                <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                <li class="menu-item"><a href="user_top.php"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                <li class="menu-item"><a href="favoriteProduct.php"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                <li class="menu-item"><a href="user_profile.php"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
            </ul>
        </div>
        <div>
            <ul class="menu-list-bottom">
            </ul>
        </div>
    </div>
    <div class="right-content">
        <div class="amozon_profile">
            <img src="img/cart_dake.svg" class="amozon_usericon">
            <?php
             // ログイン中のユーザーIDを取得
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql_user = "SELECT username FROM users WHERE user_id = '$user_id'";
            $result_user = $conn->query($sql_user);

        // クエリの実行にエラーがある場合
        if (!$result_user) {
            die("クエリの実行にエラーがあります: " . $conn->error);
        }

        // ユーザー名が取得できた場合は表示
        if ($result_user->num_rows > 0) {
            $row_user = $result_user->fetch_assoc();
            echo "<h1>{$row_user['username']}</h1>";
        }           
        // データベース接続を閉じる
            $conn->close();
        } else {
            // ログインしていない場合
            echo "<p>ログインしていません</p>";
        }
        ?>
        
        <?php if(isset($nisyou )){
                echo $nisyou;
            }?>
            <div class="sub-content">
                <?php if(isset($addLogin )){
                    echo <<<END
                    <a href="login.php">
                    <div class="sub-content-item1">
                        <h2>$addLogin</h2>
                        <p>ログインまたは新規登録してください。</p>
                    </div>
                    </a>
                    END;
                } else {
                    echo <<<END
                    <a href="address_insert.php">
                    <div class="sub-content-item1">
                        <h2>住所変更</h2>
                    END;
                    if(isset($nisyou)){
                        echo $nisyou ;
                    }
                    if(isset($jusyoNone)) {
                        echo $jusyoNone;
                    }
                    echo <<<END
                    </div>
                    </a>
                END;
                }?>

                <a href="chargePay.php">
                <div class="sub-content-item1">
                    <h2>チャージする</h2>
                </div>
                </a>
                <a href="#">
                <div class="sub-content-item1">
                    <h2>サブコンテンツ3</h2>
                    <p></p>
                </div>
                </a>
            </div>
            <?php if(isset($zandaka)){
                echo $zandaka;
            } ?>
        </div>
    </div>
</div>
</body>
</html>