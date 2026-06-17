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
// REMOVE STOCK LOGIC
// ==========================

if(isset($_POST['remove_stock'])){

    $qty  = intval($_POST['qty']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    // Current Stock
    $currentQty = $product['ready_qty'];

    // ==========================
    // VALIDATION
    // ==========================

    if($qty > $currentQty){

        $_SESSION['error'] = "Insufficient Stock";

    }else{

        // ==========================
        // NEW STOCK
        // ==========================

        $newQty = $currentQty - $qty;

        // ==========================
        // UPDATE PRODUCTS TABLE
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
            'OUT',
            '$currentQty',
            '$qty',
            '$newQty',
            '$note'
        )");

        // ==========================
        // LOW STOCK NOTIFICATION
        // ==========================

        if($newQty <= $product['min_qty']){

            mysqli_query($conn,
            "INSERT INTO notifications(
                product_id,
                message
            )
            VALUES(
                '$product_id',
                'Product stock is running low'
            )");

        }

        // ==========================
        // OUT OF STOCK NOTIFICATION
        // ==========================

        if($newQty <= 0){

            mysqli_query($conn,
            "INSERT INTO notifications(
                product_id,
                message
            )
            VALUES(
                '$product_id',
                'Product is out of stock'
            )");

        }

        // ==========================
        // SUCCESS MESSAGE
        // ==========================

        $_SESSION['success'] = "Stock Removed Successfully";

        header("Location: stock.php");
        exit();

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Remove Stock</title>

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

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="stock-card">

                <h2 class="mb-4 text-danger">
                    Remove Stock
                </h2>

                <!-- ERROR MESSAGE -->

                <?php
                if(isset($_SESSION['error'])){
                ?>

                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                </div>

                <?php
                unset($_SESSION['error']);
                }
                ?>

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
                            Remove Quantity
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
                        placeholder="Enter stock remove reason..."
                        ></textarea>

                    </div>

                    <!-- BUTTONS -->

                    <div class="d-flex gap-2">

                        <button type="submit"
                        name="remove_stock"
                        class="btn btn-danger">

                            Remove Stock

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