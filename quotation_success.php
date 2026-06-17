<?php
session_start();

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    die("No quotation found!");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Quotation Created</title>

<style>
/* BOX */
.box {
    background:white;
    padding:80px;
    border-radius:12px;
    text-align:center;
}

/* BUTTONS */
.btn {
    display:block;
    width:100%;
    margin:10px 0;
    padding:12px;
    border:none;
    border-radius:8px;
    color:white;
    font-size:16px;
    cursor:pointer;
}

.preview { background:#16a34a; }
.edit { background:#f59e0b; }
.home { background:#2563eb; }

h2 {
    margin-bottom:20px;
}
</style>
</head>

<body>
<?php
include "sidebaar.php";
?>
<div class="box">

<h2> Quotation Created Successfully</h2>

<a href="preview_quotation.php?id=<?php echo $_SESSION['last_id']; ?>">
    Preview Quotation
</a>

<a href="proposal.php">
    <button class="btn edit"> Edit Quotation</button>
</a>

<a href="dashboard.php">
    <button class="btn home"> Back to Dashboard</button>
</a>

</div>

</body>
</html>