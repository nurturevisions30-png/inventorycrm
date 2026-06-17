<?php
session_start();
include "config.php";

if($_SESSION['role'] != 'staff'){
    header("Location: dashboard.php");
    exit();
}

/* STAFF NAME */
$uid = $_SESSION['user_id'];

/* TOTAL QUOTATIONS */
$total = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t 
FROM quotations 
WHERE created_user_id='$uid'"))['t'];

/* APPROVED */
$approved = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t 
FROM quotations 
WHERE created_user_id='$uid'
AND status='Approved'"))['t'];

/* DRAFT */
$draft = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t 
FROM quotations 
WHERE created_user_id='$uid'
AND status='Draft'"))['t'];

/* REVENUE */
$revenue = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT IFNULL(SUM(total_amount),0) as t 
FROM quotations 
WHERE created_user_id='$uid'
AND status='Approved'"))['t'];

/* RECENT QUOTATIONS */
$q = mysqli_query($conn,
"SELECT * FROM quotations
WHERE created_user_id='$uid'
ORDER BY id DESC
LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>
body { font-family:'Segoe UI'; background:#f5f7fb; margin:0; }

.container { padding:20px; }

.cards {
    display:grid;
    grid-template-columns: repeat(4,1fr);
    gap:15px;
}

.card {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.blue { color:#2563eb; }
.green { color:#16a34a; }

/* TABLE */
.table-box {
    margin-top:25px;
    background:white;
    padding:20px;
    border-radius:12px;
}

table {
    width:100%;
    border-collapse:collapse;
}

td {
    padding:10px;
    border-bottom:1px solid #eee;
}
</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="container">

<h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2><br>

<!-- CARDS -->
<div class="cards">

<div class="card">
    <h4>Total Quotations</h4>
    <h2><?php echo $total; ?></h2>
</div>

<div class="card">
    <h4>Approved</h4>
    <h2 class="green"><?php echo $approved; ?></h2>
</div>

<div class="card">
    <h4>Draft</h4>
    <h2 class="blue"><?php echo $draft; ?></h2>
</div>

<div class="card">
    <h4>Revenue</h4>
    <h2>₹<?php echo $revenue; ?></h2>
</div>

</div>

<!-- ACTION -->
<div style="margin-top:20px;">
    <a href="create_quotation.php">
        <button style="padding:10px 20px;background:#16a34a;color:white;border:none;border-radius:6px;">
            Create Quotation
        </button>
    </a>
</div>

<!-- TABLE -->
<div class="table-box">

<h3>My Recent Quotations</h3><br>

<table>

<?php while($row = mysqli_fetch_assoc($q)){ ?>

<tr>
<td><?php echo $row['quotation_no']; ?></td>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['status']; ?></td>
<td>₹<?php echo $row['total_amount']; ?></td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>