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

/* 🗑 ลบสินค้า */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();

    if ($product) {

        if (!empty($product['image'])) {

            $imgPath = "../uploads/products/" . $product['image'];

            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        $del = $conn->prepare("DELETE FROM products WHERE id=?");
        $del->bind_param("i", $id);
        $del->execute();
    }

    header("Location: dashboard.php");
    exit;
}

/* 📊 Summary */
$user_count    = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$product_count = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$order_count   = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$pending       = $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'];

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admin Dashboard</title>

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

/* Stat Cards */
.card-stat{
    border:none;
    border-radius:15px;
    color:white;
    padding:20px;
}

.bg-users{ background:linear-gradient(45deg,#36d1dc,#5b86e5); }
.bg-products{ background:linear-gradient(45deg,#ff9966,#ff5e62); }
.bg-orders{ background:linear-gradient(45deg,#00b09b,#96c93d); }
.bg-pending{ background:linear-gradient(45deg,#f7971e,#ffd200); }

/* Product Card */
.product-card{
    border:none;
    border-radius:15px;
    transition:0.3s;
}

.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 15px 30px rgba(0,0,0,0.15);
}

.product-img{
    height:350px;
    object-fit:cover;
}

/* จำกัด description */
.product-desc{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
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

<h3>
ยินดีต้อนรับ,
<?= htmlspecialchars($_SESSION['admin_name']) ?>
</h3>

<hr>

<!-- Stats -->
<div class="row g-3 mb-4">

<div class="col-lg-3 col-sm-6">
<div class="card-stat bg-users">
<h5><i class="bi bi-people"></i> ผู้ใช้</h5>
<h2><?= $user_count ?></h2>
</div>
</div>

<div class="col-lg-3 col-sm-6">
<div class="card-stat bg-products">
<h5><i class="bi bi-bag"></i> สินค้า</h5>
<h2><?= $product_count ?></h2>
</div>
</div>

<div class="col-lg-3 col-sm-6">
<div class="card-stat bg-orders">
<h5><i class="bi bi-receipt"></i> ออเดอร์</h5>
<h2><?= $order_count ?></h2>
</div>
</div>

<div class="col-lg-3 col-sm-6">
<div class="card-stat bg-pending">
<h5><i class="bi bi-clock"></i> รอตรวจสอบ</h5>
<h2><?= $pending ?></h2>
</div>
</div>

</div>


<!-- Products -->
<h4 class="mb-3">
<i class="bi bi-bag"></i>
สินค้าทั้งหมด
</h4>

<div class="row g-4">

<?php while($p = $products->fetch_assoc()): ?>

<div class="col-xl-3 col-lg-4 col-md-6 col-12">

<div class="card product-card h-100">

<?php if(!empty($p['image']) && file_exists("../uploads/products/".$p['image'])): ?>

<img src="../uploads/products/<?= htmlspecialchars($p['image']) ?>"
class="product-img w-100">

<?php else: ?>

<img src="../uploads/no-image.png"
class="product-img w-100">

<?php endif; ?>

<div class="card-body d-flex flex-column">

<h6 class="fw-bold">
<?= htmlspecialchars($p['name']) ?>
</h6>

<p class="text-muted small product-desc flex-grow-1">
<?= htmlspecialchars($p['description']) ?>
</p>

<h6 class="text-success fw-bold">
<?= number_format($p['price'],2) ?> บาท
</h6>

<a href="dashboard.php?delete=<?= $p['id'] ?>"
class="btn btn-danger btn-sm w-100 mt-2"
onclick="return confirm('ต้องการลบสินค้านี้หรือไม่?')">

<i class="bi bi-trash"></i>
ลบสินค้า

</a>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

</div>

</div>

</div>

</body>
</html>
