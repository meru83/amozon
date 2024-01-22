<?php
include "../db_config.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $uProduct_id = $_POST['product_id'];
    $uProductname = $_POST['productname'];
    $uView = $_POST['view'];
    $uSeller_id = $_POST['seller_id'];
    $uBig_category_id = $_POST['big_category'];
    $uCategory_id = $_POST['category'];
    $uSmall_category = $_POST['small_category'];

    $uSql = "UPDATE products 
            SET productname = ?, view = ?, seller_id = ?, big_category_id = ?, category_id = ?, small_category = ?
            WHERE product_id = ?";
    $uStmt = $conn->prepare($uSql);
    $uStmt->bind_param("sssiiii",$uProductname,$uView,$uSeller_id,$uBig_category_id,$uCategory_id,$uSmall_category,$uProduct_id);
    if($uStmt->execute()){
        echo "更新完了";
    }
}


$sql = "SELECT p.product_id, p.productname, p.view, p.seller_id, p.big_category_id, p.category_id, p.small_category, b.big_category_name, c.category_name, s.small_category_name
        FROM products p
        INNER JOIN big_category b ON p.big_category_id = b.big_category_id
        INNER JOIN category c ON p.category_id = c.category_id
        INNER JOIN small_category s ON p.small_category = s.small_category";
$result = $conn->query($sql);

$product_id;
$productname;
$view;
$seller_id;
$big_category_id1;
$category_id1;
$small_category_id1;
$big_category_name1;
$category_name1;
$small_category_name1;
?>

<h1>商品情報更新</h1>

<?php
if($result){
    foreach($result as $row){
        $product_id = $row['product_id'];
        $productname = $row['productname'];
        $view = $row['view'];
        $seller_id = $row['seller_id'];
        $big_category_id1 = $row['big_category_id'];
        $category_id1 = $row['category_id'];
        $small_category_id1 = $row['small_category'];
        $big_category_name1 = $row['big_category_name'];
        $category_name1 = $row['category_name'];
        $small_category_name1 = $row['small_category_name'];
        ?>

        <form method="post" name="form" id="form">
            <p><?=$product_id?></p>
            <input type="hidden" name="product_id" id="product_id" value="<?=$product_id?>">
            <label for="productname">
            <b>商品名</b>　：
                <input type="text" name="productname" id="productname" value="<?=$productname?>" placeholder="商品名" required>
            </label><br>
            <label for="view">
            <b>概要</b>　：
                <textarea name="view" id="view" cols="25" rows="10" placeholder="概要"><?=$view?></textarea>
            </label><br>
            <label for="seller_id">
            <b>販売者ID</b>:
                <input type="text" name="seller_id" id="seller_id" value="<?=$seller_id?>" placeholder="id" required>
            </label><br><br>

            <label for="big_category">
            <b>大カテゴリ</b>：
                <select name="big_category" id="big_category">
                    <option hidden value="<?=$big_category_id1?>"><?=$big_category_name1?></option>
                    <?php 
                    $big_sql = "SELECT big_category_id, big_category_name FROM big_category";
                    $big_stmt = $conn->query($big_sql);
                    if ($big_stmt) {
                        while($row = $big_stmt->fetch_assoc()){
                            $big_category_id = $row['big_category_id'];
                            $big_category_name = $row['big_category_name'];
                            echo '<option value="'.$big_category_id.'">'.$big_category_name.'</option>';
                        }
                    } 
                    ?>
                </select>
            </label><br>
            <label for="category" id="categoryLabel" style="display:block;">
                中カテゴリ：
                <select name="category" id="category">
                    <option hidden value="<?=$category_id1?>"><?=$big_category_name1?>-<?=$category_name1?></option>
                </select>
            </label><br>
            <label for="small_category" id="smallCategoryLabel">
                小カテゴリ：
                <select name="small_category" id="small_category" style="display:block;">
                    <option hidden value="<?=$small_category_id1?>"><?=$category_name1?>-<?=$small_category_name1?></option>
                </select>
            </label><br><br><br>

            <input type="submit" name="submit" id="submit" value="送信">
        </form>
        <hr>
    <?php 
    }
}else{
    echo "商品情報がありません。";
}
?>

<script>
const form = document.getElementById('form');
const productname = document.getElementById('productname');
const view = document.getElementById('view');
const seller_id = document.getElementById('seller_id');
const big_category = document.getElementById('big_category');
const categoryLabel = document.getElementById('categoryLabel');
const category = document.getElementById('category');
const small_category = document.getElementById('small_category');
const smallCategoryLabel = document.getElementById('smallCategoryLabel');

big_category.addEventListener('change', (e) => {
    let num = big_category.selectedIndex;
    let b_id = big_category.options[num].value;

    const formData = new FormData();
    formData.append('big_category', b_id);
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'p2_big.php', true);
    xhr.send(formData);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    // console.log(response);
                    if (response) {
                        // 成功した場合の処理を記述
                        category.innerHTML = '<option hidden>選択してください</option>';
                        response.forEach(function(row) {
                            category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        smallCategoryLabel.style.display = "none";
                    } else {
                        console.error("Invalid or empty response data");
                    }
                } catch (error) {
                    console.error("Error parsing JSON response: " + error.message);
                }
            } else {
                console.error("Error: " + xhr.status);
            }
        }
    }
});

category.addEventListener('change', (e) => {
    let num = category.selectedIndex;
    let c_id = category.options[num].value;

    const formData = new FormData();
    formData.append('category', c_id);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'p2_cate.php',true);
    xhr.send(formData);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4){
            if(xhr.status === 200){
                try{
                    const response = JSON.parse(xhr.responseText);
                    if(response){
                        small_category.innerHTML = '<option hidden>選択してください</option>';
                        response.forEach(function(row){
                            small_category.innerHTML += `<option value="${row.value}">${row.text}</option>`;
                        });
                        smallCategoryLabel.style.display = "block";
                    }else{
                        console.error("Error parsing JSON response data");
                    }
                }catch(error){
                    console.error("Error parsing JSON response: " + error.message);
                }
            }else{
                console.error("Error: " + xhr.status);
            }
        }
    }
});
</script>