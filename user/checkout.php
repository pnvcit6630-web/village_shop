<?php
include("../config/db.php");

/* 🔒 ตรวจสอบล็อกอิน */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

/* 🔎 ตรวจสอบ order_id */
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$user_id  = $_SESSION['user_id'];

/* 🔒 ตรวจสอบว่า order เป็นของ user จริง */
$stmt = $conn->prepare("
    SELECT id
    FROM orders
    WHERE id=? AND user_id=?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: orders.php");
    exit;
}

/* ============================
   QR Code ร้าน
============================ */
$qrWebPath    = "../uploads/qr/28830.jpg";
$qrServerPath = __DIR__ . "/../uploads/qr/28830.jpg";

if (!file_exists($qrServerPath)) {
    $qrWebPath = "../uploads/no-image.png";
}

/* ============================
   Upload Slip
============================ */
$error = "";

if (isset($_POST['pay']) && isset($_FILES['slip'])) {

    $uploadDir = __DIR__ . "/../uploads/slips/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmp = $_FILES['slip']['tmp_name'];

    if (empty($fileTmp)) {

        $error = "กรุณาเลือกไฟล์สลิป";

    } else {

        $originalName = basename($_FILES['slip']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {

            $error = "อนุญาตเฉพาะ JPG, PNG, WEBP";

        } else {

            $fileName = "slip_" . $order_id . "_" . time() . "." . $ext;
            $savePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmp, $savePath)) {

                /* update database */
                $stmt = $conn->prepare("
                    UPDATE orders
                    SET slip=?, status='pending'
                    WHERE id=? AND user_id=?
                ");

                $stmt->bind_param("sii", $fileName, $order_id, $user_id);
                $stmt->execute();

                header("Location: success.php?order_id=".$order_id);
                exit;

            } else {

                $error = "อัปโหลดไม่สำเร็จ";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<title>ชำระเงิน</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
    font-family:'Segoe UI',sans-serif;
}

/* Navbar */
.navbar-custom{
    background:#ffffff;
    border-bottom:1px solid #dee2e6;
}

/* Card */
.main-card{
    border:none;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

/* QR image */
.qr-img{
    max-width:220px;
    border-radius:12px;
    border:1px solid #dee2e6;
    padding:5px;
    background:#fff;
}

/* Button */
.btn-custom{
    border-radius:8px;
}

/* Upload box */
.upload-box{
    border:2px dashed #dee2e6;
    border-radius:10px;
    padding:15px;
    background:#fafafa;
}

</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-light navbar-custom">

<div class="container">

<a href="home.php" class="navbar-brand fw-bold text-success">

<i class="bi bi-shop"></i>
ระบบขายของออนไลน์

</a>

<a href="orders.php" class="btn btn-outline-primary btn-sm">

<i class="bi bi-receipt"></i>
ออเดอร์ของฉัน

</a>

</div>
</nav>


<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card main-card">

<div class="card-body text-center">

<h4 class="mb-3">

<i class="bi bi-qr-code text-success"></i>
ชำระเงินด้วย QR Code

</h4>


<img src="<?= htmlspecialchars($qrWebPath) ?>"
class="qr-img mb-3">


<p class="text-muted">

สแกน QR Code แล้วอัปโหลดสลิปเพื่อยืนยันการชำระเงิน

</p>


<?php if($error): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<form method="post" enctype="multipart/form-data">

<div class="mb-3 text-start upload-box">

<label class="form-label">

อัปโหลดสลิป

</label>

<input type="file"
name="slip"
class="form-control"
accept="image/*"
required>

</div>


<button type="submit"
name="pay"
class="btn btn-success w-100 btn-custom">

<i class="bi bi-upload"></i>
ยืนยันการชำระเงิน

</button>

</form>


<hr>


<a href="orders.php"
class="btn btn-outline-secondary w-100 btn-custom">

<i class="bi bi-arrow-left"></i>
กลับไปหน้าออเดอร์

</a>


</div>
</div>

</div>
</div>
</div>

</body>
</html>