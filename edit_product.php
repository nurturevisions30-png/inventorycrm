<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// GET PRODUCT ID
$id = (int)$_GET['id'];

// FETCH DATA
$result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$product = mysqli_fetch_assoc($result);

// UPDATE LOGIC
if(isset($_POST['update'])){

    $group = $_POST['group'];
    $category = $_POST['category'];
    $name = $_POST['name'];
    $code = $_POST['code'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];

    mysqli_query($conn, "UPDATE products SET 
        product_group='$group',
        product_category='$category',
        product_name='$name',
        product_code='$code',
        price='$price',
        qty='$qty'
        WHERE id=$id");

    header("Location: manage_products.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<style>
body {
    background: #f5f7fb;
    font-family: 'Segoe UI';
}

.container {
    width: 500px;
    margin: 50px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
}

h2 {
    margin-bottom: 15px;
}

input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 12px;
    background: #16a34a;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.back {
    display: inline-block;
    margin-bottom: 10px;
    color: #2563eb;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="container">

<a href="manage_products.php" class="back">← Back to Products</a>

<h2>Edit Product</h2>

<form method="POST">

<select name="group" required>
    <option <?php if($product['product_group']=="Head Protection") echo "selected"; ?>>Head Protection</option>
    <option <?php if($product['product_group']=="Hand Protection") echo "selected"; ?>>Hand Protection</option>
    <option <?php if($product['product_group']=="Foot Protection") echo "selected"; ?>>Foot Protection</option>
    <option <?php if($product['product_group']=="Body Protection") echo "selected"; ?>>Body Protection</option>
</select>

<input type="text" name="category" value="<?php echo $product['product_category']; ?>" required>

<input type="text" name="name" value="<?php echo $product['product_name']; ?>" required>

<input type="text" name="code" value="<?php echo $product['product_code']; ?>" required>

<input type="number" name="price" value="<?php echo $product['price']; ?>" required>

<input type="number" name="qty" value="<?php echo $product['qty']; ?>" required>

<button name="update">Update Product</button>

</form>

</div>

</body>
</html>