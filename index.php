<?php
include("config/db.php");

/* ถ้า login แล้ว */
if(isset($_SESSION['user_id'])){
    if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
        header("location:admin/dashboard.php");
    }else{
        header("location:user/home.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ระบบขายของออนไลน์ในหมู่บ้าน</title>
    <style>
        body{
            font-family:Arial;
            background:#f4f4f4;
            text-align:center;
        }
        .box{
            background:#fff;
            width:350px;
            margin:100px auto;
            padding:30px;
            border-radius:10px;
            box-shadow:0 0 10px #ccc;
        }
        a{
            display:block;
            margin:10px 0;
            padding:10px;
            background:#007bff;
            color:#fff;
            text-decoration:none;
            border-radius:5px;
        }
        .admin{
            background:#333;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🛒 ระบบขายของออนไลน์</h2>
    <p>ภายในหมู่บ้าน</p>

    <a href="user/login.php">👤 เข้าสู่ระบบผู้ใช้</a>
    <a href="user/register.php">✍ สมัครสมาชิก</a>
    <hr>
    <a href="admin/login.php" class="admin">🔐 เข้าสู่ระบบแอดมิน</a>
</div>

</body>
</html>
