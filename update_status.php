<?php
include "config.php";

$id = $_GET['id'];
$status = $_GET['status'];

if($status == 'Approved'){
    mysqli_query($conn, "UPDATE quotations 
    SET status='Approved', is_final=1 
    WHERE id=$id");
}else{
    mysqli_query($conn, "UPDATE quotations 
    SET status='$status' 
    WHERE id=$id");
}

header("Location: quotations.php");
?>