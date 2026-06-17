<?php
include "config.php";

$id = (int)$_GET['id'];

// QUOTATION
$q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quotations WHERE id=$id"));

if(!$q){
    die("Quotation not found");
}

// ITEMS
$items = mysqli_query($conn, "SELECT * FROM quotation_items WHERE quotation_id=$id");
?>

<!DOCTYPE html>
<html>
<head>


<style>
body { background:#ddd; font-family: Arial; }
html, body {
    height: auto;
}
.page {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 15px 20px;
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.section-gap {
    margin-top: 10px;
}

.logo img { height:70px; }

.company { text-align:right; font-size:13px; }

hr { margin:20px 0; }

.top-info {
    display:flex;
    justify-content:space-between;
    font-size:13px;
}

/* CLIENT BLOCK */
.client-box {
    margin-top:10px;
    padding:8px;
    border:1px solid #ccc;
    font-size:12px;
    line-height:1.4;
}

.subject {
    margin-top:15px;
    font-size:13px;
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

th, td {
    border:1px solid #999;
    padding:5px 6px;
    font-size:11px;
}

th { background:#f1f1f1; }

.footer {
    margin-top:30px;
    font-size:13px;
}
tr {
    page-break-inside: avoid;
}
table img{
    border-radius:4px;
    object-fit:cover;
    height:60px;
    width:60px;
}
@media print {
    body { background:white; }
}
</style>
</head>

<body>



<div class="page">

<!-- HEADER -->
<div class="header">

<div class="logo">
    <?php
$logo = 'upload/logo.png';
$type = pathinfo($logo, PATHINFO_EXTENSION);
$data = file_get_contents($logo);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>

<img src="<?php echo $base64; ?>" height="70">
</div>

<div class="company">
    <b>AS ASSOCIATES</b><br>
    Korba, Chhattisgarh<br>
    Phone: XXXXX<br>
    Email: info@company.com
</div>

</div>

<hr>

<!-- TOP INFO -->
<div class="top-info">

<div>
    <b>Ref:</b> <?php echo $q['quotation_no']; ?>
</div>

<div>
    <b>Date:</b> <?php echo date("d-m-Y", strtotime($q['date'])); ?><br>
    <b>Status:</b> <?php echo $q['status']; ?>
</div>

</div>

<!-- CLIENT DETAILS -->
<div class="client-box">

<b>To,</b><br>

<b><?php echo $q['client_name']; ?></b><br>
<?php echo $q['client_company']; ?><br>

<?php echo $q['client_address'] ?? ''; ?><br>
<?php echo $q['client_city'] ?? ''; ?> - <?php echo $q['client_pincode'] ?? ''; ?><br>
<?php echo $q['client_state'] ?? ''; ?><br>

Phone: <?php echo $q['client_phone'] ?? ''; ?><br>
Email: <?php echo $q['client_email'] ?? ''; ?>

</div>

<!-- SUBJECT -->
<div class="subject">
Dear Sir,<br>
As per your requirement, please find our quotation below.
</div>

<!-- TABLE -->
<table>

<tr>
<th>SL</th>
<th>Image</th>
<th>Product</th>
<th>Model</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>

<?php

$i = 1;
$total = 0;

while($row = mysqli_fetch_assoc($items)){

$total += $row['total'];

/* GET IMAGE FROM PRODUCTS TABLE */
$product_image = '';

$model = mysqli_real_escape_string($conn, $row['model']);

$getImage = mysqli_query($conn,"
SELECT image
FROM products
WHERE model='$model'
LIMIT 1
");

if(mysqli_num_rows($getImage) > 0){

    $imgRow = mysqli_fetch_assoc($getImage);

    $product_image = $imgRow['image'];
}

?>

<tr>

<td>
<?php echo $i++; ?>
</td>

<!-- IMAGE -->
<td align="center">

<?php

if(!empty($product_image)){

$imagePath = __DIR__ . "/upload/" . $product_image;

if(file_exists($imagePath)){

$type = pathinfo($imagePath, PATHINFO_EXTENSION);

$data = file_get_contents($imagePath);

$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

?>

<img
src="<?php echo $base64; ?>"
style="
width:70px;
height:70px;
object-fit:cover;
border-radius:6px;
border:1px solid #ccc;
">

<?php

}else{

echo "No Image";
}

}else{

echo "No Image";
}

?>

</td>

<!-- PRODUCT -->
<td>

<b>
<?php echo $row['product_name']; ?>
</b>

<br>

<?php echo $row['description']; ?>

</td>

<!-- MODEL -->
<td>

<?php echo $row['model']; ?>

</td>

<!-- QTY -->
<td>

<?php echo $row['qty']; ?>

</td>

<!-- PRICE -->
<td>

₹<?php echo number_format($row['price']); ?>

</td>

<!-- TOTAL -->
<td>

<b>
₹<?php echo number_format($row['total']); ?>
</b>

</td>

</tr>

<?php } ?>

<tr>

<td colspan="6" align="right">

<b>Grand Total</b>

</td>

<td>

<b>
₹<?php echo number_format($total); ?>
</b>

</td>

</tr>

</table>
<br><br>

<!-- TERMS -->
<div class="footer">

<b>Commercial Terms & Conditions</b><br><br>

Prices : Ex Warehouse<br>
TCS : TCS 1% will be extra, if applicable<br>
Packing & Forwarding : <?php echo $q['packing']; ?><br>
Freight : <?php echo $q['freight']; ?><br>
Delivery : <?php echo $q['delivery']; ?><br>
Payment : <?php echo $q['payment']; ?><br>
Validity : <?php echo $q['validity']; ?><br><br>

<p><b>Note :</b> New GST Structure Will be applicable as per GST rule at the time of Order.</p>

<div style="margin-top:15px;">

Yours Thankfully,<br><br>

<b>For AS ASSOCIATES</b><br><br>

<b><?php echo $q['sign_name']; ?></b><br>
<?php echo $q['designation']; ?><br>
(M) <?php echo $q['sign_phone']; ?>

</div>
<br><br>


</body>
</html>