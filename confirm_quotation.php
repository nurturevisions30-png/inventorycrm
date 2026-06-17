<?php
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include "config.php";

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$qid = (int)$_GET['id'];

// TOTAL RECALCULATE
$res = mysqli_query($conn, "SELECT SUM(total) as total FROM quotation_items WHERE quotation_id=$qid");
$row = mysqli_fetch_assoc($res);

$total = $row['total'] ?? 0;

// FINAL UPDATE
mysqli_query($conn, "UPDATE quotations SET 
total_amount='$total',
is_final=1
WHERE id=$qid");

// REDIRECT TO PREVIEW
header("Location: preview_quotation.php?id=".$qid);
exit();
?>