<?php
session_start();

/* ลบข้อมูล session ทั้งหมด */
session_unset();
session_destroy();

/* กลับไปหน้าแรก */
header("Location: index.php");
exit;
?>
