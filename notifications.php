<?php
session_start();
include "config.php";

// MARK ALL AS READ

mysqli_query($conn,
"UPDATE notifications 
SET status='read'
WHERE status='unread'");

// FETCH NOTIFICATIONS

$query = mysqli_query($conn,
"SELECT notifications.*, products.model
FROM notifications
LEFT JOIN products 
ON products.id = notifications.product_id
ORDER BY notifications.id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f6fa;
    font-family:Arial;
}

.notification-box{
    background:white;
    padding:20px;
    border-radius:15px;
    margin-top:30px;
}

.notification-item{
    padding:15px;
    border-bottom:1px solid #eee;
}

</style>

</head>
<body>

<div class="container">

    <div class="notification-box">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2>
                Notifications
            </h2>

            <a href="stock.php"
            class="btn btn-dark">

                Back

            </a>

        </div>

        <?php

        if(mysqli_num_rows($query) > 0){

            while($row = mysqli_fetch_assoc($query)){

        ?>

        <div class="notification-item">

            <h5 class="mb-1">
                <?= $row['model'] ?>
            </h5>

            <p class="mb-1">
                <?= $row['message'] ?>
            </p>

            <small class="text-muted">

                <?= date("d M Y h:i A",
                strtotime($row['created_at'])) ?>

            </small>

        </div>

        <?php
            }

        } else {

            echo "<div class='alert alert-info'>
            No notifications found
            </div>";

        }

        ?>

    </div>

</div>

</body>
</html>