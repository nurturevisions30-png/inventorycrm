<?php
session_start();
include "config.php";

/* ✅ VALIDATE ID */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id == 0){
    die("Invalid Quotation ID");
}

/* ✅ REMOVE PRODUCT */
if(isset($_GET['remove'])){
    $pid = (int)$_GET['remove'];

    $delete = mysqli_query($conn, "DELETE FROM quotation_items WHERE id=$pid");

    if(!$delete){
        die("Delete Error: " . mysqli_error($conn));
    }

    header("Location: edit_quotation.php?id=".$id);
    exit();
}

/* ✅ UPDATE QTY & PRICE */
if(isset($_POST['update'])){

    foreach($_POST['qty'] as $item_id => $qty){

        $item_id = (int)$item_id;
        $qty = (int)$qty;

        $price = (float)$_POST['price'][$item_id];
        $total = $qty * $price;

        $update = mysqli_query($conn, "UPDATE quotation_items 
        SET qty='$qty', price='$price', total='$total'
        WHERE id='$item_id'");

        if(!$update){
            die("Update Error: " . mysqli_error($conn));
        }
    }

   header("Location: preview_draft.php?id=".$id);
exit();
}

/* ✅ FETCH DATA */
$items = mysqli_query($conn, "SELECT * FROM quotation_items WHERE quotation_id=$id");

if(!$items){
    die("Fetch Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Quotation</title>

<style>
body {
    font-family:'Segoe UI';
    background:#f5f7fb;
}

.container {
    width:90%;
    margin:30px auto;
    background:white;
    padding:20px;
    border-radius:10px;
}

h2 {
    margin-bottom:20px;
}

table {
    width:100%;
    border-collapse: collapse;
}

th {
    background:#e5e7eb;
    padding:12px;
}

td {
    padding:12px;
    border-bottom:1px solid #eee;
}

input {
    width:80px;
    padding:6px;
}

.remove-btn {
    color:red;
    text-decoration:none;
}

button {
    margin-top:15px;
    padding:12px 20px;
    background:#16a34a;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
</style>

</head>

<body>
<?php include "sidebaar.php"; ?>
<div class="container">

<h2>Edit Quotation</h2>

<form method="POST">

<table>

<tr>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Action</th>
</tr>

<?php 
$total_sum = 0;

while($row = mysqli_fetch_assoc($items)){ 
$total_sum += $row['total'];
?>

<tr>

<td><?php echo $row['product_name']; ?></td>

<td>
<input type="number" name="qty[<?php echo $row['id']; ?>]" value="<?php echo $row['qty']; ?>">
</td>

<td>
<input type="number" name="price[<?php echo $row['id']; ?>]" value="<?php echo $row['price']; ?>">
</td>

<td>₹<?php echo $row['total']; ?></td>

<td>
<a class="remove-btn" 
href="edit_quotation.php?id=<?php echo $id; ?>&remove=<?php echo $row['id']; ?>" 
onclick="return confirm('Remove this product?')">
Remove
</a>
</td>

</tr>

<?php } ?>

<tr>
<td colspan="3" align="right"><b>Grand Total</b></td>
<td><b>₹<?php echo $total_sum; ?></b></td>
<td></td>
</tr>

</table>

<button type="submit" name="update">Update Quotation</button>

</form>

</div>

</body>
</html>