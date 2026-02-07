<?php
include("../config/db.php");

/* เช็คล็อกอิน */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* เพิ่มสินค้า */
if (isset($_GET['add'])) {
    $id = $_GET['add'];

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

/* ลบสินค้า */
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ตะกร้าสินค้า</title>
    <style>
        body{font-family:Arial;background:#f4f4f4}
        table{background:#fff;border-collapse:collapse;width:80%;margin:auto}
        th,td{padding:10px;border:1px solid #ddd;text-align:center}
        th{background:#333;color:#fff}
        .btn{padding:8px 15px;text-decoration:none;border-radius:5px;border:none;cursor:pointer}
        .remove{background:red;color:#fff}
        .checkout{background:green;color:#fff}
        textarea{font-family:Arial}
    </style>
</head>
<body>

<h2 align="center">🛒 ตะกร้าสินค้า</h2>
<div align="center">
    <a href="home.php">⬅ เลือกสินค้าต่อ</a>
</div>
<br>

<table>
<tr>
    <th>สินค้า</th>
    <th>ราคา</th>
    <th>จำนวน</th>
    <th>รวม</th>
    <th>จัดการ</th>
</tr>

<?php
$total = 0;

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {

    foreach ($_SESSION['cart'] as $id => $qty) {
        $p = $conn->query("SELECT * FROM products WHERE id='$id'");
        $row = $p->fetch_assoc();

        $sum = $row['price'] * $qty;
        $total += $sum;
?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= $row['price'] ?> บาท</td>
    <td><?= $qty ?></td>
    <td><?= $sum ?> บาท</td>
    <td>
        <a class="btn remove"
           href="?remove=<?= $id ?>"
           onclick="return confirm('ลบสินค้าออกจากตะกร้า?')">
           ลบ
        </a>
    </td>
</tr>
<?php
    }
} else {
    echo "<tr><td colspan='5'>ไม่มีสินค้าในตะกร้า</td></tr>";
}
?>

<tr>
    <th colspan="3">ราคารวมทั้งหมด</th>
    <th colspan="2"><?= $total ?> บาท</th>
</tr>
</table>

<?php if ($total > 0) { ?>
<br><br>

<form method="post" action="checkout_process.php"
      style="width:80%;margin:auto;background:#fff;padding:20px;border-radius:8px">

    <h3>📦 ข้อมูลจัดส่ง</h3>

    <label>ที่อยู่จัดส่ง</label><br>
    <textarea name="address" required style="width:100%;height:80px"></textarea>
    <br><br>

    <label>วิธีชำระเงิน</label><br>
    <input type="radio" name="payment_method" value="cod" required>
    💵 เก็บเงินปลายทาง<br>

    <input type="radio" name="payment_method" value="scan">
    📱 สแกนจ่าย<br><br>

    <button type="submit" class="btn checkout">
        ✅ ยืนยันการสั่งซื้อ
    </button>

</form>
<?php } ?>

</body>
</html>
