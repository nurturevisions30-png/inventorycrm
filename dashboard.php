<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
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
.menu a i {
    margin-right: 8px;
}
/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.card h4 {
    color: gray;
    font-size: 14px;
}

.card h2 {
    margin: 10px 0;
}

.blue { color: #2563eb; }
.green { color: #16a34a; }
.red { color: #dc2626; }

/* Actions */
.actions {
    margin-top: 25px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    margin-right: 10px;
    color: white;
    cursor: pointer;
}

.green-btn { background: #16a34a; }
.blue-btn { background: #2563eb; }
.gray-btn { background: #4b5563; }

/* Table */
.table-box {
    margin-top: 25px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px;
    text-align: left;
}

tr {
    border-bottom: 1px solid #eee;
}

.status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.pending { background: #fef3c7; color: #92400e; }
.approved { background: #d1fae5; color: #065f46; }
.rejected { background: #facc15;
color: #000; }

.view {
    color: #2563eb;
    cursor: pointer;
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
    <!-- Cards -->
    <div class="cards">
        <div class="card">
            <h4>Total Products</h4>
            <h2>1,234</h2>
            <p class="blue">+12.5% from last month</p>
        </div>

        <div class="card">
            <h4>Active Quotations</h4>
            <h2>45</h2>
            <p class="green">+8.2% from last month</p>
        </div>

        <div class="card">
            <h4>Low Stock Items</h4>
            <h2>23</h2>
            <p class="red">-5.1% from last month</p>
        </div>

        <div class="card">
            <h4>Monthly Sales</h4>
            <h2>₹2.5L</h2>
            <p class="blue">+15.3% from last month</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <h3>Quick Actions</h3><br>
        <a href="create_quotation.php"><button class="btn green-btn">Create New Quotation</button></a>
        <a  href="add_product.php"><button class="btn blue-btn">Add Product</button></a>
        <a href="manage_products.php"><button class="btn gray-btn">View Products List</button></a>
    </div>

    <!-- Table -->
     <?php
include "config.php";

$q = mysqli_query($conn, "SELECT * FROM quotations ORDER BY id DESC LIMIT 5");
?>
    <div class="table-box">
        <h3>Today's Quotations</h3><br>

        <table>
<?php while($row = mysqli_fetch_assoc($q)) { ?>
<tr>
    <td><?php echo $row['quotation_no']; ?></td>
    <td><?php echo $row['company_name']; ?></td>
    <td><?php echo date("d-m-Y", strtotime($row['date'])); ?></td>

    <td>
        <span class="status 
        <?php 
            if($row['status']=='Pending') echo 'pending';
            elseif($row['status']=='Approved') echo 'approved';
            else echo 'rejected';
        ?>">
        <?php echo $row['status']; ?>
        </span>
    </td>
    <td>
<?php echo $row['created_by']; ?>
(
<?php echo strtolower($row['created_role']); ?>
)
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