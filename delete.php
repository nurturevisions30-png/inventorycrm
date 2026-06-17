<?php
include "config.php";

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM quotations WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        header("Location: quotations.php?msg=deleted");
    } else {
        echo "Delete failed";
    }
}
?>