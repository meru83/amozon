<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/search_style.css">
    <title>商品検索</title>
</head>

<body>
    <h1>商品検索</h1>
    <form id="form" action="search_results.php" method="GET">
    <div class="flexBox">
        <label for="search">商品を検索</label>
        <input type="text" id="search" name="search">
        <button type="submit" id="submit" class="btn-img"></button>
    </div>
    </form>
</body>
</html>
<script>
const search = document.getElementById('search');
const submit = document.getElementById('submit');
const form = document.getElementById('form');
form.addEventListener('submit',(e) => {
    let searchValue = search.value;
    let str = searchValue.replace(/\s+/g, "");
    console.log(str);
    if(str === null || str === ""){
        e.preventDefault();
        return false;
    }else{
        return true;
    }
});
</script>