<?php
include "config.php";

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn, "
    INSERT INTO users (name,email,password,role)
    VALUES ('$name','$email','$password','$role')
    ");

    echo "<script>alert('Staff Registered Successfully'); window.location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>
body {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: 'Segoe UI';
}

/* BOX */
.register-box {
    background: white;
    padding: 30px;
    width: 380px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity:0; transform: translateY(20px); }
    to { opacity:1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color:#1e293b;
}

/* INPUT */
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: 0.3s;
}

input:focus, select:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 5px rgba(99,102,241,0.3);
}

/* BUTTON */
button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* LOGIN LINK */
.login-link {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
}

.login-link a {
    color: #6366f1;
    text-decoration: none;
    font-weight: 500;
}

</style>

</head>

<body>

<div class="register-box">

<h2>Register Staff</h2>

<form method="POST">

<input type="text" name="name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email Address" required>

<input type="password" name="password" placeholder="Password" required>

<select name="role">
    <option value="staff">Staff</option>
    <option value="admin">Admin</option>
</select>

<button name="register">Create Account</button>

</form>

<div class="login-link">
    Already have account? <a href="login.php">Login</a>
</div>

</div>

</body>
</html>