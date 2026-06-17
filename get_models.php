<?php

include "config.php";

$category = $_GET['category'];

$data = [];

$q = mysqli_query($conn,"
SELECT id, model
FROM products
WHERE product_category='$category'
ORDER BY model ASC
");

while($row = mysqli_fetch_assoc($q)){

    $data[] = $row;

}

echo json_encode($data);

?>