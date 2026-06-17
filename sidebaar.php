<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

body{
    display:flex;
    background:#f5f7fb;
}

/* SIDEBAR */
.sidebar{
    width:240px;
    background:white;
    min-height:100vh;
    border-right:1px solid #e5e7eb;
    padding:20px;
    position:sticky;
    top:0;
}

/* LOGO */
.logo{
    text-align:center;
    margin-bottom:20px;
}

.logo img{
    width:170px;
    height:auto;
}

/* MENU */
.menu{
    list-style:none;
    margin-top:20px;
}

.menu li{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 15px;
    margin-bottom:8px;
    border-radius:12px;
    cursor:pointer;
    color:#1e293b;
    transition:0.3s;
    font-size:15px;
    font-weight:500;
}

.menu li i{
    width:20px;
    text-align:center;
    font-size:16px;
}

/* HOVER */
.menu li:hover{
    background:#2563eb;
    color:white;
    transform:translateX(3px);
}

/* ACTIVE TAB */
.menu li.active{
    background:linear-gradient(135deg, #2563eb, #1d4ed8);
    color:white;
    box-shadow:0 4px 12px rgba(37,99,235,0.25);
    transform:translateX(3px);
}

/* MAIN */
.main{
    flex:1;
    padding:25px 35px;
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    background:white;
    padding:18px 25px;
    border-radius:16px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
}

/* TITLE */
.title h1{
    font-size:24px;
    color:#0f172a;
}

.title p{
    color:#64748b;
    margin-top:5px;
    font-size:14px;
}

/* PROFILE */
.profile-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.profile-text{
    text-align:right;
}

.profile-text span{
    font-size:13px;
    color:#64748b;
}

.profile-text h4{
    font-size:15px;
    color:#0f172a;
}

.avatar{
    width:42px;
    height:42px;
    background:#2563eb;
    color:white;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:bold;
    font-size:16px;
    box-shadow:0 3px 8px rgba(37,99,235,0.3);
}

/* LOGOUT */
.logout-btn{
    margin-top:20px;
    display:block;
    background:#303030;
    color:white;
    text-align:center;
    padding:12px;
    border-radius:10px;
    text-decoration:none;
    transition:0.3s;
    font-weight:500;
}

.logout-btn:hover{
    background:#dc2626;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <!-- LOGO -->
    <div class="logo">
        <img src="upload/logo.png">
    </div>

    <!-- MENU -->
    <ul class="menu">

        <!-- DASHBOARD -->
        <li class="<?= ($current_page == 'dashboard.php' || $current_page == 'staff_dashboard.php') ? 'active' : ''; ?>"
        onclick="location.href='<?php 
        echo ($_SESSION['role'] == 'admin') 
        ? 'dashboard.php' 
        : 'staff_dashboard.php'; 
        ?>'">

            <i class="fa-solid fa-table-columns"></i>
            Dashboard
        </li>

        <!-- CREATE QUOTATION -->
        <li class="<?= ($current_page == 'create_quotation.php') ? 'active' : ''; ?>"
        onclick="location.href='create_quotation.php'">

            <i class="fa-solid fa-file-circle-plus"></i>
            New Quotation
        </li>

        <!-- QUOTATIONS -->
        <li class="<?= ($current_page == 'quotations.php') ? 'active' : ''; ?>"
        onclick="location.href='quotations.php'">

            <i class="fa-solid fa-file-lines"></i>
            Quotations
        </li>

        <!-- CUSTOMERS -->
        <li class="<?= ($current_page == 'customers.php') ? 'active' : ''; ?>"
        onclick="location.href='customers.php'">

            <i class="fa-solid fa-users"></i>
            Customers
        </li>

        <!-- ADMIN ONLY -->
        <?php if($_SESSION['role'] == 'admin'){ ?>

        <!-- REPORT -->
        <li class="<?= ($current_page == 'report.php') ? 'active' : ''; ?>"
        onclick="location.href='report.php'">

            <i class="fa-solid fa-chart-line"></i>
            Reports
        </li>

        <!-- STAFF -->
        <li class="<?= ($current_page == 'staff_list.php') ? 'active' : ''; ?>"
        onclick="location.href='staff_list.php'">

            <i class="fa-solid fa-user-tie"></i>
            Staff Management
        </li>

        <!-- STOCK -->
        <li class="<?= ($current_page == 'stock.php') ? 'active' : ''; ?>"
        onclick="location.href='stock.php'">

            <i class="fa-solid fa-file-circle-plus"></i>
            Stock Management
        </li>

        <!-- PRODUCTS -->
        <li class="<?= ($current_page == 'manage_products.php') ? 'active' : ''; ?>"
        onclick="location.href='manage_products.php'">

            <i class="fa-solid fa-boxes-stacked"></i>
            Product List
        </li>

        <?php } ?>

        <!-- SETTINGS -->
        <li class="<?= ($current_page == 'settings.php') ? 'active' : ''; ?>"
        onclick="location.href='settings.php'">

            <i class="fa-solid fa-gear"></i>
            Settings
        </li>

    </ul>

    <!-- LOGOUT -->
    <a href="logout.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </a>

</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">

        <div class="title">
            <h1>Inventory Management Dashboard</h1>
            <p>Overview of your inventory, quotations & staff activity</p>
        </div>

        <!-- PROFILE -->
        <div class="profile-box">

            <div class="profile-text">
                <span>Logged in as</span>

                <h4>
                    <?php echo $_SESSION['user_name']; ?>
                    (<?php echo ucfirst($_SESSION['role']); ?>)
                </h4>
            </div>

            <a href="settings.php" style="text-decoration:none;">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
                </div>
            </a>

        </div>

    </div>