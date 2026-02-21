<?php
include("config/db.php");

/* ถ้า login แล้ว */
if(isset($_SESSION['user_id'])){
    if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
        header("location:admin/dashboard.php");
    }else{
        header("location:user/home.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบขายของออนไลน์ในหมู่บ้าน</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
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

        .main-card{
            border:none;
            border-radius:20px;

            background:#ffffff;

            box-shadow:0 10px 25px rgba(0,0,0,0.08);

            animation:fadeIn 0.8s ease;
        }

        .logo{
            font-size:60px;
        }

        .btn-custom{
            padding:12px;
            font-size:16px;
            border-radius:10px;
            transition:0.25s;
        }

        .btn-custom:hover{
            transform:translateY(-2px);
            box-shadow:0 6px 15px rgba(0,0,0,0.15);
        }

        .btn-admin{
            background:#374151;
            color:#fff;
        }

        .btn-admin:hover{
            background:#1f2937;
            color:#fff;
        }

        .footer{
            font-size:12px;
            color:#6b7280;
            margin-top:15px;
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

            <div class="card main-card">

                <div class="card-body text-center p-4">

                    <div class="logo mb-3">🛒</div>

                    <h3 class="fw-bold">ระบบขายของออนไลน์</h3>

                    <p class="text-muted mb-4">
                        ภายในหมู่บ้าน
                    </p>

                    <div class="d-grid gap-3">

                        <a href="user/login.php" class="btn btn-primary btn-custom">
                            <i class="bi bi-person-circle"></i>
                            เข้าสู่ระบบผู้ใช้
                        </a>

                        <a href="user/register.php" class="btn btn-success btn-custom">
                            <i class="bi bi-person-plus-fill"></i>
                            สมัครสมาชิก
                        </a>

                        <hr>

                        <a href="admin/login.php" class="btn btn-admin btn-custom">
                            <i class="bi bi-shield-lock-fill"></i>
                            เข้าสู่ระบบแอดมิน
                        </a>

                    </div>

                    <div class="footer">
                        © <?php echo date("Y"); ?> ระบบขายของออนไลน์ในหมู่บ้าน
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>