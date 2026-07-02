<?php
require_once __DIR__.'/config/config.php';

if(isLoggedIn()){
    redirect('views/dashboard.php');
}

$error="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $email=$_POST["email"]??"";
    $password=$_POST["password"]??"";

    $userModel=new User();
    $user=$userModel->login($email,$password);

    if($user){

        User::startSession($user);

        redirect("views/dashboard.php");

    }else{

        $error="Invalid email or password.";

    }

}
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Smart Library Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/login.css">

</head>

<body>

<div class="login-container">

<div class="logo">

📚

</div>

<h1 class="title">

Welcome back

</h1>

<p class="subtitle">

Smart Library Management System

</p>

<div class="card">

<?php if($error): ?>

<div class="error">

<?=e($error)?>

</div>

<?php endif; ?>

<form method="POST">

<div class="form-group">

<label>Email address</label>

<input

type="email"

name="email"

placeholder="Enter your email"

required

>

</div>

<div class="form-group">

<div class="password-row">

<label>Password</label>

<a href="#" class="forgot">

Forgot password?

</a>

</div>

<div class="password-box">

<input

type="password"

name="password"

id="password"

placeholder="Enter your password"

required

>

<i class="fa-regular fa-eye" id="toggle"></i>

</div>

</div>

<button class="login-btn">

Log in

</button>

</form>

<div class="demo">

<b>Demo Account</b>

<br><br>

Admin

<br>

admin@library.edu

<br>

Password: admin123

<br><br>

Student

<br>

whes@student.edu

<br>

Password: student123

</div>

</div>

<div class="footer">

© 2026 Smart Library

</div>

</div>

<script>

const toggle=document.getElementById("toggle");

const pass=document.getElementById("password");

toggle.onclick=function(){

if(pass.type=="password"){

pass.type="text";

toggle.className="fa-regular fa-eye-slash";

}else{

pass.type="password";

toggle.className="fa-regular fa-eye";

}

}

</script>

</body>

</html>