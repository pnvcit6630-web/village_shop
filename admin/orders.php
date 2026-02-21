<?php
require_once("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔐 ตรวจสอบ admin */
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* 📦 ดึงข้อมูล orders */
$stmt = $conn->prepare("SELECT * FROM orders ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>จัดการออเดอร์</title>

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
    background:linear-gradient(180deg,#667eea,#764ba2);
    color:white;
}

.sidebar a{
    color:white;
    padding:12px;
    display:block;
    text-decoration:none;
    border-radius:8px;
    margin-bottom:5px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.2);
}

/* Card */
.card-main{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* Table */
.table thead{
    background:#667eea;
    color:white;
}

.table tbody tr:hover{
    background:#f8f9ff;
}

/* Badge */
.badge-status{
    padding:6px 14px;
    border-radius:20px;
    font-size:13px;
}

.badge-pending{
    background:#fff3cd;
    color:#856404;
}

.badge-paid{
    background:#d1e7dd;
    color:#0f5132;
}

.badge-cancelled{
    background:#f8d7da;
    color:#842029;
}

/* Buttons */
.btn-confirm{
    background:#198754;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-delete{
    background:#dc3545;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-confirm:hover,
.btn-delete:hover{
    opacity:0.9;
}

/* Header */
.header-title{
    font-weight:bold;
}

</style>

</head>
<body>

<div class="container-fluid">

<div class="row">

<!-- Sidebar -->
<div class="col-lg-2 col-md-3 sidebar p-3">

<h4 class="text-center mb-4">
<i class="bi bi-speedometer2"></i>
Admin
</h4>

<a href="dashboard.php">
<i class="bi bi-house"></i> Dashboard
</a>

<a href="orders.php">
<i class="bi bi-box"></i> ออเดอร์
</a>

<a href="users.php">
<i class="bi bi-people"></i> ผู้ใช้
</a>

<a href="add_product.php">
<i class="bi bi-plus-circle"></i> เพิ่มสินค้า
</a>

<a href="../logout.php">
<i class="bi bi-box-arrow-right"></i> ออกจากระบบ
</a>

</div>


<!-- Main Content -->
<div class="col-lg-10 col-md-9 p-4">

<h3 class="mb-3">
<i class="bi bi-box-seam"></i>
จัดการออเดอร์
</h3>

<div class="card card-main p-3">

<div class="table-responsive">

<table class="table table-hover align-middle text-center">

<thead>
<tr>
<th>#</th>
<th>ผู้ใช้</th>
<th>ที่อยู่</th>
<th>ยอดรวม</th>
<th>การชำระเงิน</th>
<th>สถานะ</th>
<th>จัดการ</th>
</tr>
</thead>

<tbody>

<?php if($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td>
<strong>#<?= (int)$row['id'] ?></strong>
</td>

<td>
<i class="bi bi-person-circle text-primary"></i>
<?= htmlspecialchars($row['user_id']) ?>
</td>

<td class="text-start" style="max-width:250px;">
<?= nl2br(htmlspecialchars($row['address'])) ?>
</td>

<td class="text-success fw-bold">
<?= number_format($row['total'],2) ?> บาท
</td>

<td>

<?php if (!empty($row['slip']) && $row['payment_method'] !== 'cod'): ?>

<a href="../uploads/slips/<?= htmlspecialchars($row['slip']) ?>"
target="_blank"
class="btn btn-outline-primary btn-sm rounded-pill">

<i class="bi bi-receipt"></i>
ดูสลิป

</a>

<?php elseif ($row['payment_method'] === 'cod'): ?>

<span class="badge bg-warning text-dark rounded-pill">
เก็บเงินปลายทาง
</span>

<?php else: ?>

-

<?php endif; ?>

</td>

<td>

<?php

if ($row['status'] === 'pending') {

echo '<span class="badge-status badge-pending">รอตรวจสอบ</span>';

}
elseif ($row['status'] === 'paid') {

echo '<span class="badge-status badge-paid">ชำระแล้ว</span>';

}
elseif ($row['status'] === 'cancelled') {

echo '<span class="badge-status badge-cancelled">ยกเลิก</span>';

}

?>

</td>

<td>

<?php if ($row['status'] === 'pending'): ?>

<a href="update_order.php?id=<?= $row['id'] ?>"
class="btn btn-confirm btn-sm mb-1"
onclick="return confirm('ยืนยันการชำระเงิน?')">

<i class="bi bi-check-circle"></i>
ยืนยัน

</a>

<?php endif; ?>


<a href="delete_order.php?id=<?= $row['id'] ?>"
class="btn btn-delete btn-sm"
onclick="return confirm('ลบออเดอร์นี้?')">

<i class="bi bi-trash"></i>
ลบ

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="7" class="text-muted py-4">
ยังไม่มีข้อมูล
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

</body>
</html>