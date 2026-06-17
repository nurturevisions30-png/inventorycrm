<?php
session_start();
include "config.php";

?>
<?php

if($_SESSION['role'] == 'staff'){

$uid = $_SESSION['user_id'];

$query = "SELECT * FROM quotations 
WHERE created_user_id='$uid'
ORDER BY id DESC";

} else {

    $query = "SELECT * FROM quotations 
              ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>All Quotations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body {
    font-family:'Segoe UI';
    background:#f5f7fb;
}

.container {
    padding:20px;
}

table {
    width:100%;
    border-collapse: collapse;
    background:white;
}

th, td {
    padding:12px;
    border-bottom:1px solid #eee;
}

.status {
    padding:5px 10px;
    border-radius:20px;
}

.pending { background: #facc15;
color: #000; }
.approved { background:#d1fae5; }
.rejected { background:#fee2e2; }

/* DROPDOWN CONTAINER */
.dropdown {
    position: relative;
    display: inline-block;
}

/* THREE DOTS */
.dots {
    font-size: 20px;
    cursor: pointer;
    padding: 5px 10px;
}

/* MENU */
.menuu {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    min-width: 150px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    overflow: hidden;
    z-index: 999;
}

/* LINKS */
.menuu a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    transition: 0.2s;
}

.menuu a:hover {
    background: #f3f4f6;
}

/* DELETE COLOR */
.menuu .delete {
    color: red;
}

/* SHOW ON HOVER */
.dropdown:hover .menuu {
    display: block;
}
.menuu a i {
    margin-right: 8px;
    transform: scale(1.1);
}
.modal {
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}

.modal-box {
    background:white;
    padding:20px;
    border-radius:10px;
    width:300px;
    text-align:center;
}

.modal-actions {
    margin-top:15px;
    display:flex;
    justify-content:space-between;
}

.modal-actions button {
    background:#ccc;
    border:none;
    padding:8px 15px;
    border-radius:5px;
}

.modal-actions a {
    background:red;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
}
</style>

</head>

<body>

<?php
include "sidebaar.php";
?>
<div id="deleteModal" class="modal">
  <div class="modal-box">
    <h3>Delete Confirmation</h3>
    <p>Are you sure you want to delete this quotation?</p>

    <div class="modal-actions">
      <button onclick="closeModal()">Cancel</button>
      <a id="confirmDeleteBtn" href="#">Delete</a>
    </div>
  </div>
</div>
<div class="container">

<h2>All Quotations</h2>

<table>

<tr>
<th>ID</th>
<th>Quotation No</th>
<th>Client</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['quotation_no']; ?></td>
<td><?php echo $row['client_name']; ?></td>
<td><?php echo date("d-m-Y", strtotime($row['date'])); ?></td>

<td>
<span class="status 
<?php 
if($row['status']=='Draft') echo 'pending';
elseif($row['status']=='Approved') echo 'approved';
else echo 'rejected';
?>">
<?php echo $row['status']; ?>
</span>
</td>
<td>

<div class="dropdown">

    <!-- THREE DOTS ICON -->
    <div class="dots">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </div>

    <!-- MENU -->
    <div class="menuu">

        <a href="preview_quotation.php?id=<?php echo $row['id']; ?>">
            <i class="fa-solid fa-eye"></i> View
        </a>

        <a href="edit_quotation.php?id=<?php echo $row['id']; ?>">
            <i class="fa-solid fa-pen"></i> Edit
        </a>

        <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Approved">
            <i class="fa-solid fa-check"></i> Approve
        </a>

        <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Rejected">
            <i class="fa-solid fa-xmark"></i> Reject
        </a>

<a href="#" onclick="openDeleteModal(<?php echo $row['id']; ?>)" class="delete">
    <i class="fa-solid fa-trash"></i> Delete
</a>

    </div>

</div>

</td>

</tr>

<?php } ?>

</table>

</div>
<script>
function openDeleteModal(id){
    document.getElementById("deleteModal").style.display = "flex";
    document.getElementById("confirmDeleteBtn").href = "delete.php?id=" + id;
}

function closeModal(){
    document.getElementById("deleteModal").style.display = "none";
}
</script>
</body>
</html>