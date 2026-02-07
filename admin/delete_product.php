<?php
include("../config/db.php");

// 🔒 Admin Guard
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['id'])) {

    $id    = intval($_POST['id']);
    $image = $_POST['image'];

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // ลบไฟล์รูป
    $imgPath = "../uploads/products/" . $image;
    if (!empty($image) && file_exists($imgPath)) {
        unlink($imgPath);
    }

    header("Location: dashboard.php");
    exit;
}
