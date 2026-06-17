<?php
include "config.php";

$id = (int)$_GET['id'];
$qid = (int)$_GET['qid'];

mysqli_query($conn, "DELETE FROM quotation_items WHERE id=$id");

header("Location: edit_quotation.php?id=".$qid);
exit();
?>