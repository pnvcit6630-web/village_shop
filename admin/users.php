<?php
include("../config/db.php");


// 🔒 Admin Guard
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
        $msg = "❌ อีเมลนี้ถูกใช้งานแล้ว";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO users (name,email,password,role)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssss", $name, $email, $pass, $role);
        $stmt->execute();
        $msg = "✅ เพิ่มผู้ใช้เรียบร้อยแล้ว";
    }
}

/* =====================
   ✏️ แก้ไขผู้ใช้
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

    $msg = "✏️ แก้ไขข้อมูลผู้ใช้เรียบร้อย";
}

/* =====================
   🗑 ลบผู้ใช้
===================== */
if (isset($_GET['delete'])) {

    $del_id = intval($_GET['delete']);

    if ($del_id == $_SESSION['admin_id']) {
        $msg = "❌ ไม่สามารถลบตัวเองได้";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $msg = "🗑 ลบผู้ใช้เรียบร้อย";
    }
}

/* 👤 ดึงรายชื่อผู้ใช้ */
$users = $conn->query("SELECT id,name,email,role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการผู้ใช้</title>

<style>
body{font-family:Arial;background:#f4f4f4;padding:20px}
.box{background:#fff;padding:20px;border-radius:8px;margin-bottom:20px}
input,select{padding:6px}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #ccc;padding:8px;text-align:center}
th{background:#eee}
.role-admin{color:#d32f2f;font-weight:bold}
.role-user{color:#2e7d32;font-weight:bold}
.msg{margin-bottom:15px;font-weight:bold}

.menu a{
    background:#1976d2;color:#fff;padding:8px 12px;
    border-radius:5px;margin-right:5px;text-decoration:none
}

.btn{
    padding:5px 10px;
    border-radius:5px;
    border:none;
    cursor:pointer;
    color:#fff;
    font-size:13px
}
.edit{background:#4caf50}
.del{background:#f44336}
.edit:hover{background:#388e3c}
.del:hover{background:#d32f2f}
</style>
</head>
<body>

<h2>👤 จัดการผู้ใช้</h2>

<div class="menu">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="../logout.php">🚪 ออกจากระบบ</a>
</div>

<br>

<?php if($msg): ?>
    <div class="msg"><?= $msg ?></div>
<?php endif; ?>

<!-- ➕ เพิ่มผู้ใช้ -->
<div class="box">
<h3>➕ เพิ่มผู้ใช้</h3>
<form method="post">
    <input type="text" name="name" placeholder="ชื่อผู้ใช้" required>
    <input type="email" name="email" placeholder="อีเมล" required>
    <input type="password" name="password" placeholder="รหัสผ่าน" required>
    <select name="role">
        <option value="user">ผู้ใช้</option>
        <option value="admin">แอดมิน</option>
    </select>
    <br><br>
    <button class="btn edit" name="add_user">บันทึกผู้ใช้</button>
</form>
</div>

<!-- 📋 ตารางผู้ใช้ -->
<div class="box">
<h3>📋 รายชื่อผู้ใช้</h3>

<table>
<tr>
    <th>ID</th>
    <th>ชื่อ</th>
    <th>Email</th>
    <th>สิทธิ์</th>
    <th>จัดการ</th>
</tr>

<?php while($u = $users->fetch_assoc()): ?>
<tr>
<form method="post">
    <td><?= $u['id'] ?></td>

    <td>
        <input type="text" name="name"
               value="<?= htmlspecialchars($u['name']) ?>" required>
    </td>

    <td><?= htmlspecialchars($u['email']) ?></td>

    <td>
        <select name="role">
            <option value="user" <?= $u['role']=='user'?'selected':'' ?>>user</option>
            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>admin</option>
        </select>
    </td>

    <td>
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button class="btn edit" name="edit_user">💾 แก้ไข</button>

        <?php if($u['id'] != $_SESSION['admin_id']): ?>
            <a class="btn del"
               href="?delete=<?= $u['id'] ?>"
               onclick="return confirm('ลบผู้ใช้นี้ใช่หรือไม่?')">
               🗑 ลบ
            </a>
        <?php endif; ?>
    </td>
</form>
</tr>
<?php endwhile; ?>
</table>
</div>

</body>
</html>
