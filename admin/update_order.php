<?php
include("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$id = $_GET['id'];

$conn->query("
    UPDATE orders 
    SET status='paid'
    WHERE id='$id'
");

header("Location: orders.php");
exit;
