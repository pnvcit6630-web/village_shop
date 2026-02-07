<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สั่งซื้อสำเร็จ</title>
    <style>
        body{
            font-family: Tahoma, Arial;
            background:#f5f5f5;
            text-align:center;
            padding-top:80px;
        }
        .box{
            background:#fff;
            width:420px;
            margin:auto;
            padding:30px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        h1{color:#2e7d32;}
        a{
            display:inline-block;
            margin-top:20px;
            text-decoration:none;
            background:#2e7d32;
            color:#fff;
            padding:10px 25px;
            border-radius:5px;
        }
        a:hover{background:#1b5e20;}
    </style>
</head>
<body>

<div class="box">
    <h1>✅ สั่งซื้อสำเร็จ</h1>
    <p>ขอบคุณสำหรับการสั่งซื้อ</p>
    <p>ร้านค้าจะตรวจสอบการชำระเงินของคุณโดยเร็ว</p>

    <a href="home.php">กลับไปหน้าหลัก</a>
</div>

</body>
</html>
