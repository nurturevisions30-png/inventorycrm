<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config.php";

/* DELETE PRODUCT */
if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"DELETE FROM products WHERE id=$id");

    echo "
    <script>
    alert('Product Deleted Successfully');
    window.location='manage_products.php';
    </script>
    ";

    exit();
}

/* FILTERS */
$category_filter = $_GET['category'] ?? '';
$model_filter = $_GET['model'] ?? '';
$search = $_GET['search'] ?? '';

/* PAGINATION */
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* MAIN QUERY */
$where = " WHERE 1 ";

/* CATEGORY FILTER */
if($category_filter != ''){
    $where .= " AND product_category='$category_filter'";
}

/* MODEL FILTER */
if($model_filter != ''){
    $where .= " AND model='$model_filter'";
}

/* SEARCH */
if($search != ''){
    $where .= " AND (
        model LIKE '%$search%' OR
        description LIKE '%$search%'
    )";
}

/* TOTAL PRODUCTS */
$totalQuery = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM products
$where
");

$totalRow = mysqli_fetch_assoc($totalQuery);

$total_products = $totalRow['total'];

$total_pages = ceil($total_products / $limit);

/* MAIN DATA */
$query = "
SELECT * FROM products
$where
ORDER BY product_category ASC, model ASC
LIMIT $offset, $limit
";

$result = mysqli_query($conn,$query);

/* CATEGORY LIST */
$cats = mysqli_query($conn,"
SELECT DISTINCT product_category
FROM products
ORDER BY product_category ASC
");

/* MODEL LIST */
$model_query = "
SELECT DISTINCT model
FROM products
WHERE 1
";

if($category_filter != ''){
    $model_query .= " AND product_category='$category_filter'";
}

$model_query .= " ORDER BY model ASC";

$models = mysqli_query($conn,$model_query);

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Products</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI';
}

body{
    background:#f4f7fb;
}

/* PAGE */
.page{
    padding:20px;
    width:100%;
}

/* TOP BAR */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    flex-wrap:wrap;
    gap:15px;
}

.title h1{
    font-size:28px;
    color:#111827;
}

.title p{
    color:#6b7280;
    margin-top:5px;
}

/* BUTTONS */
.actions{
    display:flex;
    gap:10px;
}

.btn{
    height:45px;
    padding:0 18px;
    border:none;
    border-radius:12px;
    color:white;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

.upload-btn{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
}

.add-btn{
    background:linear-gradient(135deg,#16a34a,#15803d);
}

/* FILTER BAR */
.filter-bar{
    background:white;
    padding:16px;
    border-radius:18px;
    display:flex;
    gap:12px;
    margin-bottom:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    flex-wrap:wrap;
    align-items:center;
}

.filter-bar input,
.filter-bar select{
    height:45px;
    padding:0 14px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    width:240px;
}

.filter-btn{
    height:45px;
    padding:0 18px;
    border:none;
    border-radius:12px;
    background:#111827;
    color:white;
    font-weight:600;
    cursor:pointer;
}

/* TABLE CARD */
.table-card{
    background:white;
    border-radius:20px;
    padding:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    overflow-x:auto;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    min-width:1000px;
}

th{
    background:#111827;
    color:white;
    padding:14px 10px;
    text-align:left;
    font-size:13px;
    white-space:nowrap;
}

td{
    padding:12px 10px;
    border-bottom:1px solid #f1f1f1;
    font-size:13px;
    vertical-align:middle;
}

/* COLUMN WIDTH */
th:nth-child(1),
td:nth-child(1){
    width:60px;
    text-align:center;
}

th:nth-child(2),
td:nth-child(2){
    width:80px;
    text-align:center;
}

th:nth-child(3),
td:nth-child(3){
    width:150px;
}

th:nth-child(4),
td:nth-child(4){
    width:180px;
}

th:nth-child(5),
td:nth-child(5){
    width:280px;
}

th:nth-child(6),
td:nth-child(6){
    width:90px;
    text-align:center;
}

th:nth-child(7),
td:nth-child(7){
    width:100px;
    text-align:center;
}

th:nth-child(8),
td:nth-child(8){
    width:200px;
}

th:nth-child(9),
td:nth-child(9){
    width:100px;
    text-align:center;
}

/* IMAGE */
.product-img{
    width:55px;
    height:55px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #eee;
}

/* CATEGORY */
.category{
    background:#eef2ff;
    color:#4338ca;
    padding:7px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    display:inline-block;
}

/* MODEL */
.model{
    font-weight:700;
    color:#111827;
}

/* DESC */
.desc{
    color:#6b7280;
    line-height:1.5;
}

/* STOCK */
.stock{
    font-weight:700;
    color:#16a34a;
}

/* PRICE */
.price{
    font-weight:700;
}

/* SLAB */
.slab{
    display:flex;
    flex-direction:column;
    gap:5px;
}

.slab span{
    background:#f3f4f6;
    padding:6px 8px;
    border-radius:8px;
    font-size:11px;
    font-weight:600;
}

/* ACTION */
.action-box{
    display:flex;
    justify-content:center;
    gap:8px;
}

.edit-btn,
.delete-btn{
    width:36px;
    height:36px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    text-decoration:none;
    transition:.3s;
}

.edit-btn{
    background:#f59e0b;
}

.delete-btn{
    background:#dc2626;
}

.edit-btn:hover,
.delete-btn:hover{
    transform:scale(1.08);
}

/* EMPTY */
.empty{
    text-align:center;
    padding:40px;
    color:#6b7280;
    font-size:18px;
}

/* PAGINATION */
.pagination{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-top:25px;
    flex-wrap:wrap;
}

.pagination a{
    width:40px;
    height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    background:white;
    color:#111827;
    text-decoration:none;
    font-weight:600;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.pagination a.active{
    background:#2563eb;
    color:white;
}

/* MOBILE */
@media(max-width:768px){

    .page{
        padding:10px;
    }

    .top-bar{
        flex-direction:column;
        align-items:flex-start;
    }

    .actions{
        width:100%;
    }

    .btn{
        flex:1;
        justify-content:center;
    }

    .filter-bar{
        flex-direction:column;
        align-items:stretch;
    }

    .filter-bar input,
    .filter-bar select,
    .filter-btn{
        width:100%;
    }

    table{
        min-width:1000px;
    }
}

</style>
</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="page">

<!-- TOP -->
<div class="top-bar">

    <div class="title">
        <h1>Manage Products</h1>
        <p>Inventory management, pricing slabs & stock overview</p>
    </div>

    <div class="actions">

        <a href="upload_products.php" class="btn upload-btn">
            <i class="fa-solid fa-upload"></i>
            Upload CSV
        </a>

        <a href="add_product.php" class="btn add-btn">
            <i class="fa-solid fa-plus"></i>
            Add Product
        </a>

    </div>

</div>

<!-- FILTER -->
<form method="GET">

<div class="filter-bar">

    <!-- SEARCH -->
    <input
    type="text"
    name="search"
    placeholder="Search model..."
    value="<?php echo $search; ?>">

    <!-- CATEGORY -->
    <select name="category" onchange="this.form.submit()">

        <option value="">All Categories</option>

        <?php while($cat = mysqli_fetch_assoc($cats)){ ?>

        <option
        value="<?php echo $cat['product_category']; ?>"

        <?php
        if($category_filter == $cat['product_category']){
            echo "selected";
        }
        ?>

        >

        <?php echo $cat['product_category']; ?>

        </option>

        <?php } ?>

    </select>

    <!-- MODEL -->
    <select name="model">

        <option value="">All Models</option>

        <?php while($m = mysqli_fetch_assoc($models)){ ?>

        <option
        value="<?php echo $m['model']; ?>"

        <?php
        if($model_filter == $m['model']){
            echo "selected";
        }
        ?>

        >

        <?php echo $m['model']; ?>

        </option>

        <?php } ?>

    </select>

    <button class="filter-btn">
        <i class="fa-solid fa-filter"></i>
        Apply Filter
    </button>

</div>

</form>

<!-- TABLE -->
<div class="table-card">

<table>

<tr>

<th>ID</th>
<th>Image</th>
<th>Category</th>
<th>Model</th>
<th>Description</th>
<th>Stock</th>
<th>MSP</th>
<th>Pricing Slabs</th>
<th>Action</th>

</tr>

<?php if(mysqli_num_rows($result) > 0){ ?>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

<td>#<?php echo $row['id']; ?></td>

<!-- IMAGE -->
<td>

<?php if(!empty($row['image'])){ ?>

<img
src="upload/<?php echo $row['image']; ?>"
class="product-img">

<?php } else { ?>

<img
src="https://via.placeholder.com/65"
class="product-img">

<?php } ?>

</td>

<!-- CATEGORY -->
<td>

<span class="category">

<?php echo $row['product_category']; ?>

</span>

</td>

<!-- MODEL -->
<td class="model">

<?php echo $row['model']; ?>

</td>

<!-- DESC -->
<td class="desc">

<?php echo substr($row['description'],0,90); ?>...

</td>

<!-- STOCK -->
<td class="stock">

<?php echo $row['ready_qty']; ?> pcs

</td>

<!-- MSP -->
<td class="price">

₹<?php echo number_format($row['msp']); ?>

</td>

<!-- SLABS -->
<td>

<div class="slab">

<span>
300+ Qty → ₹<?php echo $row['slab1']; ?>
</span>

<span>
500+ Qty → ₹<?php echo $row['slab2']; ?>
</span>

<span>
900+ Qty → ₹<?php echo $row['slab3']; ?>
</span>

</div>

</td>

<!-- ACTION -->
<td>

<div class="action-box">

<a
href="edit_product.php?id=<?php echo $row['id']; ?>"
class="edit-btn">

<i class="fa-solid fa-pen"></i>

</a>

<a
href="manage_products.php?delete=<?php echo $row['id']; ?>"
class="delete-btn"
onclick="return confirm('Delete this product?')">

<i class="fa-solid fa-trash"></i>

</a>

</div>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="9" class="empty">

No Products Found

</td>

</tr>

<?php } ?>

</table>

</div>

<!-- PAGINATION -->
<div class="pagination">

<?php for($i=1; $i <= $total_pages; $i++){ ?>

<a
href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&model=<?php echo $model_filter; ?>&search=<?php echo $search; ?>"
class="<?php if($page == $i){ echo 'active'; } ?>">

<?php echo $i; ?>

</a>

<?php } ?>

</div>

</div>

</body>
</html>