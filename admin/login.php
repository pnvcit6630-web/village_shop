<?php 
include("../config/db.php"); 

$error = ""; 

if(isset($_POST['login'])){ 

    $email = trim($_POST['email']); 
    $pass  = trim($_POST['password']); 

    if(empty($email) || empty($pass)){
        $error = "กรุณากรอกข้อมูลให้ครบ";
    }else{

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

            // (แนะนำให้ใช้ password_verify ในอนาคต)
            if($pass === $u['password']){

                $_SESSION['admin_id']   = $u['id'];
                $_SESSION['admin_name'] = $u['name'];
                $_SESSION['role']       = 'admin';

                header("Location: dashboard.php");
                exit;

            }else{
                $error = "รหัสผ่านไม่ถูกต้อง";
            }

        }else{
            $error = "ไม่พบบัญชีแอดมิน";
        }
    }
}
?> 

<!DOCTYPE html> 
<html lang="th"> 
<head> 

<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 

<title>เข้าสู่ระบบแอดมิน</title> 

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> 

<style> 

/* พื้นหลังขาวฟ้า */
body{
    background: linear-gradient(to bottom, #ffffff, #e3f2fd);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}

/* การ์ด */
.login-card{

    width:100%;
    max-width:400px;

    border-radius:18px;
    border:none;

    box-shadow:0 8px 25px rgba(0,0,0,0.1);

    animation:fadeIn .5s ease;

    background:#ffffff;
}

/* โลโก้ */
.logo{

    font-size:55px;
    color:#0d6efd;

}

/* ปุ่ม login */
.btn-login{

    background:#0d6efd;
    border:none;
    border-radius:10px;
    padding:12px;

    font-weight:bold;
    color:white;
}

.btn-login:hover{

    background:#0b5ed7;

}

/* ปุ่ม back */
.btn-back{

    border-radius:10px;
}

/* input */
.form-control{

    border-radius:10px;
    padding:12px;

}

.input-group-text{

    border-radius:10px 0 0 10px;

}

/* animation */
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


<div class="card login-card"> 

<div class="card-body p-4"> 


<div class="text-center mb-3"> 

<div class="logo"> 
<i class="bi bi-shield-lock-fill"></i> 
</div> 

<h4 class="fw-bold text-primary"> 
Admin Panel 
</h4> 

<p class="text-muted"> 
เข้าสู่ระบบผู้ดูแลระบบ 
</p> 

</div> 


<?php if($error): ?> 

<div class="alert alert-danger text-center"> 
<i class="bi bi-exclamation-triangle"></i> 
<?= htmlspecialchars($error) ?> 
</div> 

<?php endif; ?> 


<form method="post"> 


<div class="mb-3"> 

<label class="form-label">
อีเมล
</label>

<div class="input-group">

<span class="input-group-text">
<i class="bi bi-envelope"></i>
</span>

<input type="email"
name="email"
class="form-control"
placeholder="admin@email.com"
required>

</div>

</div>


<div class="mb-3">

<label class="form-label">
รหัสผ่าน
</label>

<div class="input-group">

<span class="input-group-text">
<i class="bi bi-lock"></i>
</span>

<input type="password"
name="password"
class="form-control"
placeholder="รหัสผ่าน"
required>

</div>

</div>


<button type="submit"
name="login"
class="btn btn-login w-100">

<i class="bi bi-box-arrow-in-right"></i>
เข้าสู่ระบบ

</button>


</form>


<hr>


<a href="../index.php"
class="btn btn-outline-secondary btn-back w-100">

<i class="bi bi-arrow-left"></i>
กลับหน้าแรก

</a>


</div>
</div>


</body> 
</html>