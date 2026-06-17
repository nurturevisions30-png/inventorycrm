<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config.php";

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    die("No products selected!");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $client_name     = mysqli_real_escape_string($conn,$_POST['client_name']);
    $client_company  = mysqli_real_escape_string($conn,$_POST['client_company']);
    $client_email    = mysqli_real_escape_string($conn,$_POST['client_email']);
    $client_phone    = mysqli_real_escape_string($conn,$_POST['client_phone']);
    $client_address  = mysqli_real_escape_string($conn,$_POST['client_address']);
    $client_city     = mysqli_real_escape_string($conn,$_POST['client_city']);
    $client_state    = mysqli_real_escape_string($conn,$_POST['client_state']);
    $client_pincode  = mysqli_real_escape_string($conn,$_POST['client_pincode']);

    $packing         = mysqli_real_escape_string($conn,$_POST['packing']);
    $freight         = mysqli_real_escape_string($conn,$_POST['freight']);
    $delivery        = mysqli_real_escape_string($conn,$_POST['delivery']);
    $payment         = mysqli_real_escape_string($conn,$_POST['payment']);
    $validity        = mysqli_real_escape_string($conn,$_POST['validity']);

    $sign_name       = mysqli_real_escape_string($conn,$_POST['sign_name']);
    $designation     = mysqli_real_escape_string($conn,$_POST['designation']);
    $sign_phone      = mysqli_real_escape_string($conn,$_POST['sign_phone']);

    $cc_email        = mysqli_real_escape_string($conn,$_POST['cc_email']);
    $email_subject   = mysqli_real_escape_string($conn,$_POST['email_subject']);
    $email_message   = mysqli_real_escape_string($conn,$_POST['email_message']);

    $total_amount = 0;

    foreach($_SESSION['cart'] as $item){
        $total_amount += $item['total'];
    }

    /* LAST QUOTATION ID */

$getLast = mysqli_query($conn,"
SELECT id
FROM quotations
ORDER BY id DESC
LIMIT 1
");

$last = mysqli_fetch_assoc($getLast);

$next_no = ($last) ? $last['id'] + 1 : 1;

/* FINANCIAL YEAR */

$financial_year = "2026-27";

/* QUOTATION FORMAT */

$quotation_no =
"ASA/Q/" .
str_pad($next_no, 3, "0", STR_PAD_LEFT) .
"/" .
$financial_year;

    $company_name = "ASA Associates";

    $date = date("Y-m-d");

    $insert = mysqli_query($conn,"INSERT INTO quotations
    (
        quotation_no,
        company_name,
        client_name,
        client_company,
        client_email,
        client_phone,
        client_address,
        client_city,
        client_state,
        client_pincode,
        date,
        status,
        is_final,
        total_amount,
        packing,
        freight,
        delivery,
        payment,
        validity,
        sign_name,
        designation,
        sign_phone,
        cc_email,
        email_subject,
        email_message,
        created_user_id,
        created_by,
        created_role
    )

    VALUES
    (
        '$quotation_no',
        '$company_name',
        '$client_name',
        '$client_company',
        '$client_email',
        '$client_phone',
        '$client_address',
        '$client_city',
        '$client_state',
        '$client_pincode',
        '$date',
        'Draft',
        0,
        '$total_amount',
        '$packing',
        '$freight',
        '$delivery',
        '$payment',
        '$validity',
        '$sign_name',
        '$designation',
        '$sign_phone',
        '$cc_email',
        '$email_subject',
        '$email_message',
        '".$_SESSION['user_id']."',
        '".$_SESSION['user_name']."',
        '".$_SESSION['role']."'
    )");

    $qid = mysqli_insert_id($conn);

    foreach($_SESSION['cart'] as $item){

        $stock_note = mysqli_real_escape_string($conn,$item['stock_note'] ?? '');

        mysqli_query($conn,"INSERT INTO quotation_items
        (
            quotation_id,
            product_name,
            model,
            description,
            qty,
            price,
            total,
            stock_note
        )

        VALUES
        (
            '$qid',
            '".$item['name']."',
            '".$item['model']."',
            '".$item['description']."',
            '".$item['qty']."',
            '".$item['price']."',
            '".$item['total']."',
            '$stock_note'
        )");
    }

    unset($_SESSION['cart']);

    header("Location: preview_draft.php?id=".$qid);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Create Quotation</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI';
}

body{
    background:#eef2ff;
    padding:20px 12px;
}

/* CONTAINER */

.container{
    max-width:1150px;
    margin:auto;
}

/* CARD */

.box{
    background:#fff;
    padding:24px;
    border-radius:20px;
    box-shadow:0 8px 30px rgba(0,0,0,0.06);
    border:1px solid #e5e7eb;
}

/* TITLE */

.page-title{
    margin-bottom:22px;
}

.page-title h1{
    font-size:28px;
    color:#111827;
    margin-bottom:4px;
}

.page-title p{
    color:#6b7280;
    font-size:13px;
}

/* SECTION */

.section{
    margin-bottom:24px;
}

.section h3{
    font-size:17px;
    color:#111827;
    margin-bottom:14px;
    padding-bottom:8px;
    border-bottom:1px solid #ececec;
}

/* GRID */

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px 18px;
}

/* FULL WIDTH */

.full-width{
    grid-column:1 / -1;
}

/* INPUT */

.input-group{
    display:flex;
    flex-direction:column;
}

.input-group label{
    font-size:13px;
    font-weight:600;
    margin-bottom:6px;
    color:#374151;
}

/* INPUTS */

input,
textarea{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:10px;
    padding:10px 14px;
    font-size:13px;
    outline:none;
    transition:0.25s;
    background:#fafafa;
}

/* SMALL HEIGHT */

input{
    height:42px;
}

/* TEXTAREA */

textarea{
    min-height:90px;
    resize:vertical;
    padding-top:12px;
}

/* FOCUS */

input:focus,
textarea:focus{
    border-color:#2563eb;
    background:#fff;
    box-shadow:0 0 0 3px rgba(37,99,235,0.08);
}

/* BUTTON */

.submit-btn{
    margin-top:5px;
    background:linear-gradient(135deg,#16a34a,#15803d);
    color:white;
    border:none;
    height:46px;
    padding:0 24px;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 8px 18px rgba(22,163,74,0.18);
}

.submit-btn:hover{
    transform:translateY(-2px);
}

/* MOBILE */

@media(max-width:768px){

    body{
        padding:12px;
    }

    .box{
        padding:16px;
        border-radius:16px;
    }

    .page-title h1{
        font-size:23px;
    }

    .form-grid{
        grid-template-columns:1fr;
        gap:12px;
    }

    .full-width{
        grid-column:auto;
    }

    input{
        height:40px;
    }

    textarea{
        min-height:85px;
    }

}
/* EXISTING CUSTOMER DROPDOWN */

.customer-select{
    width:100%;
    height:48px;
    border:1px solid #dbe2ea;
    border-radius:12px;
    padding:0 14px;
    font-size:14px;
    background:#fff;
    margin-bottom:18px;
    outline:none;
    transition:0.3s ease;
    color:#111827;
}

.customer-select:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,0.10);
}

/* LABEL */

.select-label{
    font-size:14px;
    font-weight:600;
    color:#374151;
    margin-bottom:8px;
    display:block;
}

/* OPTIONAL BADGE */

.customer-badge{
    display:inline-block;
    background:#eff6ff;
    color:#2563eb;
    padding:5px 10px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
    margin-bottom:10px;
}

</style>

</head>

<body>
<?php include "sidebaar.php"; ?>
<div class="container">

<div class="box">

<div class="page-title">
    <h1>Create Quotation</h1>
    <p>Fill all quotation details carefully</p>
</div>

<form method="POST">
    <?php
$customers = mysqli_query($conn,"
SELECT DISTINCT
client_name,
client_company,
client_email,
client_phone,
client_address,
client_city,
client_state,
client_pincode
FROM quotations
ORDER BY client_name ASC
");
?>
    
    <div class="customer-badge">
    Existing Customer
</div>

<label class="select-label">
    Select Existing Customer
</label>

<select
id="customerSelect"
class="customer-select">

    <option value="">
        Select Existing Customer
    </option>

    <?php while($c = mysqli_fetch_assoc($customers)){ ?>

    <option
    value='<?php echo json_encode($c); ?>'>

        <?php echo $c['client_name']; ?>

        -
        
        <?php echo $c['client_company']; ?>

    </option>

    <?php } ?>

</select>

    <!-- CLIENT DETAILS -->

    <div class="section">

        <h3>Client Details</h3>

        <div class="form-grid">

            <div class="input-group">
                <label>Client Name</label>
                <input type="text" id="client_name" name="client_name" placeholder="Client Name" required>
            </div>

            <div class="input-group">
                <label>Company Name</label>
                <input type="text" id="client_company" name="client_company" placeholder="Company Name" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" id="client_email" name="client_email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" id="client_phone" name="client_phone" placeholder="Phone Number">
            </div>

            <div class="input-group full-width">
                <label>Address</label>
                <textarea id="client_address" name="client_address" placeholder="Address"></textarea>
            </div>

            <div class="input-group">
                <label>City</label>
                <input type="text" id="client_city" name="client_city" placeholder="City">
            </div>

            <div class="input-group">
                <label>State</label>
                <input type="text" id="client_state" name="client_state" placeholder="State">
            </div>

            <div class="input-group">
                <label>Pincode</label>
                <input type="text" id="client_pincode" name="client_pincode" placeholder="Pincode">
            </div>

        </div>

    </div>

    <!-- TERMS -->

    <div class="section">

        <h3>Terms & Conditions</h3>

        <div class="form-grid">

            <div class="input-group">
                <label>Packing</label>
                <input type="text" name="packing">
            </div>

            <div class="input-group">
                <label>Freight</label>
                <input type="text" name="freight">
            </div>

            <div class="input-group">
                <label>Delivery</label>
                <input type="text" name="delivery">
            </div>

            <div class="input-group">
                <label>Payment Terms</label>
                <input type="text" name="payment">
            </div>

            <div class="input-group">
                <label>Validity</label>
                <input type="text" name="validity">
            </div>

        </div>

    </div>

    <!-- EMAIL DETAILS -->

    <div class="section">

        <h3>Email Details</h3>

        <div class="form-grid">

            <div class="input-group">
                <label>CC Email</label>
                <input
                type="email"
                name="cc_email"
                placeholder="Optional">
            </div>

            <div class="input-group">
                <label>Email Subject</label>
                <input
                type="text"
                name="email_subject"
                value="Quotation from AS ASSOCIATES">
            </div>

            <div class="input-group full-width">

                <label>Email Message</label>

                <textarea
                name="email_message">Dear Sir/Madam,

Please find attached quotation for your requirement.

Regards,
AS ASSOCIATES</textarea>

            </div>

        </div>

    </div>

    <!-- SIGNATURE -->

    <div class="section">

        <h3>Signature Details</h3>

        <div class="form-grid">

            <div class="input-group">
                <label>Person Name</label>
                <input type="text" name="sign_name">
            </div>

            <div class="input-group">
                <label>Designation</label>
                <input type="text" name="designation">
            </div>

            <div class="input-group">
                <label>Phone</label>
                <input type="text" name="sign_phone">
            </div>

        </div>

    </div>

    <button type="submit" class="submit-btn">
        Create Quotation
    </button>

</form>

</div>

</div>
<script>

document.getElementById("customerSelect").addEventListener("change", function(){

    if(this.value == ""){
        return;
    }

    let data = JSON.parse(this.value);

    document.getElementById("client_name").value =
    data.client_name;

    document.getElementById("client_company").value =
    data.client_company;

    document.getElementById("client_email").value =
    data.client_email;

    document.getElementById("client_phone").value =
    data.client_phone;

    document.getElementById("client_address").value =
    data.client_address;

    document.getElementById("client_city").value =
    data.client_city;

    document.getElementById("client_state").value =
    data.client_state;

    document.getElementById("client_pincode").value =
    data.client_pincode;

});

</script>
</body>
</html>