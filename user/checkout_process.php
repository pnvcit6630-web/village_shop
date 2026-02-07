<?php
include("../config/db.php");
session_start();

/* เช็คล็อกอิน */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* เช็คตะกร้า */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method']; // cod | scan

$total = 0;

/* คำนวณราคารวม */
foreach ($_SESSION['cart'] as $id => $qty) {
    $p = $conn->query("SELECT price FROM products WHERE id='$id'");
    $row = $p->fetch_assoc();
    $total += $row['price'] * $qty;
}

/* COD ไม่มีสลิป */
$slip = null;

/* บันทึกออเดอร์ */
$sql = "INSERT INTO orders (user_id, address, total, payment_method, status)
        VALUES (?,?,?,?, 'pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $user_id, $address, $total, $payment_method);
$stmt->execute();

$order_id = $stmt->insert_id;

/* ล้างตะกร้า */
unset($_SESSION['cart']);

/* 🔀 redirect ตามวิธีชำระเงิน */
if ($payment_method === 'scan') {
    header("Location: checkout.php?order_id=" . $order_id);
    exit;
} else {
    header("Location: success.php");
    exit;
}
