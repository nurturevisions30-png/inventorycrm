<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>
<?php
include "config.php";
?>

<?php


// FETCH CUSTOMERS
if($_SESSION['role'] == 'staff'){

    $uid = $_SESSION['user_id'];

    $query = "
    SELECT client_name, client_company, client_email,
    COUNT(id) as total_quotes,
    SUM(total_amount) as total_value
    FROM quotations
    WHERE created_by='$uid'
    GROUP BY client_email
    ORDER BY total_quotes DESC
    ";

} else {

    $query = "
    SELECT client_name, client_company, client_email,
    COUNT(id) as total_quotes,
    SUM(total_amount) as total_value
    FROM quotations
    GROUP BY client_email
    ORDER BY total_quotes DESC
    ";
}

$customers = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Customers</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body {
    font-family:'Segoe UI';
    background:#f1f5f9;
}

.container {
    padding:20px;
}

.title {
    font-size:22px;
    margin-bottom:20px;
}

/* TABLE */
table {
    width:100%;
    border-collapse: collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
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

tr:hover {
    background:#f9fafb;
}

/* BUTTON */
.view-btn {
    background:#10b981;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
}

</style>
</head>

<body>
<?php
include "sidebaar.php";
?>
<div class="container">

<div class="title">👥 Customers</div>

<table>

<tr>
<th>Name</th>
<th>Company</th>
<th>Email</th>
<th>Total Quotations</th>
<th>Total Value</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($customers)){ ?>

<tr>

<td><?php echo $row['client_name']; ?></td>
<td><?php echo $row['client_company']; ?></td>
<td><?php echo $row['client_email']; ?></td>
<td><?php echo $row['total_quotes']; ?></td>
<td>₹<?php echo $row['total_value'] ?? 0; ?></td>

<td>
<a class="view-btn" href="customer_detail.php?email=<?php echo $row['client_email']; ?>">
View
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>