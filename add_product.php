<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add'])){

    $group = $_POST['group'];
    $category = $_POST['category'];
    $name = $_POST['name'];
    $model = $_POST['model'];
    $desc = $_POST['description'];
    $ready_qty = $_POST['ready_qty'];
    $purchase_price = $_POST['purchase_price'];

    // ✅ AUTO CALCULATE TOTAL
    $total = $ready_qty * $purchase_price;

    $query = "INSERT INTO products 
    ( product_category,  model, description, ready_qty, total)
    VALUES 
    ('$category',  '$model', '$desc', '$ready_qty',  '$total')";

    mysqli_query($conn, $query);

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>

<style>
body {
    background: #f5f7fb;
    font-family: 'Segoe UI';
}

.container {
    width: 600px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
}

h2 {
    margin-bottom: 15px;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

textarea {
    height: 80px;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.upload-btn {
    background:#16a34a;
    margin-bottom:10px;
}
</style>

<script>
// 🔥 AUTO CALCULATE TOTAL
function calculateTotal(){
    let qty = document.getElementById('ready_qty').value;
    let price = document.getElementById('purchase_price').value;

    let total = qty * price;
    document.getElementById('total').value = total;
}
</script>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="container">
    <h2>Add Product</h2>

    <a href="upload_products.php">
        <button class="upload-btn">Upload CSV</button>
    </a>

    <form method="POST">

        <!-- Product Group -->
        <select name="group" required>
            <option value="">Select Product Group</option>
            <option>Head Protection</option>
            <option>Hand Protection</option>
            <option>Foot Protection</option>
            <option>Body Protection</option>
        </select>

        <!-- Category -->
        <input type="text" name="category" placeholder="Product Category" required>

        <!-- Product Name -->
        <input type="text" name="name" placeholder="Product Name" required>

        <!-- Model -->
        <input type="text" name="model" placeholder="Model">

        <!-- Description -->
        <textarea name="description" placeholder="Product Description"></textarea>

        <!-- Ready Qty -->
        <input type="number" id="ready_qty" name="ready_qty" placeholder="Ready Quantity" onkeyup="calculateTotal()" required>

        <!-- Purchase Price -->
        <input type="number" id="purchase_price" name="purchase_price" placeholder="Purchase Price" onkeyup="calculateTotal()" required>

        <!-- Total -->
        <input type="number" id="total" placeholder="Total (Auto)" readonly>

        <button name="add">Add Product</button>

    </form>
</div>

</body>
</html>