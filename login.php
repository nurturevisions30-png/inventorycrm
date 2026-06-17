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

<style>
body {
    background: #f5f7fb;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: Arial;
}

.login-box {
    background: white;
    padding: 30px;
    width: 350px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

/* 🔥 NEW */
.register-link {
    text-align:center;
    margin-top:15px;
    font-size:14px;
}

.register-link a {
    color:#2563eb;
    text-decoration:none;
    font-weight:500;
}

.register-link a:hover {
    text-decoration:underline;
}

.error {
    color: red;
    text-align: center;
}
</style>

</head>

<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button name="login">Login</button>
    </form>

    <!-- ✅ REGISTER BUTTON -->


</div>

</body>
</html>