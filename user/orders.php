<?php
include("../config/db.php");

/* 🔒 ตรวจสอบล็อกอิน */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ❌ ยกเลิกออเดอร์ */
if (isset($_POST['cancel_order'], $_POST['order_id'])) {

    $oid = intval($_POST['order_id']);

    // ยกเลิกได้เฉพาะของตัวเอง + ยังไม่จ่าย
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
<title>ออเดอร์ของฉัน</title>

<style>
body{
    font-family:Arial;
    background:#f4f4f4
}
table{
    background:#fff;
    border-collapse:collapse;
    width:95%;
    margin:auto
}
th,td{
    padding:10px;
    border:1px solid #ddd;
    text-align:center
}
th{
    background:#333;
    color:#fff
}
.paid{color:green;font-weight:bold}
.pending{color:orange;font-weight:bold}
.cancelled{color:red;font-weight:bold}
a{color:#1976d2;text-decoration:none}
a:hover{text-decoration:underline}
button{
    padding:6px 10px;
    border:none;
    border-radius:4px;
    cursor:pointer
}
.btn-cancel{
    background:#dc3545;
    color:#fff
}
.btn-cancel:hover{background:#b02a37}
</style>
</head>
<body>

<h2 align="center">🧾 ออเดอร์ของฉัน</h2>

<div align="center">
    <a href="home.php">🏠 หน้าแรก</a> |
    <a href="cart.php">🛒 ตะกร้า</a>
</div>

<br>

<table>
<tr>
    <th>ID</th>
    <th>วันที่</th>
    <th>ยอดรวม</th>
    <th>ที่อยู่จัดส่ง</th>
    <th>สลิป</th>
    <th>สถานะ</th>
    <th>จัดการ</th>
</tr>

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

if ($result->num_rows > 0):
    while ($o = $result->fetch_assoc()):

        $slipWebPath    = "../uploads/slips/" . $o['slip'];
        $slipServerPath = __DIR__ . "/../uploads/slips/" . $o['slip'];
?>
<tr>
    <td><?= $o['id'] ?></td>
    <td><?= $o['created_at'] ?></td>
    <td><?= number_format($o['total']) ?> บาท</td>
    <td><?= htmlspecialchars($o['address']) ?></td>

    <td>
        <?php if (!empty($o['slip']) && file_exists($slipServerPath)): ?>
            <a href="<?= htmlspecialchars($slipWebPath) ?>" target="_blank">📄 ดูสลิป</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td class="<?= htmlspecialchars($o['status']) ?>">
        <?php
            if ($o['status'] === 'paid') echo "✅ ชำระแล้ว";
            elseif ($o['status'] === 'cancelled') echo "❌ ยกเลิกแล้ว";
            else echo "⏳ รอตรวจสอบ";
        ?>
    </td>

    <td>
        <?php if ($o['status'] === 'pending'): ?>
            <form method="post" onsubmit="return confirm('ต้องการยกเลิกออเดอร์นี้หรือไม่?');">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <button type="submit" name="cancel_order" class="btn-cancel">
                    ❌ ยกเลิก
                </button>
            </form>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
    <td colspan="7">ยังไม่มีออเดอร์</td>
</tr>
<?php endif; ?>

</table>

</body>
</html>
