<?php
include("../config/db.php");

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($email == "" || $password == ""){
        $error = "กรุณากรอกข้อมูลให้ครบ";
    }else{

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $user = $result->fetch_assoc();

            if(password_verify($password,$user['password'])){

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = "user";

                header("location:home.php");
                exit;

            }else{
                $error = "รหัสผ่านไม่ถูกต้อง";
            }

        }else{
            $error = "ไม่พบอีเมลนี้ในระบบ";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<title>เข้าสู่ระบบ - ระบบขายของออนไลน์</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    height:100vh;

    /* พื้นหลังสบายตา */
    background: linear-gradient(
        135deg,
        #f8fafc,
        #e0f2fe,
        #d1fae5
    );

    display:flex;
    align-items:center;
    justify-content:center;

    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.login-card{
    border:none;
    border-radius:20px;
    background:#ffffff;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    animation:fadeIn .6s ease;
}

.logo{
    font-size:55px;
}

.form-control{
    padding:12px;
    border-radius:10px;
    border:1px solid #e5e7eb;
    transition:.2s;
}

.form-control:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,0.15);
}

.input-group-text{
    background:#f1f5f9;
    border-radius:10px 0 0 10px;
    border:1px solid #e5e7eb;
}

.btn-custom{
    padding:12px;
    border-radius:10px;
    font-size:16px;
    transition:.25s;
}

.btn-primary{
    background:#38bdf8;
    border:none;
}

.btn-primary:hover{
    background:#0ea5e9;
    transform:translateY(-2px);
    box-shadow:0 6px 15px rgba(0,0,0,.1);
}

.btn-back{
    background:#94a3b8;
    color:#fff;
    border:none;
}

.btn-back:hover{
    background:#64748b;
    color:#fff;
    transform:translateY(-2px);
}

.footer{
    font-size:12px;
    color:#6b7280;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

</head>
<body>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

<div class="card login-card">
<div class="card-body p-4">

<div class="text-center mb-3">
<div class="logo">🛒</div>
<h4 class="fw-bold">เข้าสู่ระบบ</h4>
<p class="text-muted">
ระบบขายของออนไลน์ในหมู่บ้าน
</p>
</div>

<?php if($error!=""){ ?>
<div class="alert alert-danger text-center">
<i class="bi bi-exclamation-triangle-fill"></i>
<?php echo $error; ?>
</div>
<?php } ?>

<form method="post">

<div class="mb-3">
<div class="input-group">
<span class="input-group-text">
<i class="bi bi-envelope-fill"></i>
</span>
<input 
type="email" 
name="email" 
class="form-control"
placeholder="กรอกอีเมล"
required
>
</div>
</div>

<div class="mb-3">
<div class="input-group">
<span class="input-group-text">
<i class="bi bi-lock-fill"></i>
</span>
<input 
type="password" 
name="password" 
class="form-control"
placeholder="กรอกรหัสผ่าน"
required
>
</div>
</div>

<div class="d-grid mb-3">
<button name="login" class="btn btn-primary btn-custom">
<i class="bi bi-box-arrow-in-right"></i>
เข้าสู่ระบบ
</button>
</div>

</form>

<div class="d-grid mb-2">
<a href="../index.php" class="btn btn-back btn-custom">
<i class="bi bi-arrow-left"></i>
กลับหน้าแรก
</a>
</div>

<div class="text-center">
<a href="register.php" class="text-decoration-none">
ยังไม่มีบัญชี? สมัครสมาชิก
</a>
</div>

<hr>

<div class="text-center footer">
© <?php echo date("Y"); ?> ระบบขายของออนไลน์
</div>

</div>
</div>

</div>
</div>
</div>

</body>
</html>