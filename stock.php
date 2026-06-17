<?php
session_start();
include "config.php";

// ============================
// STOCK SUMMARY QUERIES
// ============================

// Total Products
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM products"))['total'];

// In Stock
$inStock = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM products 
WHERE ready_qty > min_qty"))['total'];

// Low Stock
$lowStock = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM products 
WHERE ready_qty <= min_qty 
AND ready_qty > 0"))['total'];

// Out Of Stock
$outStock = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM products 
WHERE ready_qty <= 0"))['total'];


// ============================
// PRODUCT LIST QUERY
// ============================

$productQuery = mysqli_query($conn,
"SELECT * FROM products ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Stock Management</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
    background:#f5f6fa;
    font-family:Arial;
}

.card-box{
    border-radius:15px;
    color:white;
    padding:20px;
}

.table img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
}

.badge{
    font-size:13px;
    padding:8px 12px;
}

.stock-table{
    background:white;
    border-radius:15px;
    padding:20px;
}

.page-title{
    font-weight:bold;
    margin-bottom:20px;
}

</style>

</head>
<body>
<?php include "sidebaar.php"; ?>
<div class="container-fluid p-4">

    <!-- PAGE TITLE -->
    <h2 class="page-title">
        <i class="bi bi-box-seam"></i>
        Stock Management
    </h2>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">

        <!-- TOTAL PRODUCTS -->
        <div class="col-md-3 mb-3">
            <div class="card-box bg-primary">
                <h5>Total Products</h5>
                <h2><?= $totalProducts ?></h2>
            </div>
        </div>

        <!-- IN STOCK -->
        <div class="col-md-3 mb-3">
            <div class="card-box bg-success">
                <h5>In Stock</h5>
                <h2><?= $inStock ?></h2>
            </div>
        </div>

        <!-- LOW STOCK -->
        <div class="col-md-3 mb-3">
            <div class="card-box bg-warning">
                <h5>Low Stock</h5>
                <h2><?= $lowStock ?></h2>
            </div>
        </div>

        <!-- OUT OF STOCK -->
        <div class="col-md-3 mb-3">
            <div class="card-box bg-danger">
                <h5>Out Of Stock</h5>
                <h2><?= $outStock ?></h2>
            </div>
        </div>

    </div>

    <!-- PRODUCT TABLE -->
    <div class="stock-table">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h4>
                Product Stock List
            </h4>

            <a href="add-product.php" class="btn btn-dark">
                <i class="bi bi-plus-circle"></i>
                Add Product
            </a>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Model</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Min Qty</th>
                        <th>Status</th>
                        <th>Alert</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                <?php
                while($row = mysqli_fetch_assoc($productQuery)){
                ?>

                    <tr>

                        <td>
                            <?= $row['id'] ?>
                        </td>

                        <!-- IMAGE -->
                        <td>

                            <?php
                            if(!empty($row['image'])){
                            ?>

                            <img src="uploads/<?= $row['image'] ?>">

                            <?php
                            } else {
                                echo "No Image";
                            }
                            ?>

                        </td>

                        <!-- MODEL -->
                        <td>
                            <?= $row['model'] ?>
                        </td>

                        <!-- CATEGORY -->
                        <td>
                            <?= $row['product_category'] ?>
                        </td>

                        <!-- STOCK -->
                        <td>

                            <strong>
                                <?= $row['ready_qty'] ?>
                            </strong>

                        </td>

                        <!-- MIN QTY -->
                        <td>
                            <?= $row['min_qty'] ?>
                        </td>

                        <!-- STATUS -->
                        <td>

                            <?php
                            if($row['status'] == 'active'){
                                echo "<span class='badge bg-success'>Active</span>";
                            }else{
                                echo "<span class='badge bg-secondary'>Inactive</span>";
                            }
                            ?>

                        </td>

                        <!-- ALERT -->
                        <td>

                            <?php

                            if($row['ready_qty'] <= 0){

                                echo "<span class='badge bg-danger'>
                                Out Of Stock
                                </span>";

                            }

                            elseif($row['ready_qty'] <= $row['min_qty']){

                                echo "<span class='badge bg-warning text-dark'>
                                Low Stock
                                </span>";

                            }

                            else{

                                echo "<span class='badge bg-success'>
                                In Stock
                                </span>";

                            }

                            ?>

                        </td>

                        <!-- ACTION -->
                        <td>

                            <a href="edit-product.php?id=<?= $row['id'] ?>"
                            class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <a href="add-stock.php?id=<?= $row['id'] ?>"
                            class="btn btn-sm btn-success">
                                + Stock
                            </a>

                            <a href="remove-stock.php?id=<?= $row['id'] ?>"
                            class="btn btn-sm btn-danger">
                                - Stock
                            </a>

                        </td>

                    </tr>

                <?php
                }
                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>