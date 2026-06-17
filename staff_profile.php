<?php
session_start();
include "config.php";

// 🔐 ADMIN ONLY
if($_SESSION['role'] != 'admin'){
    header("Location: dashboard.php");
    exit();
}

// GET ID
if(!isset($_GET['id'])){
    die("Invalid Request");
}

$id = (int)$_GET['id'];

// 👤 USER DATA
$user = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT * FROM users WHERE id=$id
"));

if(!$user){
    die("Staff not found");
}

// 📊 STATS
$total_quotes = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as t FROM quotations WHERE created_by=$id
"))['t'];

$revenue = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT IFNULL(SUM(total_amount),0) as t FROM quotations WHERE created_by=$id AND status='Approved'
"))['t'];

$approved = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as t FROM quotations WHERE created_by=$id AND status='Approved'
"))['t'];

$draft = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as t FROM quotations WHERE created_by=$id AND status='Draft'
"))['t'];

// 📄 RECENT QUOTES
$quotes = mysqli_query($conn, "
SELECT * FROM quotations WHERE created_by=$id ORDER BY id DESC LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Profile</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>
body {
    font-family:'Segoe UI';
    background:#f5f7fb;
    margin:0;
}

.container {
    padding:20px;
}

/* HEADER */
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

/* PROFILE CARD */
.profile {
    background:white;
    padding:20px;
    border-radius:12px;
    display:flex;
    align-items:center;
    gap:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

.avatar {
    width:70px;
    height:70px;
    border-radius:50%;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    font-weight:bold;
}

.info h2 {
    margin:0;
}

.info p {
    margin:5px 0;
    color:#6b7280;
}

/* STATS */
.stats {
    margin-top:20px;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
    gap:15px;
}

.card {
    background:white;
    padding:20px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

.card h2 {
    margin:0;
}

.card p {
    margin:5px 0;
    color:#6b7280;
}

/* TABLE */
.table-box {
    margin-top:20px;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

table {
    width:100%;
    border-collapse:collapse;
}

th, td {
    padding:10px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

th {
    background:#f1f5f9;
    text-align:left;
}

.status {
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.approved { background:#dcfce7; color:#16a34a; }
.draft { background:#fef3c7; color:#d97706; }
.rejected { background:#fee2e2; color:#dc2626; }

</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="container">

<!-- PROFILE -->
<div class="profile">

    <div class="avatar">
        <?php echo strtoupper(substr($user['name'],0,1)); ?>
    </div>

    <div class="info">
        <h2><?php echo $user['name']; ?></h2>
        <p><?php echo $user['email']; ?></p>
        <p>Status: <b><?php echo ucfirst($user['status']); ?></b></p>
    </div>

</div>

<!-- STATS -->
<div class="stats">

<div class="card">
    <h2><?php echo $total_quotes; ?></h2>
    <p>Total Quotes</p>
</div>

<div class="card">
    <h2><?php echo $approved; ?></h2>
    <p>Approved</p>
</div>

<div class="card">
    <h2><?php echo $draft; ?></h2>
    <p>Draft</p>
</div>

<div class="card">
    <h2>₹<?php echo $revenue; ?></h2>
    <p>Revenue</p>
</div>

</div>

<!-- TABLE -->
<div class="table-box">

<h3>Recent Quotations</h3>

<table>
<tr>
<th>Quotation No</th>
<th>Date</th>
<th>Status</th>
<th>Amount</th>
</tr>

<?php while($row = mysqli_fetch_assoc($quotes)){ ?>

<tr>
<td><?php echo $row['quotation_no']; ?></td>
<td><?php echo $row['date']; ?></td>
<td>
<span class="status <?php echo strtolower($row['status']); ?>">
<?php echo $row['status']; ?>
</span>
</td>
<td>₹<?php echo $row['total_amount']; ?></td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>