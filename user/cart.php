<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔒 เช็คล็อกอิน */
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


/* ➕ เพิ่มสินค้า */
if (isset($_GET['add'])) {

    $id = intval($_GET['add']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }

    header("Location: cart.php");
    exit;
}


/* ❌ ลบสินค้า */
if (isset($_GET['remove'])) {

    $id = intval($_GET['remove']);

    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    header("Location: cart.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>ตะกร้าสินค้า</title>

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


/* Card */

.card-custom{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}


/* Table */

.table thead{
    background:#f1f4f9;
}


/* Total */

.total-box{
    font-size:22px;
    font-weight:bold;
    color:#198754;
}


/* Button */

.btn-success{
    border-radius:20px;
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


<a href="home.php">
<i class="bi bi-house"></i>
หน้าหลัก
</a>


<a class="active">
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


<h4 class="mb-4">

<i class="bi bi-cart-fill"></i>
ตะกร้าสินค้า

</h4>


<div class="card card-custom">

<div class="card-body">


<div class="table-responsive">

<table class="table align-middle text-center">


<thead>

<tr>

<th class="text-start">สินค้า</th>

<th>ราคา</th>

<th>จำนวน</th>

<th>รวม</th>

<th>ลบ</th>

</tr>

</thead>


<tbody>

<?php

$total = 0;

if(isset($_SESSION['cart']) && count($_SESSION['cart'])>0):

foreach($_SESSION['cart'] as $id=>$qty):

$stmt = $conn->prepare("SELECT name,price FROM products WHERE id=?");

$stmt->bind_param("i",$id);

$stmt->execute();

$r=$stmt->get_result();


if($row=$r->fetch_assoc()):

$sum=$row['price']*$qty;

$total+=$sum;

?>

<tr>

<td class="text-start">

<?= htmlspecialchars($row['name']) ?>

</td>


<td>

<?= number_format($row['price'],2) ?>

</td>


<td>

<?= $qty ?>

</td>


<td class="text-success fw-bold">

<?= number_format($sum,2) ?>

</td>


<td>

<a href="?remove=<?= $id ?>"
class="btn btn-danger btn-sm">

<i class="bi bi-trash"></i>

</a>

</td>


</tr>


<?php endif; endforeach; else: ?>


<tr>

<td colspan="5" class="text-muted">

ไม่มีสินค้าในตะกร้า

</td>

</tr>


<?php endif; ?>


</tbody>

</table>


</div>


<div class="text-end total-box">

รวมทั้งหมด: <?= number_format($total,2) ?> บาท

</div>


</div>

</div>



<?php if($total>0): ?>


<div class="card card-custom mt-4">

<div class="card-body">


<form method="post" action="checkout_process.php">


<label class="form-label">

ที่อยู่จัดส่ง

</label>


<textarea name="address"
class="form-control mb-3"
required></textarea>



<label class="form-label">

วิธีชำระเงิน

</label>


<div class="form-check">

<input class="form-check-input"
type="radio"
name="payment_method"
value="cod"
required>

<label class="form-check-label">

เก็บเงินปลายทาง

</label>

</div>


<div class="form-check mb-3">

<input class="form-check-input"
type="radio"
name="payment_method"
value="scan">

<label class="form-check-label">

สแกนจ่าย

</label>

</div>


<button class="btn btn-success w-100">

ยืนยันการสั่งซื้อ

</button>


</form>


</div>

</div>


<?php endif; ?>


</div>

</div>

</div>


</body>

</html>