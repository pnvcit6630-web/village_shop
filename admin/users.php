<?php
require_once("../config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

/* =====================
   ➕ เพิ่มผู้ใช้
===================== */
if (isset($_POST['add_user'])) {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
    $chk->bind_param("s", $email);
    $chk->execute();
    $chk->store_result();

    if ($chk->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>❌ อีเมลนี้ถูกใช้งานแล้ว</div>";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO users (name,email,password,role)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssss", $name, $email, $pass, $role);
        $stmt->execute();

        $msg = "<div class='alert alert-success'>✅ เพิ่มผู้ใช้เรียบร้อย</div>";
    }
}

/* =====================
   ✏️ แก้ไข
===================== */
if (isset($_POST['edit_user'])) {

    $id   = intval($_POST['id']);
    $name = trim($_POST['name']);
    $role = $_POST['role'];

    $stmt = $conn->prepare(
        "UPDATE users SET name=?, role=? WHERE id=?"
    );

    $stmt->bind_param("ssi", $name, $role, $id);
    $stmt->execute();

    $msg = "<div class='alert alert-success'>✏️ แก้ไขเรียบร้อย</div>";
}

/* =====================
   🗑 ลบ
===================== */
if (isset($_GET['delete'])) {

    $del_id = intval($_GET['delete']);

    if ($del_id == $_SESSION['admin_id']) {
        $msg = "<div class='alert alert-danger'>❌ ไม่สามารถลบตัวเองได้</div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $msg = "<div class='alert alert-warning'>🗑 ลบผู้ใช้เรียบร้อย</div>";
    }
}

$users = $conn->query(
    "SELECT id,name,email,role FROM users ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>จัดการผู้ใช้</title>

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

/* Table */
.table thead{
    background:#667eea;
    color:white;
}

.table tbody tr:hover{
    background:#f8f9ff;
}

/* Buttons */
.btn-save{
    background:#198754;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-delete{
    background:#dc3545;
    border:none;
    border-radius:20px;
    color:white;
}

.btn-save:hover,
.btn-delete:hover{
    opacity:0.9;
}

/* Header */
.header-title{
    font-weight:bold;
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
<i class="bi bi-people"></i> จัดการผู้ใช้
</h3>

<?= $msg ?>

<div class="card card-main p-4 mb-4">

<h5 class="mb-3">
<i class="bi bi-person-plus"></i> เพิ่มผู้ใช้ใหม่
</h5>

<form method="post">
<div class="row g-2">

<div class="col-md-3">
<input type="text" name="name" class="form-control" placeholder="ชื่อ" required>
</div>

<div class="col-md-3">
<input type="email" name="email" class="form-control" placeholder="Email" required>
</div>

<div class="col-md-3">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>

<div class="col-md-2">
<select name="role" class="form-select">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>
</div>

<div class="col-md-1 d-grid">
<button name="add_user" class="btn btn-save">
<i class="bi bi-save"></i>
</button>
</div>

</div>
</form>

</div>

<div class="card card-main p-3">
<div class="table-responsive">

<table class="table table-hover align-middle text-center">

<thead>
<tr>
<th>ID</th>
<th>ชื่อ</th>
<th>Email</th>
<th>สิทธิ์</th>
<th>จัดการ</th>
</tr>
</thead>

<tbody>

<?php while($u = $users->fetch_assoc()): ?>
<tr>
<form method="post">

<td><?= $u['id'] ?></td>

<td>
<input type="text"
name="name"
value="<?= htmlspecialchars($u['name']) ?>"
class="form-control">
</td>

<td><?= htmlspecialchars($u['email']) ?></td>

<td>
<select name="role" class="form-select">
<option value="user" <?= $u['role']=="user"?"selected":"" ?>>User</option>
<option value="admin" <?= $u['role']=="admin"?"selected":"" ?>>Admin</option>
</select>
</td>

<td>
<input type="hidden" name="id" value="<?= $u['id'] ?>">

<button name="edit_user"
class="btn btn-save btn-sm mb-1">
<i class="bi bi-save"></i>
</button>

<?php if($u['id'] != $_SESSION['admin_id']): ?>
<a href="?delete=<?= $u['id'] ?>"
class="btn btn-delete btn-sm"
onclick="return confirm('ลบผู้ใช้นี้?')">
<i class="bi bi-trash"></i>
</a>
<?php endif; ?>

</td>

</form>
</tr>
<?php endwhile; ?>

</tbody>

</table>

</div>
</div>

</div>
</div>
</div>

</body>
</html>