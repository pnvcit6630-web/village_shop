<?php
include("../config/db.php"); // มี session_start() แล้ว

// 🔒 Admin Guard
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เพิ่มสินค้า</title>

<style>
body{
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg,#74ebd5,#9face6);
    height:100vh;
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
}
.box{
    background:#fff;
    width:400px;
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}
h2{text-align:center;margin-bottom:20px}
input, textarea, button, a{
    width:100%;
    padding:10px;
    margin-bottom:12px;
    border-radius:6px;
    box-sizing:border-box;
    font-size:14px;
}
input, textarea{border:1px solid #ccc}
textarea{resize:none;height:90px}
button{
    background:#28a745;
    color:#fff;
    border:none;
    cursor:pointer;
    font-size:16px;
}
button:hover{background:#218838}
a{
    background:#6c757d;
    color:#fff;
    text-decoration:none;
    text-align:center;
}
a:hover{background:#5a6268}
</style>
</head>
<body>

<div class="box">
<h2>➕ เพิ่มสินค้า</h2>

<form method="post" enctype="multipart/form-data">
    <input name="name" placeholder="ชื่อสินค้า" required>
    <textarea name="description" placeholder="รายละเอียดสินค้า" required></textarea>
    <input name="price" placeholder="ราคา" required>
    <input type="file" name="image" accept="image/*" required>
    <button name="add">เพิ่มสินค้า</button>
</form>

<a href="dashboard.php">⬅ กลับแดชบอร์ด</a>
</div>

</body>
</html>

<?php
if (isset($_POST['add'])) {

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);

    /* 📁 โฟลเดอร์รูป */
    $uploadDir = "../uploads/products/";

    // ถ้าโฟลเดอร์ไม่มี ให้สร้าง
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /* 📸 อัปโหลดรูป */
    $imgName = time() . "_" . basename($_FILES['image']['name']);
    $tmp     = $_FILES['image']['tmp_name'];
    $path    = $uploadDir . $imgName;

    if (!move_uploaded_file($tmp, $path)) {
        die("❌ อัปโหลดรูปไม่สำเร็จ");
    }

    /* 💾 บันทึกลงฐานข้อมูล */
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
}
?>
