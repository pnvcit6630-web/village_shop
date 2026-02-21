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

$user_id = $_SESSION['user_id'];

/* ✅ ดึงชื่อผู้ใช้ */
$stmt = $conn->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultUser = $stmt->get_result();

$userName = "ผู้ใช้";

if($resultUser->num_rows > 0){
    $user = $resultUser->fetch_assoc();
    $userName = $user['name'];
}


/* ❌ ยกเลิกออเดอร์ */
if (isset($_POST['cancel_order'], $_POST['order_id'])) {

    $oid = intval($_POST['order_id']);

    $stmt = $conn->prepare(
        "UPDATE orders
         SET status='cancelled'
         WHERE id=? AND user_id=? AND status='pending'"
    );

    $stmt->bind_param("ii", $oid, $user_id);
    $stmt->execute();

    header("Location: orders.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>ออเดอร์ของฉัน</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* =====================
   BODY
===================== */

body{
    background:#f1f4f9;
    font-family:'Segoe UI',sans-serif;
}


/* =====================
   SIDEBAR
===================== */

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


/* =====================
   CONTENT
===================== */

.content{
    padding:25px;
}


/* =====================
   CARD
===================== */

.card-custom{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}


/* =====================
   TABLE
===================== */

.table thead{
    background:#f1f4f9;
}

.table td{
    vertical-align:middle;
}


/* =====================
   BADGE
===================== */

.badge-paid{
    background:#198754;
}

.badge-pending{
    background:#ffc107;
    color:#000;
}

.badge-cancelled{
    background:#dc3545;
}


/* =====================
   BUTTON
===================== */

.btn-danger,
.btn-info{
    border-radius:20px;
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

<a href="home.php">
<i class="bi bi-house"></i>
หน้าหลัก
</a>

<a href="cart.php">
<i class="bi bi-cart"></i>
ตะกร้าสินค้า
</a>

<a class="active">
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

<i class="bi bi-receipt"></i>
ออเดอร์ของฉัน

</h4>


<div class="card card-custom">

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover text-center">

<thead>

<tr>

<th>ID</th>
<th>วันที่</th>
<th>ยอดรวม</th>
<th>ที่อยู่</th>
<th>สลิป</th>
<th>สถานะ</th>
<th>จัดการ</th>

</tr>

</thead>

<tbody>

<?php

$q = $conn->prepare(
    "SELECT id, created_at, total, address, slip, status
     FROM orders
     WHERE user_id=?
     ORDER BY id DESC"
);

$q->bind_param("i", $user_id);
$q->execute();

$result = $q->get_result();

if($result->num_rows > 0):

while($o = $result->fetch_assoc()):

$slipWebPath = "../uploads/slips/" . $o['slip'];
$slipServerPath = __DIR__ . "/../uploads/slips/" . $o['slip'];

?>

<tr>

<td>

<strong>#<?= $o['id'] ?></strong>

</td>

<td>

<?= date("d/m/Y H:i", strtotime($o['created_at'])) ?>

</td>

<td class="text-success fw-bold">

<?= number_format($o['total'],2) ?> บาท

</td>

<td class="text-start">

<?= htmlspecialchars($o['address']) ?>

</td>

<td>

<?php if(!empty($o['slip']) && file_exists($slipServerPath)): ?>

<a href="<?= htmlspecialchars($slipWebPath) ?>"
target="_blank"
class="btn btn-info btn-sm">

<i class="bi bi-image"></i>
ดูสลิป

</a>

<?php else: ?>

<span class="text-muted">-</span>

<?php endif; ?>

</td>

<td>

<?php

if($o['status']=="paid"){
echo '<span class="badge badge-paid">ชำระแล้ว</span>';
}
elseif($o['status']=="cancelled"){
echo '<span class="badge badge-cancelled">ยกเลิกแล้ว</span>';
}
else{
echo '<span class="badge badge-pending">รอตรวจสอบ</span>';
}

?>

</td>

<td>

<?php if($o['status']=="pending"): ?>

<form method="post"
onsubmit="return confirm('ยกเลิกออเดอร์หรือไม่?')">

<input type="hidden"
name="order_id"
value="<?= $o['id'] ?>">

<button type="submit"
name="cancel_order"
class="btn btn-danger btn-sm">

<i class="bi bi-x-circle"></i>
ยกเลิก

</button>

</form>

<?php else: ?>

<span class="text-muted">-</span>

<?php endif; ?>

</td>

</tr>

<?php endwhile; else: ?>

<tr>

<td colspan="7" class="text-muted">

ยังไม่มีออเดอร์

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>