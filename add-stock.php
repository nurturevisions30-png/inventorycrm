<?php
session_start();
include "config.php";

// ==========================
// GET PRODUCT ID
// ==========================

if(!isset($_GET['id'])){
    header("Location: stock.php");
    exit();
}

$product_id = $_GET['id'];

// ==========================
// FETCH PRODUCT
// ==========================

$productQuery = mysqli_query($conn,
"SELECT * FROM products WHERE id='$product_id'");

$product = mysqli_fetch_assoc($productQuery);

if(!$product){
    die("Product not found");
}

// ==========================
// ADD STOCK LOGIC
// ==========================

if(isset($_POST['add_stock'])){

    $qty  = intval($_POST['qty']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // Current Stock
    $currentQty = $product['ready_qty'];

    // New Stock
    $newQty = $currentQty + $qty;

    // ==========================
    // UPDATE PRODUCT STOCK
    // ==========================

    mysqli_query($conn,
    "UPDATE products 
    SET ready_qty='$newQty'
    WHERE id='$product_id'");

    // ==========================
    // INSERT STOCK HISTORY
    // ==========================

    mysqli_query($conn,
    "INSERT INTO stock_history(
        product_id,
        type,
        old_qty,
        qty_changed,
        new_qty,
        note
    )
    VALUES(
        '$product_id',
        'IN',
        '$currentQty',
        '$qty',
        '$newQty',
        '$note'
    )");

    // ==========================
    // SUCCESS MESSAGE
    // ==========================

    $_SESSION['success'] = "Stock Added Successfully";

    header("Location: stock.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Stock</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f6fa;
    font-family:Arial;
}

.stock-card{
    background:white;
    padding:30px;
    border-radius:15px;
    margin-top:40px;
}

.product-img{
    width:120px;
    height:120px;
    object-fit:cover;
    border-radius:15px;
}

</style>

</head>
<body>
<?php include "sidebaar.php"; ?>
<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="stock-card">

                <h2 class="mb-4">
                    Add Stock
                </h2>

                <!-- PRODUCT INFO -->

                <div class="row mb-4 align-items-center">

                    <div class="col-md-3">

                        <?php
                        if(!empty($product['image'])){
                        ?>

                        <img src="uploads/<?= $product['image'] ?>"
                        class="product-img">

                        <?php
                        } else {
                            echo "No Image";
                        }
                        ?>

                    </div>

                    <div class="col-md-9">

                        <h4>
                            <?= $product['model'] ?>
                        </h4>

                        <p class="mb-1">
                            Category :
                            <strong>
                                <?= $product['product_category'] ?>
                            </strong>
                        </p>

                        <p class="mb-1">
                            Current Stock :
                            <strong>
                                <?= $product['ready_qty'] ?>
                            </strong>
                        </p>

                        <p class="mb-0">
                            Min Qty :
                            <strong>
                                <?= $product['min_qty'] ?>
                            </strong>
                        </p>

                    </div>

                </div>

                <!-- FORM -->

                <form method="POST">

                    <!-- QTY -->

                    <div class="mb-3">

                        <label class="form-label">
                            Add Quantity
                        </label>

                        <input type="number"
                        name="qty"
                        class="form-control"
                        required
                        min="1">

                    </div>

                    <!-- NOTE -->

                    <div class="mb-3">

                        <label class="form-label">
                            Note
                        </label>

                        <textarea
                        name="note"
                        class="form-control"
                        rows="4"
                        placeholder="Enter stock note..."
                        ></textarea>

                    </div>

                    <!-- BUTTONS -->

                    <div class="d-flex gap-2">

                        <button type="submit"
                        name="add_stock"
                        class="btn btn-success">

                            Add Stock

                        </button>

                        <a href="stock.php"
                        class="btn btn-secondary">

                            Back

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>