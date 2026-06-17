<?php
session_start();
?>
<?php
include "config.php";

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));

if(isset($_POST['save'])){

    $name = $_POST['company_name'];
    $address = $_POST['address'];
    $gst = $_POST['gst'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    mysqli_query($conn, "UPDATE settings SET
        company_name='$name',
        address='$address',
        gst='$gst',
        phone='$phone',
        email='$email'
        WHERE id=1");

    echo "<script>alert('Settings Updated Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Settings</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>


/* HEADER */
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.header h1 {
    font-size:22px;
}

.back-btn {
    background:#6b7280;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

/* CARD */
.card {
    background:white;
    border-radius:12px;
    padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    margin-bottom:20px;
}

/* FORM GRID */
.grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.full {
    grid-column: span 2;
}

label {
    font-size:13px;
    color:#6b7280;
}

input, textarea {
    width:100%;
    padding:10px;
    border:1px solid #e5e7eb;
    border-radius:8px;
    margin-top:5px;
}

textarea {
    height:80px;
}

/* BUTTON */
.save-btn {
    background:#2563eb;
    color:white;
    padding:12px 25px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-top:15px;
}

.save-btn:hover {
    background:#1d4ed8;
}
</style>
</head>

<body>
<?php
include "sidebaar.php";
?>

<form method="POST">

<!-- COMPANY DETAILS -->
<div class="card">
    <h3>Company Details</h3><br>

    <div class="grid">
        <div>
            <label>Company Name</label>
            <input type="text" name="company_name" value="<?php echo $data['company_name'] ?? ''; ?>">
        </div>

        <div>
            <label>GST Number</label>
            <input type="text" name="gst" value="<?php echo $data['gst'] ?? ''; ?>">
        </div>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo $data['phone'] ?? ''; ?>">
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $data['email'] ?? ''; ?>">
        </div>

        <div class="full">
            <label>Address</label>
            <textarea name="address"><?php echo $data['address'] ?? ''; ?></textarea>
        </div>
    </div>
</div>

<!-- QUOTATION SETTINGS -->
<div class="card">
    <h3>Quotation Settings</h3><br>

    <div class="grid">
        <div>
            <label>Quotation Prefix</label>
            <input type="text" placeholder="ASA/Q/">
        </div>

        <div>
            <label>Default GST (%)</label>
            <input type="text" placeholder="18">
        </div>

        <div class="full">
            <label>Default Terms & Conditions</label>
            <textarea placeholder="Write terms here..."></textarea>
        </div>
    </div>
</div>

<!-- SAVE -->
<button class="save-btn" name="save">Save Settings</button>

</form>

</div>

</body>
</html>