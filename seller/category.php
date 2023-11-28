<?php
include "../db_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit']) && isset($_POST['big_category'])) {
        $big = $_POST['big_category'];

        // 大カテゴリ名の重複チェック
        $check_sql = "SELECT COUNT(*) FROM big_category WHERE big_category_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $big);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            echo "同じ大カテゴリ名が既に存在します。";
        } else {
            // 大カテゴリの挿入
            $sql = "INSERT INTO big_category (big_category_name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $big);

            try {
                if ($stmt->execute()) {
                    echo "大カテゴリの登録完了<br>";
                } else {
                    echo "大カテゴリの登録失敗<br>";
                }
            } catch (Exception $e) {
                echo "大カテゴリの登録失敗：" . $e->getMessage() . "<br>";
            }
        }
    }

    if (isset($_POST['tyu']) && isset($_POST['tyu_submit']) && isset($_POST['dai'])) {
        $dai = $_POST['dai'];
        $chu = $_POST['tyu'];

        // 中カテゴリの重複チェック
        $check_sql = "SELECT COUNT(*) FROM category WHERE big_category_id = ? AND category_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $dai, $chu);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            echo "同じ中カテゴリ名が既に存在します。";
        } else {
            // 中カテゴリの挿入
            $middle_insert_sql = "INSERT INTO category (big_category_id, category_name) VALUES (?, ?)";
            $middle_insert_stmt = $conn->prepare($middle_insert_sql);
            $middle_insert_stmt->bind_param("is", $dai, $chu);

            try {
                if ($middle_insert_stmt->execute()) {
                    echo "中カテゴリの登録完了";
                } else {
                    echo "中カテゴリの登録失敗";
                }
            } catch (Exception $e) {
                echo "中カテゴリの登録失敗：" . $e->getMessage();
            }
        }
    }

    // 小カテゴリの登録処理
    if (isset($_POST['small_category']) && isset($_POST['small_dai']) && isset($_POST['small_submit'])) {
        $dai = $_POST['small_dai'];
        $small = $_POST['small_category'];

        // 小カテゴリの挿入
        $small_insert_sql = "INSERT INTO small_category (category_id, small_category_name) VALUES (?, ?)";
        $small_insert_stmt = $conn->prepare($small_insert_sql);
        $small_insert_stmt->bind_param("is", $dai, $small);

        try {
            if ($small_insert_stmt->execute()) {
                echo "小カテゴリの登録完了<br>";
            } else {
                echo "小カテゴリの登録失敗<br>";
            }
        } catch (Exception $e) {
            echo "小カテゴリの登録失敗：" . $e->getMessage() . "<br>";
        }
    }
}
?>

<form name="big" method="POST">
    <input type="text" name="big_category" placeholder="大カテゴリ">
    <input type="submit" name="submit" value="大カテゴリ登録">
</form>



<form name="normal" method="post">
    <select name="dai">
        <option hidden>選択してください</option>
        <?php 
        $select_sql = "SELECT big_category_id, big_category_name FROM big_category";
        $select_stmt = $conn->query($select_sql);
        if ($select_stmt) {
            while($row = $select_stmt->fetch_assoc()){
                $big_category_id = $row['big_category_id'];
                $big_category_name = $row['big_category_name'];
                echo '<option value="'.$big_category_id.'">'.$big_category_name.'</option>';
            }
        } 
        ?>
    </select>
    <input type="text" name="tyu" placeholder="中カテゴリ">
    <input type="submit" name="tyu_submit" value="中カテゴリ登録">
</form>



<form name="small" method="post">
    <select name="small_dai">
        <option hidden>選択してください</option>
        <?php 
        $tyu_sql = "SELECT c.category_id, c.category_name, b.big_category_name
                    FROM category c
                    INNER JOIN big_category b ON (c.big_category_id = b.big_category_id)";
        $tyu_stmt = $conn->query($tyu_sql);
        if($tyu_stmt){
            while($row = $tyu_stmt->fetch_assoc()){
                $category_id = $row['category_id'];
                $category_name = $row['category_name'];
                $big_category_name = $row['big_category_name'];
                echo '<option value="'.$category_id.'">'.$big_category_name . ' - ' . $category_name.'</option>';
            }
        }
        ?>
    </select>
    <input type="text" name="small_category" placeholder="小カテゴリ">
    <input type="submit" name="small_submit" value="小カテゴリ登録">
</form>



