<?php
include("../config/db.php");

// 🔒 Admin Guard
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* 🗑 ลบสินค้า */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    // ดึงชื่อรูปก่อน
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();

    if ($product) {

        // ลบไฟล์รูป
        if (!empty($product['image'])) {
            $imgPath = "../uploads/products/" . $product['image'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        // ลบข้อมูลใน DB
        $del = $conn->prepare("DELETE FROM products WHERE id=?");
        $del->bind_param("i", $id);
        $del->execute();
    }

    // รีเฟรชหน้า
    header("Location: dashboard.php");
    exit;
}

/* 📊 สรุป */
$user_count    = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$product_count = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$order_count   = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$pending       = $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'];

/* 🛒 สินค้า */
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
body{
    font-family:Arial;
    background:#f4f4f4;
    padding:20px
}
.box{
    background:#fff;
    padding:20px;
    border-radius:8px;
    text-align:center
}
.grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:10px;
    margin-bottom:20px
}
.menu a{
    background:#1976d2;
    color:#fff;
    padding:10px 15px;
    border-radius:5px;
    margin-right:5px;
    text-decoration:none
}
.menu a:hover{background:#0d47a1}

/* 🛒 สินค้า */
.products{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(230px,1fr));
    gap:15px;
}
.card{
    background:#fff;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
    overflow:hidden;
}
.card img{
    width:100%;
    height:180px;
    object-fit:cover;
}
.card .info{
    padding:12px;
}
.card h4{
    margin:0 0 5px 0;
}
.card p{
    font-size:13px;
    color:#555;
    height:40px;
    overflow:hidden;
}
.price{
    font-weight:bold;
    color:#2e7d32;
}
.delete-btn{
    display:inline-block;
    margin-top:8px;
    background:#e53935;
    color:#fff;
    padding:6px 10px;
    border-radius:5px;
    font-size:13px;
    text-decoration:none;
}
.delete-btn:hover{background:#c62828}
</style>
</head>
<body>

<h2>📊 Admin Dashboard</h2>
<p>ยินดีต้อนรับ <?= htmlspecialchars($_SESSION['admin_name']) ?></p>

<div class="menu">
    <a href="dashboard.php">🏠 หน้าแรก</a>
    <a href="orders.php">📦 ออเดอร์</a>
    <a href="users.php">👤 ผู้ใช้</a>
    <a href="add_product.php">➕ เพิ่มสินค้า</a>
    <a href="../logout.php">🚪 ออกจากระบบ</a>
</div>

<br>

<div class="grid">
    <div class="box">👤 ผู้ใช้ทั้งหมด<br><b><?= $user_count ?></b></div>
    <div class="box">📦 สินค้า<br><b><?= $product_count ?></b></div>
    <div class="box">🧾 ออเดอร์<br><b><?= $order_count ?></b></div>
    <div class="box">⏳ รอตรวจสอบ<br><b><?= $pending ?></b></div>
</div>

<h3>🛒 สินค้าที่เพิ่มล่าสุด</h3>

<div class="products">
<?php while($p = $products->fetch_assoc()): ?>
    <div class="card">

        <?php if(!empty($p['image']) && file_exists("../uploads/products/".$p['image'])): ?>
            <img src="../uploads/products/<?= htmlspecialchars($p['image']) ?>" alt="สินค้า">
        <?php else: ?>
            <img src="../uploads/no-image.png" alt="no image">
        <?php endif; ?>

        <div class="info">
            <h4><?= htmlspecialchars($p['name']) ?></h4>
            <p><?= htmlspecialchars($p['description']) ?></p>

            <div class="price">
                <?= number_format($p['price'],2) ?> บาท
            </div>

            <!-- 🗑 ปุ่มลบสินค้า -->
            <a class="delete-btn"
               href="dashboard.php?delete=<?= $p['id'] ?>"
               onclick="return confirm('⚠️ ต้องการลบสินค้านี้จริงหรือไม่?');">
               🗑 ลบสินค้า
            </a>
        </div>

    </div>
<?php endwhile; ?>
</div>

</body>
</html>
