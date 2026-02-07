<?php include("../config/db.php"); ?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สมัครสมาชิก</title>
<style>
    body{
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg,#ffecd2,#fcb69f);
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .register-box{
        background:#fff;
        width:340px;
        padding:25px;
        border-radius:10px;
        box-shadow:0 10px 25px rgba(0,0,0,.15);
    }
    h2{
        text-align:center;
        margin-bottom:20px;
    }
    input, button, a{
        width:100%;
        padding:10px;
        margin-bottom:12px;
        border-radius:6px;
        box-sizing:border-box;
        display:block;
    }
    input{
        border:1px solid #ccc;
    }
    button{
        background:#28a745;
        color:#fff;
        border:none;
        cursor:pointer;
    }
    button:hover{
        background:#218838;
    }
    .back{
        background:#6c757d;
        color:#fff;
        text-decoration:none;
        text-align:center;
    }
    .back:hover{
        background:#5a6268;
    }
</style>
</head>
<body>

<div class="register-box">
    <h2>📝 สมัครสมาชิก</h2>

    <form method="post">
        <input name="name" placeholder="ชื่อ">
        <input name="email" placeholder="อีเมล">
        <input name="password" type="password" placeholder="รหัสผ่าน">
        <button name="register">สมัคร</button>
    </form>

    <!-- 🔙 ปุ่มย้อนกลับหน้า index หลัก -->
    <a href="../index.php" class="back">⬅ กลับหน้าแรก</a>
</div>

</body>
</html>

<?php
if(isset($_POST['register'])){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users(name,email,password) 
    VALUES('{$_POST['name']}','{$_POST['email']}','$pass')");

    // เด้งไปหน้าเข้าสู่ระบบ
    header("Location: login.php");
    exit;
}
?>
