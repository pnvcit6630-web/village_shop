<?php include("../config/db.php"); ?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
    body{
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg,#74ebd5,#9face6);
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-box{
        background:#fff;
        width:320px;
        padding:25px;
        border-radius:10px;
        box-shadow:0 10px 25px rgba(0,0,0,.15);
    }
    input, button, a{
        width:100%;
        padding:10px;
        margin-bottom:12px;
        border-radius:6px;
        box-sizing:border-box;
        display:block;
        text-align:center;
    }
    button{
        background:#007bff;
        color:#fff;
        border:none;
        cursor:pointer;
    }
    button:hover{
        background:#0056b3;
    }
    .back{
        background:#6c757d;
        color:#fff;
        text-decoration:none;
    }
    .back:hover{
        background:#5a6268;
    }
</style>
</head>
<body>

<div class="login-box">

<form method="post">
    <input name="email" placeholder="อีเมล">
    <input type="password" name="password" placeholder="รหัสผ่าน">
    <button name="login">Login</button>
</form>

<!-- 🔙 ปุ่มย้อนกลับหน้า index หลัก -->
<a href="../index.php" class="back">⬅ กลับหน้าแรก</a>

</div>

</body>
</html>

<?php
if(isset($_POST['login'])){
    $q = $conn->query("SELECT * FROM users WHERE email='{$_POST['email']}'");
    $u = $q->fetch_assoc();
    if(password_verify($_POST['password'],$u['password'])){
        $_SESSION['user_id']=$u['id'];
        header("location:home.php");
    }
}
?>
