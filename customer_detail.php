<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include "config.php";

if(!isset($_GET['email'])){
    die("Invalid Request");
}

$email = $_GET['email'];

// FETCH CUSTOMER QUOTES
$quotes = mysqli_query($conn, "
SELECT * FROM quotations 
WHERE client_email='$email'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Details</title>

<style>
body {
    font-family:'Segoe UI';
    background:#f1f5f9;
}

.container {
    padding:20px;
}

table {
    width:100%;
    border-collapse: collapse;
    background:white;
    border-radius:10px;
}

th {
    background:#6366f1;
    color:white;
    padding:12px;
}

td {
    padding:12px;
    border-bottom:1px solid #eee;
}

a {
    text-decoration:none;
    color:#2563eb;
}
</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="container">

<h2>Customer Quotations</h2><br>

<table>

<tr>
<th>Quotation No</th>
<th>Date</th>
<th>Status</th>
<th>Amount</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($quotes)){ ?>

<tr>

<td><?php echo $row['quotation_no']; ?></td>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['status']; ?></td>
<td>₹<?php echo $row['total_amount']; ?></td>

<td>
<a href="preview_draft.php?id=<?php echo $row['id']; ?>">View</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>