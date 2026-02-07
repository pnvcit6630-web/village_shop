<?php
include("../config/db.php"); // มี session_start() แล้ว

// 🔒 กันคนที่ยังไม่ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$userName = $_SESSION['user_name'] ?? 'ผู้ใช้';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>หน้าหลัก | สินค้า</title>

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
}
.top{
    margin-bottom:20px;
}
.product{
    border:1px solid #ccc;
    padding:15px;
    width:220px;
    display:inline-block;
    margin:10px;
    text-align:center;
    background:#fff;
    border-radius:8px;
}
.product img{
    width:150px;
    height:150px;
    object-fit:cover;
    margin-bottom:10px;
}
.btn{
    display:inline-block;
    margin-top:5px;
    padding:6px 12px;
    background:#28a745;
    color:#fff;
    text-decoration:none;
    border-radius:5px;
}
.order{ background:#007bff; }
.logout{ background:#dc3545; }
.desc{
    font-size:14px;
    color:#555;
    margin:8px 0;
}
</style>
</head>
<body>

<!-- 🔝 เมนู -->
<div class="top">
    👤 <?= htmlspecialchars($userName) ?> |
    <a href="orders.php" class="btn order">🧾 ออเดอร์ของฉัน</a>
    <a href="cart.php" class="btn">🛒 ตะกร้าสินค้า</a>
    <a href="../logout.php" class="btn logout"
       onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?');">
       🚪 ออกจากระบบ
    </a>
</div>

<hr>

<!-- 🛍 แสดงสินค้า -->
<?php
$sql = "SELECT id, name, price, image, description FROM products ORDER BY id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()):

    // ✅ path รูป ต้องตรงกับ admin
    $imgPath = "../uploads/products/" . $row['image'];

    if (empty($row['image']) || !file_exists($imgPath)) {
        $imgPath = "../uploads/no-image.png";
    }
?>
<div class="product">

    <img src="<?= htmlspecialchars($imgPath) ?>" alt="product">

    <h3><?= htmlspecialchars($row['name']) ?></h3>

    <div class="desc">
        <?= nl2br(htmlspecialchars($row['description'])) ?>
    </div>

    <p><strong><?= number_format($row['price']) ?></strong> บาท</p>

    <a href="cart.php?add=<?= $row['id'] ?>" class="btn">
        ➕ ใส่ตะกร้า
    </a>

</div>
<?php endwhile; ?>

</body>
</html>
