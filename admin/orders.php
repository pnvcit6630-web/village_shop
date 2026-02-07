<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Admin Guard
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$result = $conn->query(
    "SELECT * FROM orders ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการออเดอร์</title>

<style>
body{
    font-family:Tahoma, Arial;
    background:#f2f4f7;
    padding:20px;
}
.container{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}
.back-btn{
    text-decoration:none;
    background:#607d8b;
    color:#fff;
    padding:8px 15px;
    border-radius:5px;
}
table{
    border-collapse:collapse;
    width:100%;
}
th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
    font-size:14px;
}
th{background:#f5f5f5}
tr:nth-child(even){background:#fafafa}

/* 🔔 สถานะ */
.badge-pending{color:#ff9800;font-weight:bold}
.badge-paid{color:#2e7d32;font-weight:bold}
.badge-cancelled{color:#d32f2f;font-weight:bold}

/* 🔘 ปุ่ม */
.confirm-btn{
    text-decoration:none;
    color:#fff;
    background:#4caf50;
    padding:5px 10px;
    border-radius:4px;
    font-size:13px;
}
.delete-btn{
    text-decoration:none;
    color:#fff;
    background:#f44336;
    padding:5px 10px;
    border-radius:4px;
    font-size:13px;
    margin-left:5px;
}
.confirm-btn:hover{background:#388e3c}
.delete-btn:hover{background:#d32f2f}
</style>
</head>
<body>

<div class="container">

<div class="top-bar">
    <h2>📦 รายการออเดอร์</h2>
    <a href="dashboard.php" class="back-btn">← ย้อนกลับ</a>
</div>

<table>
<tr>
    <th>#</th>
    <th>ผู้ใช้</th>
    <th>ที่อยู่</th>
    <th>ยอดรวม</th>
    <th>สลิป</th>
    <th>สถานะ</th>
    <th>จัดการ</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['user_id'] ?></td>
    <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
    <td><?= number_format($row['total'],2) ?> บาท</td>

<td>
    <?php if (
        !empty($row['slip']) &&
        (!isset($row['payment_method']) || $row['payment_method'] !== 'cod')
    ): ?>
        <a href="../uploads/slips/<?= htmlspecialchars($row['slip']) ?>" target="_blank">
            ดูสลิป
        </a>

    <?php elseif (
        isset($row['payment_method']) &&
        $row['payment_method'] === 'cod'
    ): ?>
        💵 เก็บเงินปลายทาง

    <?php else: ?>
        -
    <?php endif; ?>
</td>



    <td>
        <?php
            if ($row['status'] === 'pending') {
                echo '<span class="badge-pending">⏳ รอตรวจสอบ</span>';
            } elseif ($row['status'] === 'paid') {
                echo '<span class="badge-paid">✅ ชำระแล้ว</span>';
            } elseif ($row['status'] === 'cancelled') {
                echo '<span class="badge-cancelled">❌ ยกเลิกแล้ว</span>';
            }
        ?>
    </td>

    <td>
        <?php if ($row['status'] === 'pending'): ?>
            <a class="confirm-btn"
               href="update_order.php?id=<?= $row['id'] ?>"
               onclick="return confirm('ยืนยันการชำระเงินออเดอร์นี้?');">
               ✔ ยืนยัน
            </a>
        <?php else: ?>
            -
        <?php endif; ?>

        <a class="delete-btn"
           href="delete_order.php?id=<?= $row['id'] ?>"
           onclick="return confirm('ต้องการลบออเดอร์นี้ใช่หรือไม่?');">
           🗑 ลบ
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</div>

</body>
</html>
