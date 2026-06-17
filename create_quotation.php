<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config.php";

/* REMOVE ITEM */
if(isset($_GET['remove'])){

    $index = (int)$_GET['remove'];

    if(isset($_SESSION['cart'][$index])){
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    header("Location:create_quotation.php");
    exit();
}

/* INIT CART */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

/* ADD PRODUCT */
if(isset($_POST['add'])){

    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];

    if($pid == "" || $qty == ""){

        echo "<script>
        alert('Select model & enter quantity');
        </script>";

    }else{

        $p = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM products WHERE id=$pid
        "));

        if(!$p){

            echo "<script>
            alert('Product not found');
            </script>";

        }else{

            /* STOCK CHECK */
/* STOCK MESSAGE */

$stock_note = "";

if($qty > $p['ready_qty']){

    $stock_note = "Required qty not available in ready stock. Delivery may take additional time.";

}

/* AUTO PRICE LOGIC */

if($qty >= 900){
    $price = $p['slab3'];
}
elseif($qty >= 500){
    $price = $p['slab2'];
}
elseif($qty >= 300){
    $price = $p['slab1'];
}
else{
    $price = $p['msp'];
}

$total = $price * $qty;

$_SESSION['cart'][] = [

    "product"=>$p['product_category'],
    "model"=>$p['model'],
    "description"=>$p['description'],
    "image"=>$p['image'],
    "qty"=>$qty,
    "price"=>$price,
    "total"=>$total,
    "stock_note"=>$stock_note

];

        }

    }
}

/* CATEGORY LIST */
$cats = mysqli_query($conn,"
SELECT DISTINCT product_category
FROM products
ORDER BY product_category ASC
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Create Quotation</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI';
}

body{
    background:#f4f7fb;
}
.manual-box{
    background:white;
    padding:20px;
    border-radius:20px;
    margin-top:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.manual-box input{
    width:250px;
}
/* PAGE */
.page{
    padding:25px;
}

/* TITLE */

.heading{
    margin-bottom:20px;
}

.heading h1{
    font-size:30px;
    color:#111827;
}

.heading p{
    color:#6b7280;
    margin-top:6px;
}

/* TOP BOX */

.top-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    display:flex;
    gap:15px;
    align-items:end;
    /* flex-wrap:wrap; */
}

/* INPUTS */

select,
input{
    height:50px;
    padding:0 14px;
    border:1px solid #ddd;
    border-radius:12px;
    /* min-width:240px; */
    outline:none;
    font-size:14px;
    background:white;
        width: 234px;
}

/* BUTTON */

.add-btn{
    height:50px;
    padding:0 22px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#16a34a,#15803d);
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.add-btn:hover{
    transform:translateY(-2px);
}

/* PRODUCT INFO */

.product-preview{
    margin-top:20px;
    background:white;
    border-radius:20px;
    padding:20px;
    display:none;
    align-items:center;
    gap:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.product-preview img{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:15px;
    border:1px solid #eee;
}

.preview-info h3{
    color:#111827;
    margin-bottom:8px;
}

.preview-info p{
    color:#6b7280;
    line-height:1.6;
}

.price-box{
    margin-top:10px;
    font-weight:700;
    color:#16a34a;
}

/* TABLE */

.table-box{
    margin-top:25px;
    background:white;
    border-radius:20px;
    padding:20px;
    overflow:auto;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#111827;
    color:white;
    padding:14px;
    font-size:13px;
    text-align:left;
}

td{
    padding:14px;
    border-bottom:1px solid #f1f1f1;
    font-size:14px;
    vertical-align:middle;
}

tr:hover{
    background:#f9fafb;
}

/* IMAGE */

.table-img{
    width:55px;
    height:55px;
    border-radius:10px;
    object-fit:cover;
}

/* TOTAL */

.total{
    font-weight:700;
    color:#111827;
}

/* REMOVE */

.remove-btn{
    color:#dc2626;
    font-size:18px;
    text-decoration:none;
}

/* NEXT BUTTON */

.next-area{
    margin-top:20px;
    display:flex;
    justify-content:flex-end;
}

.next-btn{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    border:none;
    padding:14px 26px;
    border-radius:14px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

/* RESPONSIVE */

@media(max-width:768px){

    .top-box{
        flex-direction:column;
        align-items:stretch;
    }

    select,
    input,
    .add-btn{
        width:100%;
    }

}

</style>
</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="page">

<!-- TITLE -->
<div class="heading">

    <h1>Create Quotation</h1>
    <p>Select category → model → quantity</p>

</div>

<!-- FORM -->
<form method="POST" class="top-box">

    <!-- CATEGORY -->
    <select id="category">

        <option value="">Select Category</option>

        <?php while($c = mysqli_fetch_assoc($cats)){ ?>

        <option value="<?php echo $c['product_category']; ?>">

            <?php echo $c['product_category']; ?>

        </option>

        <?php } ?>

    </select>

    <!-- MODEL -->
    <select name="product_id" id="modelDropdown" required>

        <option value="">Select Model</option>

    </select>

    <!-- QTY -->
    <input
    type="number"
    name="qty"
    id="qtyInput"
    placeholder="Enter Qty"
    required>

    

    <!-- BUTTON -->
    <button class="add-btn" name="add">

        <i class="fa-solid fa-plus"></i>
        Add Product

    </button>
    

</form>
<!-- MANUAL PRODUCT SECTION -->

<div class="table-box" style="margin-top:20px;">

    <h3 style="
    margin-bottom:15px;
    color:#111827;
    ">
    Manual Product Entry
    </h3>

    <div style="
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    ">

        <input
        type="text"
        name="manual_product"
        placeholder="Product Name">

        <input
        type="text"
        name="manual_model"
        placeholder="Model">

        <input
        type="text"
        name="manual_description"
        placeholder="Description">

        <input
        type="number"
        name="manual_price"
        placeholder="Unit Price">

    </div>
    <!-- BUTTON -->
    <button class="add-btn" name="add">

        <i class="fa-solid fa-plus"></i>
        Add Product

    </button>

</div>
<!-- PRODUCT PREVIEW -->
<div class="product-preview" id="previewBox">

    <img src="" id="previewImg">

    <div class="preview-info">

        <h3 id="previewModel"></h3>

        <p id="previewDesc"></p>

        <div class="price-box" id="previewPrice"></div>

        <div class="price-box" id="previewStock"></div>

    </div>

</div>

<!-- TABLE -->
<div class="table-box">

<table>

<tr>

<th>#</th>
<th>Image</th>
<th>Category</th>
<th>Model</th>
<th>Description</th>
<th>Qty</th>
<th>Unit Price</th>
<th>Total</th>
<th>Action</th>

</tr>

<?php

$i=1;
$grand = 0;

foreach($_SESSION['cart'] as $key=>$item){

$grand += $item['total'];

?>

<tr>

<td><?php echo $i++; ?></td>

<td>
<img
src="upload/<?php echo $item['image']; ?>"
class="table-img">
</td>

<td>
<?php echo $item['product']; ?>
</td>

<td>
<b><?php echo $item['model']; ?></b>
</td>

<td>
<?php echo substr($item['description'],0,80); ?>...
<?php if(!empty($item['stock_note'])){ ?>

<div style="color:red;font-size:12px;margin-top:5px;">
    <?php echo $item['stock_note']; ?>
</div>

<?php } ?>
</td>

<td>
<?php echo $item['qty']; ?>
</td>

<td>
₹<?php echo number_format($item['price']); ?>
</td>

<td class="total">
₹<?php echo number_format($item['total']); ?>
</td>

<td>

<a
href="create_quotation.php?remove=<?php echo $key; ?>"
class="remove-btn"
onclick="return confirm('Remove product?')">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

<tr>

<td colspan="7" align="right">

<b>Grand Total</b>

</td>

<td class="total">

₹<?php echo number_format($grand); ?>

</td>

<td></td>

</tr>

</table>

</div>

<!-- NEXT -->
<div class="next-area">

<a href="proposal.php">

<button type="button" class="next-btn">

Next Step

</button>

</a>

</div>

</div>

<script>

/* CATEGORY CHANGE */

document.getElementById("category").addEventListener("change", function(){

    let category = this.value;

    fetch("get_models.php?category=" + category)

    .then(response => response.json())

    .then(data => {

        let modelDropdown =
        document.getElementById("modelDropdown");

        modelDropdown.innerHTML =
        '<option value="">Select Model</option>';

        data.forEach(function(item){

            modelDropdown.innerHTML += `
            <option value="${item.id}">
                ${item.model}
            </option>
            `;

        });

    });

});

/* MODEL CHANGE */

document.getElementById("modelDropdown").addEventListener("change", function(){

    let pid = this.value;

    fetch("get_product.php?id=" + pid)

    .then(response => response.json())

    .then(data => {

        document.getElementById("previewBox").style.display = "flex";

        document.getElementById("previewImg").src =
        "upload/" + data.image;

        document.getElementById("previewModel").innerHTML =
        data.model;

        document.getElementById("previewDesc").innerHTML =
        data.description;

        document.getElementById("previewPrice").innerHTML =
        "MSP Price : ₹" + data.msp;

        document.getElementById("previewStock").innerHTML =
        "Available Stock : " + data.ready_qty;

    });

});


</script>

</body>
</html>