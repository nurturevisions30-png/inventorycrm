<?php
include "config.php";

$id = $_GET['id'];
$status = $_GET['status'];

mysqli_query($conn, "UPDATE users SET status='$status' WHERE id=$id");

header("Location: staff_list.php");
?>