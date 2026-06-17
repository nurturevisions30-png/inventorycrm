<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
if($_SESSION['role'] != 'admin'){
    die("Access Denied");
}
include "config.php";

// ================= DATA =================

// TOTAL
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM quotations"))['t'];

// DRAFT
$draft = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM quotations WHERE status='Draft'"))['t'];

// APPROVED
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM quotations WHERE status='Approved'"))['t'];

// TODAY
$today = date('Y-m-d');

$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COALESCE(SUM(total_amount),0) as t
FROM quotations
WHERE status='Approved'
AND DATE(date)=CURDATE()
"))['t'];

// MONTH
$month = date('Y-m');

$monthly_revenue = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT SUM(total_amount) as t FROM quotations 
WHERE status='Approved' AND DATE_FORMAT(date,'%Y-%m')='$month'
"))['t'] ?? 0;

// TOTAL SALES
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT SUM(qty) as t FROM quotation_items
"))['t'] ?? 0;

// TOTAL REVENUE
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT SUM(total_amount) as t FROM quotations WHERE status='Approved'
"))['t'] ?? 0;

// PROFIT (20% assumed)
$profit = $total_revenue * 0.20;


// ================= TOP PRODUCTS =================
$top_products = mysqli_query($conn, "
SELECT product_name, SUM(qty) as total_qty 
FROM quotation_items 
GROUP BY product_name 
ORDER BY total_qty DESC LIMIT 5
");

// ================= TOP CLIENTS =================
$top_clients = mysqli_query($conn, "
SELECT client_name, SUM(total_amount) as revenue 
FROM quotations 
WHERE status='Approved'
GROUP BY client_name 
ORDER BY revenue DESC LIMIT 5
");

// ================= CHART =================
$chart = mysqli_query($conn, "
SELECT date, SUM(total_amount) as total 
FROM quotations 
WHERE status='Approved'
GROUP BY date ORDER BY date ASC
");

$dates = [];
$revenues = [];

while($row = mysqli_fetch_assoc($chart)){
    $dates[] = date("d M", strtotime($row['date']));
    $revenues[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Professional Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body {
    font-family: 'Poppins', sans-serif;
    background: #f1f5f9;
    margin:0;
}

/* CONTAINER */
.container {
    padding:25px;
}

/* TITLE */
.title {
    font-size:26px;
    font-weight:700;
    margin-bottom:25px;
}

/* GRID */
.cards {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

/* CARD */
.card {
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
    position:relative;
    transition:0.3s;
}

.card:hover {
    transform: translateY(-6px);
}

.card.total { border-left:5px solid #6366f1; }
.card.draft { border-left:5px solid #f59e0b; }
.card.approved { border-left:5px solid #10b981; }
.card.today { border-left:5px solid #3b82f6; }
.card.month { border-left:5px solid #ec4899; }
.card.sales { border-left:5px solid #0ea5e9; }
.card.profit { border-left:5px solid #22c55e; }

.card h4 {
    font-size:13px;
    color:#64748b;
}

.card h2 {
    margin-top:10px;
    font-size:22px;
    font-weight:700;
}

.card i {
    position:absolute;
    right:15px;
    bottom:15px;
    font-size:28px;
    color:#cbd5e1;
}

/* BOX */
.box {
    background:white;
    padding:20px;
    border-radius:15px;
    margin-top:25px;
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
}

/* FLEX */
.flex {
    display:flex;
    gap:20px;
    margin-top:20px;
}

.flex .box {
    flex:1;
}

/* LIST */
.box p {
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #f1f5f9;
}

canvas {
    max-height:300px;
}

</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="container">

<div class="title">Reports Dashboard</div>

<!-- CARDS -->
<div class="cards">

<div class="card total">
<h4>Total Quotations</h4>
<h2><?php echo $total; ?></h2>
<i class="fa-solid fa-file-lines"></i>
</div>

<div class="card draft">
<h4>Pending</h4>
<h2><?php echo $draft; ?></h2>
<i class="fa-solid fa-clock"></i>
</div>

<div class="card approved">
<h4>Approved</h4>
<h2><?php echo $approved; ?></h2>
<i class="fa-solid fa-circle-check"></i>
</div>

<div class="card today">
<h4>Today Revenue</h4>
<h2>₹<?php echo number_format($today_revenue); ?></h2>
<i class="fa-solid fa-chart-line"></i>
</div>

<div class="card month">
<h4>Monthly Revenue</h4>
<h2>₹<?php echo number_format($monthly_revenue); ?></h2>
<i class="fa-solid fa-wallet"></i>
</div>

<div class="card sales">
<h4>Total Sales</h4>
<h2><?php echo $total_sales; ?></h2>
<i class="fa-solid fa-cart-shopping"></i>
</div>

<div class="card profit">
<h4>Total Profit</h4>
<h2>₹<?php echo number_format($profit); ?></h2>
<i class="fa-solid fa-indian-rupee-sign"></i>
</div>

</div>

<!-- CHART -->
<div class="box">
<h3>Daily Revenue</h3>
<canvas id="chart"></canvas>
</div>

<!-- PRODUCTS + CLIENTS -->
<div class="flex">

<div class="box">
<h3>Top Products</h3>
<?php while($row = mysqli_fetch_assoc($top_products)){ ?>
<p>
<span><?php echo $row['product_name']; ?></span>
<b><?php echo $row['total_qty']; ?></b>
</p>
<?php } ?>
</div>

<div class="box">
<h3>Top Clients</h3>
<?php while($row = mysqli_fetch_assoc($top_clients)){ ?>
<p>
<span><?php echo $row['client_name']; ?></span>
<b>₹<?php echo number_format($row['revenue']); ?></b>
</p>
<?php } ?>
</div>

</div>

</div>

<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Revenue',
            data: <?php echo json_encode($revenues); ?>,
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 3
        }]
    },
    options: {
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>