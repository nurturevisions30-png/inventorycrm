<?php
session_start();
include "config.php";

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$id = (int)$_GET['id'];

/* QUOTATION */
$q = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT * FROM quotations WHERE id=$id
"));

if(!$q){
    die("Quotation not found");
}

/* ITEMS */
$items = mysqli_query($conn, "
SELECT * FROM quotation_items 
WHERE quotation_id=$id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Draft Quotation</title>

<style>
body{
    background:#ddd;
    font-family:Arial;
}

.page{
    width:1000px;
    margin:20px auto;
    background:white;
    padding:30px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo img{
    height:70px;
}

.company{
    text-align:right;
    font-size:13px;
}

hr{
    margin:20px 0;
}

.top-info{
    display:flex;
    justify-content:space-between;
    font-size:13px;
}

/* CLIENT BLOCK */
.client-box{
    margin-top:15px;
    padding:10px;
    border:1px solid #ccc;
    font-size:13px;
    line-height:1.6;
}

.subject{
    margin-top:15px;
    font-size:13px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th,td{
    border:1px solid #999;
    padding:8px;
    font-size:12px;
    vertical-align:top;
}

th{
    background:#f1f1f1;
}

.footer{
    margin-top:30px;
    font-size:13px;
}

/* STOCK WARNING */
.stock-warning{
    color:red;
    font-size:11px;
    margin-top:6px;
    font-weight:600;
}

/* ACTION BAR */
.action-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:20px;
}

/* LEFT SIDE */
.left-actions{
    display:flex;
    gap:10px;
}

/* RIGHT SIDE */
.right-actions{
    display:flex;
}

/* COMMON BUTTON */
.btn{
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:14px;
    border:none;
    cursor:pointer;
    transition:.3s ease;
}

/* EDIT */
.edit-btn{
        background: transparent;
    border: 2px solid #000;
    color: #000;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
}

.edit-btn:hover{
       background: #000;
    color: #fff;
}

/* APPROVE */
.approve-btn{
    background:#16a34a;
}

.approve-btn:hover{
    background:#15803d;
}

/* EMAIL */
.mail-btn{
    background:#2563eb;
}

.mail-btn:hover{
    background:#1d4ed8;
}

/* FORM RESET */
.right-actions form{
    margin:0;
}
/* DOWNLOAD */
.download-btn{
        background: transparent;
    border: 2px solid #000;
    color: #000;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
}

.download-btn:hover{
      background: #000;
    color: #fff;
}

@media print{

    body{
        background:white;
    }

    .action-bar{
        display:none;
    }
}
</style>
</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="page">

<!-- HEADER -->
<div class="header">

<div class="logo">
    <img src="upload/logo.png">
</div>

<div class="company">

    <b>AS ASSOCIATES</b><br>
    HANDI CHOWK ANARTHALAYA COLONY<br> BESIDE YASH PRINTERS,RAIGARH, CHHATTISHGARH, 496001<br>
    Phone: 8839085359<br>
    Email: info.asassociates@yahoo.com

</div>

</div>

<hr>

<!-- TOP INFO -->
<div class="top-info">

<div>
    <b>Ref:</b>
    <?php echo $q['quotation_no']; ?>
</div>

<div>
    <b>Date:</b>
    <?php echo date("d-m-Y", strtotime($q['date'])); ?><br>

    <b>Status:</b>
    <?php echo $q['status']; ?>
</div>

</div>

<!-- CLIENT DETAILS -->
<div class="client-box">

<b>To,</b><br>

<b><?php echo $q['client_name']; ?></b><br>

<?php echo $q['client_company']; ?><br>

<?php echo $q['client_address'] ?? ''; ?><br>

<?php echo $q['client_city'] ?? ''; ?>
-
<?php echo $q['client_pincode'] ?? ''; ?><br>

<?php echo $q['client_state'] ?? ''; ?><br>

Phone:
<?php echo $q['client_phone'] ?? ''; ?><br>

Email:
<?php echo $q['client_email'] ?? ''; ?>

</div>

<!-- SUBJECT -->
<div class="subject">

Dear Sir,<br>

As per your requirement, please find our quotation below.

</div>

<!-- TABLE -->
<!-- TABLE -->
<table>

<tr>

<th>SL</th>
<th>Image</th>
<th>Product</th>
<th>Model</th>
<th>HSN Code</th>
<th>GST (%)</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>

</tr>

<?php

$i = 1;
$total = 0;

while($row = mysqli_fetch_assoc($items)){

    $total += $row['total'];

    /* GET PRODUCT IMAGE */
    $product_image = '';

    $model = mysqli_real_escape_string($conn, $row['model']);

    $imgQuery = mysqli_query($conn,"
    SELECT image, hsn_code, gst
    FROM products
    WHERE model='$model'
    LIMIT 1
    ");


$product_image = '';
$hsn_code = '';
$gst = '';

if(mysqli_num_rows($imgQuery) > 0){

    $imgRow = mysqli_fetch_assoc($imgQuery);

    $product_image = $imgRow['image'];
    $hsn_code = $imgRow['hsn_code'];
    $gst = $imgRow['gst'];
}

?>

<tr>

<!-- SL -->
<td>
<?php echo $i++; ?>
</td>

<!-- IMAGE -->
<td align="center">

<?php if(!empty($product_image)){ ?>

<img
src="upload/<?php echo $product_image; ?>"
style="
width:70px;
height:70px;
object-fit:cover;
border-radius:10px;
border:1px solid #ddd;
padding:2px;
background:white;
">

<?php } else { ?>

<img
src="https://via.placeholder.com/70"
style="
width:70px;
height:70px;
object-fit:cover;
border-radius:10px;
border:1px solid #ddd;
">

<?php } ?>

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

<!-- HSN -->
<td>
    <?php echo $hsn_code ?: '-'; ?>
</td>

<!-- GST -->
<td>
    <?php echo $gst != '' ? $gst.'%' : '-'; ?>
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

<!-- GRAND TOTAL -->
<tr>

<td colspan="8" align="right">

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

Packing & Forwarding :
<?php echo $q['packing']; ?><br>

Freight :
<?php echo $q['freight']; ?><br>

Delivery :
<?php echo $q['delivery']; ?><br>

Payment :
<?php echo $q['payment']; ?><br>

Validity :
<?php echo $q['validity']; ?><br><br>

<p>
<b>Note :</b>
New GST Structure Will be applicable as per GST rule at the time of Order.
</p>

<br><br><br>

Yours Thankfully,<br><br>

<b>For AS ASSOCIATES</b><br><br>

<b><?php echo $q['sign_name']; ?></b><br>

<?php echo $q['designation']; ?><br>

(M)
<?php echo $q['sign_phone']; ?>

</div>

<br><br>

<!-- ACTIONS -->
<!-- ACTIONS -->
<div class="action-bar">

    <!-- LEFT BUTTONS -->
    <div class="left-actions">

        <!-- EDIT -->
        <a
        href="edit_quotation.php?id=<?php echo $id; ?>"
        class="btn edit-btn">

        Edit

        </a>

        <!-- APPROVE -->
        <!--<a-->
        <!--href="update_status.php?id=<?php echo $id; ?>&status=Approved"-->
        <!--class="btn approve-btn">-->

        <!--Approve-->

        <!--</a>-->

        <!-- DOWNLOAD PDF -->
        <a
        href="download_pdf.php?id=<?php echo $id; ?>"
        class="btn download-btn"
        target="_blank">

        Download PDF

        </a>

    </div>

    <!-- RIGHT BUTTON -->
    <div class="right-actions">

        <form method="POST"
        action="send_mail.php?id=<?php echo $id; ?>">

            <input
            type="hidden"
            name="send_email"
            value="1">

            <button
            type="submit"
            class="btn mail-btn">

            Send Email

            </button>

        </form>

    </div>

</div>

</body>
</html>