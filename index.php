<?php
session_start();
include "config.php";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "Please fill all fields!";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user){

            if($user['status'] != 'active'){
                $error = "Your account is inactive!";
            }

            elseif(password_verify($password, $user['password'])){

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'admin'){
                    header("Location: dashboard.php");
                } else {
                    header("Location: staff_dashboard.php");
                }
                exit();

            } else {
                $error = "Wrong Password!";
            }

        } else {
            $error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, sans-serif;
}

body{
    background:#f5f7fb;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.container{
    width:1000px;
    max-width:95%;
    background:#fff;
    display:flex;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 5px 25px rgba(0,0,0,0.08);
}

/* LEFT SIDE */

.left{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
}

.left img{
    max-width:250px;
}

/* RIGHT SIDE */

.right{
    width:400px;
    padding:60px 27px;
}

.right h2{
    font-size:32px;
    margin-bottom:10px;
    color:#111827;
}

.subtitle{
    color:#6b7280;
    margin-bottom:35px;
    font-size:14px;
}

label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    color:#374151;
}

input[type=email],
input[type=password],
input[type=text]{
    width:100%;
    padding:12px 15px;
    border:1px solid #d1d5db;
    border-radius:6px;
    margin-bottom:20px;
}

.password-box{
    position:relative;
}

.toggle-pass{
    position:absolute;
    right:15px;
    top:38%;
    cursor:pointer;
    color:#6b7280;
}

.options{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    font-size:14px;
}

.options a{
    color:#2563eb;
    text-decoration:none;
}

.options a:hover{
    text-decoration:underline;
}

.signin-btn{
    width:100%;
    padding:12px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:15px;
    margin-bottom:15px;
}

.google-btn{
    width:100%;
    padding:12px;
    background:#fff;
    border:1px solid #d1d5db;
    border-radius:6px;
    cursor:pointer;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
    font-size:14px;
}

.signup{
    text-align:center;
    margin-top:25px;
    font-size:14px;
    color:#6b7280;
}

.signup a{
    color:#2563eb;
    text-decoration:none;
}

.error{
    color:red;
    margin-bottom:20px;
}

@media(max-width:768px){

    .container{
        flex-direction:column;
    }

    .right{
        width:100%;
    }
}
</style>

</head>

<body>

<div class="container">

    <div class="left">
        <!-- Replace logo.png with your actual logo -->
        <img src="upload/logo.png" alt="AS Associates">
    </div>

    <div class="right">

        <h2>Log in to your account</h2>

        <p class="subtitle">
            Welcome back! Please enter your details.
        </p>

        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required>

            <label>Password</label>

            <div class="password-box">

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="••••••••"
                    required>

                <i class="fa-solid fa-eye toggle-pass"
                   onclick="togglePassword('password', this)">
                </i>

            </div>

            <div class="options">

                <label>
                    <input type="checkbox">
                    Remember for 30 days
                </label>

                <a href="#">Forgot password</a>

            </div>

            <button class="signin-btn" name="login">
                Sign in
            </button>

        </form>


    </div>

</div>

<script>
function togglePassword(id, icon){

    let input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        icon.classList.replace("fa-eye","fa-eye-slash");
    }else{
        input.type = "password";
        icon.classList.replace("fa-eye-slash","fa-eye");
    }
}
</script>

</body>
</html>