<?php

include "config.php";

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"
SELECT *
FROM products
WHERE id=$id
");

$row = mysqli_fetch_assoc($q);

echo json_encode($row);

?>