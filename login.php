<?php
session_start();
// ถ้า Login ค้างไว้แล้ว ให้ดีดไปหน้า Dashboard เลย
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'student') header("Location: student_dashboard.php");
    else header("Location: admin_dashboard.php");
    exit;
}
///ธรรมศักดิ์ 
// รับค่าจาก QR Code (ถ้ามี)
$redirect_act_id = isset($_GET['redirect_act_id']) ? $_GET['redirect_act_id'] : '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบกิจกรรมนักศึกษา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%); height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card login-card p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Login</h3>
                    <p class="text-muted">ระบบบันทึกการเข้าร่วมกิจกรรม</p>
                </div>
                
                <form action="login_process.php" method="POST">
                    <input type="hidden" name="redirect_act_id" value="<?php echo $redirect_act_id; ?>">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="กรอกชื่อผู้ใช้" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow">เข้าสู่ระบบ</button>
                    
                    <div class="text-center mt-4">
                        <a href="register.php" class="text-decoration-none">ยังไม่มีบัญชี? สมัครสมาชิกที่นี่</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>