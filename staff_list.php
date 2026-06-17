<?php
session_start();
include "config.php";

/* ADMIN ONLY */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/* CREATE STAFF */
if(isset($_POST['register'])){

    $name = mysqli_real_escape_string($conn,$_POST['name']);

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    $password_raw = $_POST['password'];

    $confirm_password = $_POST['confirm_password'];

    /* PASSWORD MATCH CHECK */
    if($password_raw != $confirm_password){

        echo "
        <script>
        alert('Password & Confirm Password not matched');
        </script>
        ";

    }else{

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        /* CHECK EMAIL */
        $check = mysqli_query($conn,"
        SELECT * FROM users
        WHERE email='$email'
        ");

        if(mysqli_num_rows($check) > 0){

            echo "
            <script>
            alert('Email already exists');
            </script>
            ";

        }else{

$result = mysqli_query($conn,"
INSERT INTO users
(name,email,password,role,status)
VALUES
('$name','$email','$password','staff','active')
");

if(!$result){
    die("DB Error: " . mysqli_error($conn));
}

            /* SEND EMAIL */

try{

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'quotation@asasafety.in';
    $mail->Password = 'Quotation@asa1';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom(
        'quotation@asasafety.in',
        'AS Associates'
    );

    $mail->addAddress($email, $name);

    $mail->isHTML(true);

    $mail->Subject = 'Staff Account Created';

    $mail->Body = "
    <h2>Welcome To AS Associates</h2>

    <p>Hello <b>$name</b>,</p>

    <p>Your staff account has been created successfully.</p>

    <table border='1' cellpadding='10' cellspacing='0'>

        <tr>
            <td><b>Login URL</b></td>
            <td>https://asasafety.in/login.php</td>
        </tr>

        <tr>
            <td><b>Email</b></td>
            <td>$email</td>
        </tr>

        <tr>
            <td><b>Password</b></td>
            <td>$password_raw</td>
        </tr>

    </table>

    <br>

    Regards,<br>
    AS Associates
    ";

    $mail->send();

}catch(Exception $e){

    die("Mail Error: " . $mail->ErrorInfo);

}



            echo "
            <script>
            alert('Staff Created Successfully');
            window.location='staff_list.php';
            </script>
            ";
        }

    }
}

/* STAFF DATA */
$query = mysqli_query($conn,"
SELECT
u.id,
u.name,
u.email,
u.status,
COUNT(q.id) as total_quotes,
IFNULL(SUM(q.total_amount),0) as revenue
FROM users u
LEFT JOIN quotations q
ON u.name = q.created_by
WHERE u.role='staff'
GROUP BY u.id
ORDER BY u.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Staff Management</title>

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
    padding:25px;
}

/* TOP */

.topbar{
    margin-bottom:25px;
}

.topbar h1{
    font-size:30px;
    color:#111827;
}

.topbar p{
    color:#6b7280;
    margin-top:5px;
}

/* GRID */

.main-grid{
    display:grid;
    grid-template-columns:380px 1fr;
    gap:20px;
    align-items:start;
}

/* CARD */

.card{
    background:white;
    border-radius:22px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    border:1px solid #eef2f7;
}

/* FORM */

.form-title{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:20px;
}

.form-title i{
    width:42px;
    height:42px;
    border-radius:12px;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
}

.form-title h2{
    font-size:20px;
    color:#111827;
}

.input-group{
    margin-bottom:15px;
}

.input-group label{
    display:block;
    font-size:13px;
    font-weight:600;
    margin-bottom:6px;
    color:#374151;
}

.input-group input{
    width:100%;
    height:46px;
    border:1px solid #dbe2ea;
    border-radius:12px;
    padding:0 14px;
    outline:none;
    transition:.3s;
    background:#fafafa;
}

.input-group input:focus{
    border-color:#2563eb;
    background:white;
    box-shadow:0 0 0 4px rgba(37,99,235,0.08);
}

.create-btn{
    width:100%;
    height:48px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.create-btn:hover{
    transform:translateY(-2px);
}

/* STAFF GRID */

.staff-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:18px;
}

/* STAFF CARD */

.staff-card{
    background:white;
    border-radius:22px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    border:1px solid #eef2f7;
    transition:.3s;
}

.staff-card:hover{
    transform:translateY(-4px);
}

/* HEADER */

.staff-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.staff-info h3{
    font-size:18px;
    color:#111827;
}

.staff-info p{
    font-size:13px;
    color:#6b7280;
    margin-top:4px;
}

/* AVATAR */

.avatar{
    width:52px;
    height:52px;
    border-radius:16px;
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    font-weight:700;
}

/* STATS */

.stats{
    display:flex;
    justify-content:space-between;
    margin-top:18px;
}

.stat-box{
    flex:1;
    background:#f9fafb;
    padding:12px;
    border-radius:14px;
    text-align:center;
}

.stat-box h2{
    font-size:18px;
    color:#111827;
}

.stat-box span{
    font-size:12px;
    color:#6b7280;
}

/* STATUS */

.status{
    margin-top:16px;
    display:inline-block;
    padding:7px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

.active{
    background:#dcfce7;
    color:#16a34a;
}

.inactive{
    background:#fee2e2;
    color:#dc2626;
}

/* ACTIONS */

.actions{
    display:flex;
    gap:10px;
    margin-top:18px;
}

.btn{
    flex:1;
    height:42px;
    border:none;
    border-radius:12px;
    text-decoration:none;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    color:white;
    font-size:13px;
    font-weight:600;
    transition:.3s;
}

.view-btn{
    background:#111827;
}

.hold-btn{
    background:#f59e0b;
}

.activate-btn{
    background:#16a34a;
}

.btn:hover{
    transform:translateY(-2px);
}

/* MOBILE */

@media(max-width:900px){

    .main-grid{
        grid-template-columns:1fr;
    }

}

@media(max-width:600px){

    .page{
        padding:15px;
    }

    .staff-grid{
        grid-template-columns:1fr;
    }

}
/* PASSWORD BOX */

.password-box{
    position:relative;
}

.password-box input{
    padding-right:45px !important;
}

/* EYE ICON */

.toggle-pass{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#6b7280;
    font-size:15px;
    transition:.3s;
}

.toggle-pass:hover{
    color:#111827;
}
</style>

</head>

<body>

<?php include "sidebaar.php"; ?>

<div class="page">

<!-- TOP -->
<div class="topbar">

    <h1>Staff Management</h1>
    <p>Create staff accounts, manage status & track quotation performance</p>

</div>

<div class="main-grid">

    <!-- CREATE STAFF -->
    <div class="card">

        <div class="form-title">
            <i class="fa-solid fa-user-plus"></i>
            <h2>Create Staff</h2>
        </div>

        <form method="POST">

            <div class="input-group">
                <label>Full Name</label>
                <input
                type="text"
                name="name"
                placeholder="Enter staff name"
                required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input
                type="email"
                name="email"
                placeholder="Enter email"
                required>
            </div>

           <!-- PASSWORD -->

<div class="input-group">

    <label>Password</label>

    <div class="password-box">

        <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter password"
        required>

        <i
        class="fa-solid fa-eye toggle-pass"
        onclick="togglePassword('password', this)">
        </i>

    </div>

</div>

<!-- CONFIRM PASSWORD -->

<div class="input-group">

    <label>Confirm Password</label>

    <div class="password-box">

        <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        placeholder="Confirm password"
        required>

        <i
        class="fa-solid fa-eye toggle-pass"
        onclick="togglePassword('confirm_password', this)">
        </i>

    </div>

</div>

            <button class="create-btn" name="register">

                <i class="fa-solid fa-plus"></i>
                Create Staff Account

            </button>

        </form>

    </div>

    <!-- STAFF LIST -->
    <div class="staff-grid">

    <?php while($row = mysqli_fetch_assoc($query)){ ?>

        <div class="staff-card">

            <!-- HEADER -->
            <div class="staff-header">

                <div class="staff-info">

                    <h3>
                        <?php echo $row['name']; ?>
                    </h3>

                    <p>
                        <?php echo $row['email']; ?>
                    </p>

                </div>

                <div class="avatar">

                    <?php
                    echo strtoupper(substr($row['name'],0,1));
                    ?>

                </div>

            </div>

            <!-- STATS -->
            <div class="stats">

                <div class="stat-box">

                    <h2>
                        <?php echo $row['total_quotes']; ?>
                    </h2>

                    <span>Total Quotes</span>

                </div>

                <div class="stat-box">

                    <h2>
                        ₹<?php echo number_format($row['revenue']); ?>
                    </h2>

                    <span>Revenue</span>

                </div>

            </div>

            <!-- STATUS -->
            <div class="status <?php echo $row['status']; ?>">

                <?php echo ucfirst($row['status']); ?>

            </div>

            <!-- ACTIONS -->
            <div class="actions">

                <a
                href="staff_profile.php?id=<?php echo $row['id']; ?>"
                class="btn view-btn">

                    <i class="fa-solid fa-eye"></i>
                    View

                </a>

                <?php if($row['status'] == 'active'){ ?>

                <a
                href="update_user_status.php?id=<?php echo $row['id']; ?>&status=inactive"
                class="btn hold-btn">

                    Hold

                </a>

                <?php } else { ?>

                <a
                href="update_user_status.php?id=<?php echo $row['id']; ?>&status=active"
                class="btn activate-btn">

                    Activate

                </a>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

    </div>

</div>

</div>
<script>

function togglePassword(id, icon){

    let input = document.getElementById(id);

    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove("fa-eye");

        icon.classList.add("fa-eye-slash");

    }else{

        input.type = "password";

        icon.classList.remove("fa-eye-slash");

        icon.classList.add("fa-eye");

    }
}

</script>
</body>
</html>