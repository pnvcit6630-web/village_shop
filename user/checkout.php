<?php
include("../config/db.php");

// 🔒 กันคนที่ยังไม่ล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// 🔎 ตรวจสอบ order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: cart.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// ================================
// 📌 QR ร้าน (แก้ให้ตรงโครงสร้างจริง)
// ================================
$qrWebPath    = "../uploads/qr/28830.jpg";            // path สำหรับแสดงใน <img>
$qrServerPath = __DIR__ . "/../uploads/qr/28830.jpg"; // path สำหรับเช็กไฟล์

if (!file_exists($qrServerPath)) {
    $qrWebPath = "../uploads/no-image.png";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ชำระเงิน</title>

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    padding:30px;
}
.box{
    background:#fff;
    max-width:500px;
    margin:auto;
    padding:25px;
    border-radius:10px;
    text-align:center;
}
img{
    max-width:300px;
    margin:15px 0;
}
button{
    background:#28a745;
    color:#fff;
    padding:10px 20px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
button:hover{background:#218838}
</style>
</head>
<body>

<div class="box">

<h2>📲 สแกนเพื่อชำระเงิน</h2>

<!-- ✅ QR Code ร้าน -->
<img src="<?= htmlspecialchars($qrWebPath) ?>" alt="QR Code ร้าน">

<p>กรุณาสแกน QR Code เพื่อโอนเงิน</p>

<hr>

<h3>📤 อัปโหลดสลิปการโอนเงิน</h3>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="slip" accept="image/*" required>
    <br><br>
    <button type="submit" name="pay">ยืนยันการชำระเงิน</button>
</form>

</div>

</body>
</html>

<?php
/* 💳 บันทึกสลิป */
if (isset($_POST['pay']) && !empty($_FILES['slip']['name'])) {

    $uploadDir = __DIR__ . "/../uploads/slips/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['slip']['name']);
    $savePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['slip']['tmp_name'], $savePath)) {

        $stmt = $conn->prepare(
            "UPDATE orders SET slip=?, status='pending' WHERE id=?"
        );
        $stmt->bind_param("si", $fileName, $order_id);
        $stmt->execute();

        echo "<script>
            alert('✅ อัปโหลดสลิปเรียบร้อย รอร้านค้าตรวจสอบ');
            window.location='success.php';
        </script>";
    } else {
        echo "<script>alert('❌ อัปโหลดไฟล์ไม่สำเร็จ');</script>";
    }
}
?>
