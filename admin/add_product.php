<?php
require_once("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔒 Admin Guard */
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

/* =====================
   ➕ เพิ่มสินค้า
===================== */
if (isset($_POST['add'])) {

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);

    $uploadDir = "../uploads/products/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imgName = time() . "_" . basename($_FILES['image']['name']);
    $tmp     = $_FILES['image']['tmp_name'];
    $path    = $uploadDir . $imgName;

    $allowed = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {

        $msg = "<div class='alert alert-danger'>❌ อนุญาตเฉพาะ JPG PNG WEBP</div>";

    } elseif (!move_uploaded_file($tmp, $path)) {

        $msg = "<div class='alert alert-danger'>❌ อัปโหลดรูปไม่สำเร็จ</div>";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO products (name, description, price, image)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssds", $name, $description, $price, $imgName);
        $stmt->execute();

        echo "<script>
            alert('✅ เพิ่มสินค้าเรียบร้อย');
            window.location='dashboard.php';
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>เพิ่มสินค้า</title>

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

/* Button */
.btn-save{
    background:#198754;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-save:hover{
    opacity:0.9;
}

/* Image Preview */
.preview-img{
    width:100%;
    height:250px;
    object-fit:cover;
    border-radius:12px;
    border:2px dashed #dee2e6;
}

</style>

</head>
<body>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-lg-2 col-md-3 sidebar p-3">

<h4 class="text-center mb-4">
<i class="bi bi-speedometer2"></i> Admin
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
<i class="bi bi-plus-circle"></i> เพิ่มสินค้าใหม่
</h3>

<?= $msg ?>

<div class="card card-main p-4">

<form method="post" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">ชื่อสินค้า</label>

<input type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">รายละเอียดสินค้า</label>

<textarea
name="description"
class="form-control"
rows="3"
required></textarea>

</div>

<div class="mb-3">

<label class="form-label">ราคา</label>

<input type="number"
name="price"
class="form-control"
step="0.01"
required>

</div>

<div class="mb-3">

<label class="form-label">เลือกรูปสินค้า</label>

<input type="file"
name="image"
class="form-control"
accept="image/*"
required
onchange="previewImage(event)">

</div>

<div class="mb-3 text-center">

<img id="preview"
src="../uploads/no-image.png"
class="preview-img">

</div>

<button class="btn btn-save w-100" name="add">

<i class="bi bi-save"></i>
เพิ่มสินค้า

</button>

</form>

</div>

</div>
</div>
</div>

<script>

function previewImage(event){

    const reader = new FileReader();

    reader.onload = function(){
        document.getElementById('preview').src = reader.result;
    }

    reader.readAsDataURL(event.target.files[0]);
}

</script>

</body>
</html>