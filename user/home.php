<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔒 ตรวจสอบล็อกอิน */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

/* ✅ ดึงชื่อผู้ใช้ */
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$userName = "ผู้ใช้";

if($res->num_rows > 0){
    $user = $res->fetch_assoc();
    $userName = $user['name'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>หน้าหลัก | สินค้า</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background:#f1f4f9;
    font-family:'Segoe UI',sans-serif;
}

/* Sidebar */

.sidebar{
    min-height:100vh;
    background:linear-gradient(180deg,#198754,#157347);
    color:white;
}

.sidebar a{
    color:white;
    padding:12px;
    display:block;
    text-decoration:none;
    border-radius:10px;
    margin-bottom:6px;
    transition:0.2s;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.2);
}

.sidebar a.active{
    background:rgba(255,255,255,0.3);
}

.user-box{
    background:rgba(255,255,255,0.15);
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}

/* Content */

.content{
    padding:25px;
}

/* Product Card */

.product-card{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:0.3s;
}

.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 15px 30px rgba(0,0,0,0.15);
}

.product-img{
    height:350px;
    object-fit:cover;
    border-top-left-radius:15px;
    border-top-right-radius:15px;
}

.product-desc{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.price{
    font-size:18px;
    font-weight:bold;
    color:#198754;
}

.btn-cart{
    background:#198754;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-cart:hover{
    background:#157347;
    color:white;
}

.section-title{
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

<!-- SIDEBAR -->
<div class="col-lg-2 col-md-3 sidebar p-3">

<h4 class="text-center mb-3">

<i class="bi bi-shop"></i>
ร้านค้า

</h4>

<div class="user-box text-center">

<i class="bi bi-person-circle fs-4"></i>
<br>

<?= htmlspecialchars($userName) ?>

</div>

<a class="active">

<i class="bi bi-house"></i>
หน้าหลัก

</a>

<a href="cart.php">

<i class="bi bi-cart"></i>
ตะกร้าสินค้า

</a>

<a href="orders.php">

<i class="bi bi-receipt"></i>
ออเดอร์ของฉัน

</a>

<a href="../logout.php"
onclick="return confirm('ต้องการออกจากระบบหรือไม่?')">

<i class="bi bi-box-arrow-right"></i>
ออกจากระบบ

</a>

</div>


<!-- CONTENT -->
<div class="col-lg-10 col-md-9 content">

<h4 class="section-title mb-4">

<i class="bi bi-bag"></i>
สินค้าทั้งหมด

</h4>

<div class="row g-4">

<?php

$sql = "SELECT id,name,price,image,description FROM products ORDER BY id DESC";

$result = $conn->query($sql);

if($result->num_rows > 0):

while($row = $result->fetch_assoc()):

$imgPath = "../uploads/products/".$row['image'];

if(empty($row['image']) || !file_exists($imgPath)){
    $imgPath = "../uploads/no-image.png";
}

?>

<div class="col-xl-3 col-lg-4 col-md-6">

<div class="card product-card h-100">

<img src="<?= htmlspecialchars($imgPath) ?>"
class="product-img w-100">

<div class="card-body d-flex flex-column">

<h6 class="fw-bold">

<?= htmlspecialchars($row['name']) ?>

</h6>

<p class="text-muted small product-desc flex-grow-1">

<?= htmlspecialchars($row['description']) ?>

</p>

<div class="price mb-2">

<?= number_format($row['price'],2) ?> บาท

</div>

<a href="cart.php?add=<?= $row['id'] ?>"
class="btn btn-cart w-100">

<i class="bi bi-cart-plus"></i>
ใส่ตะกร้า

</a>

</div>

</div>

</div>

<?php endwhile; else: ?>

<div class="col-12 text-center text-muted">

ยังไม่มีสินค้า

</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

</body>

</html>