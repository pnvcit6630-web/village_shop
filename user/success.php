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

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* พื้นหลังขาวฟ้า สบายตา */
body{
    background: linear-gradient(to bottom, #ffffff, #e3f2fd);
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* กล่องหลัก */
.success-card{
    border:none;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    animation:fadeIn .6s ease;
    background:#ffffff;
}

/* ไอคอน */
.success-icon{
    font-size:75px;
    color:#0d6efd;
    animation:pop .5s ease;
}

/* ปุ่ม */
.btn-custom{
    border-radius:10px;
    padding:12px;
    font-size:16px;
    transition:.25s;
}

.btn-custom:hover{
    transform:translateY(-1px);
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

/* animation */
@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes pop{
    0%{
        transform:scale(0.7);
        opacity:0;
    }
    100%{
        transform:scale(1);
        opacity:1;
    }
}

</style>

</head>
<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-6 col-lg-5">

<div class="card success-card">

<div class="card-body text-center p-5">

<div class="success-icon mb-3">
<i class="bi bi-check-circle-fill"></i>
</div>

<h2 class="text-primary fw-bold mb-3">
สั่งซื้อสำเร็จ
</h2>

<p class="text-muted mb-2">
ขอบคุณสำหรับการสั่งซื้อสินค้า
</p>

<p class="text-muted mb-4">
ระบบได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว
</p>

<div class="d-grid gap-2">

<a href="home.php"
class="btn btn-primary btn-custom">

<i class="bi bi-house"></i>
กลับหน้าหลัก

</a>

<a href="orders.php"
class="btn btn-outline-primary btn-custom">

<i class="bi bi-receipt"></i>
ดูออเดอร์ของฉัน

</a>

</div>

<hr>

<div class="text-muted small">

<i class="bi bi-shield-check"></i>
ร้านค้าจะตรวจสอบและดำเนินการโดยเร็วที่สุด

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>