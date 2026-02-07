<?php
include("../config/db.php");

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $pass  = $_POST['password']; // รหัสผ่านแบบธรรมดา

    // ดึงเฉพาะ admin
    $stmt = $conn->prepare(
        "SELECT id, name, password 
         FROM users 
         WHERE email = ? AND role = 'admin'
         LIMIT 1"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows === 1){
        $u = $result->fetch_assoc();

        // เทียบรหัสผ่านตรง ๆ
        if($pass === $u['password']){
            $_SESSION['admin_id']   = $u['id'];
            $_SESSION['admin_name'] = $u['name'];
            $_SESSION['role']       = 'admin';

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบบัญชีแอดมิน";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
    body{
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg,#74ebd5,#9face6);
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-box{
        background:#fff;
        width:320px;
        padding:25px;
        border-radius:10px;
        box-shadow:0 10px 25px rgba(0,0,0,.15);
    }
    input, button, a{
        width:100%;
        padding:10px;
        margin-bottom:12px;
        border-radius:6px;
        box-sizing:border-box;
        display:block;
        text-align:center;
    }
    button{
        background:#dc3545;
        color:#fff;
        border:none;
        cursor:pointer;
    }
    button:hover{
        background:#c82333;
    }
    .back{
        background:#6c757d;
        color:#fff;
        text-decoration:none;
    }
    .error{
        color:red;
        text-align:center;
        margin-bottom:10px;
    }
</style>
</head>
<body>

<div class="login-box">

<h3 style="text-align:center;">🔐 Admin Login</h3>

<?php if($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <input type="email" name="email" placeholder="อีเมล" required>
    <input type="password" name="password" placeholder="รหัสผ่าน" required>
    <button type="submit" name="login">เข้าสู่ระบบ</button>
</form>

<a href="../index.php" class="back">⬅ กลับหน้าแรก</a>

</div>

</body>
</html>
