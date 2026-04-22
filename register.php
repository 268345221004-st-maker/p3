<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สมัครสมาชิก - Activity System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --univ-navy: #1a237e;
            --univ-gold: #ffd600;
            --univ-navy-dark: #0d145a;
        }

        body { 
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, var(--univ-navy) 0%, var(--univ-navy-dark) 100%);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px 20px;
        }

        .card { 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            border: none;
            overflow: hidden;
            width: 100%;
            max-width: 700px;
        }

        .card::before {
            content: "";
            height: 5px;
            background: var(--univ-gold);
            display: block;
        }

        .btn-register {
            background: var(--univ-navy);
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            border-radius: 50px;
            transition: 0.3s;
            font-weight: 600;
        }

        .btn-register:hover {
            background: var(--univ-navy-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            background-color: #f8f9fa;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.2);
            border-color: var(--univ-navy);
        }

        .header-title { color: var(--univ-navy); font-weight: 600; }
        .text-navy { color: var(--univ-navy) !important; }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-2">
                    <i class="bi bi-person-plus-fill" style="font-size: 3rem; color: var(--univ-navy);"></i>
                </div>
                <h3 class="header-title">📝 สมัครสมาชิกใหม่</h3>
                <p class="text-muted">กรอกข้อมูลให้ครบถ้วนเพื่อเข้าใช้งานระบบ</p>
            </div>
            
            <form action="register_save.php" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="ชื่อผู้ใช้">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="รหัสผ่าน">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">ชื่อจริง</label>
                        <input type="text" name="firstname" class="form-control" required placeholder="ชื่อภาษาไทย">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">นามสกุล</label>
                        <input type="text" name="lastname" class="form-control" required placeholder="นามสกุลภาษาไทย">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label text-secondary small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="example@university.ac.th">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-secondary small fw-bold">ระดับการศึกษา</label>
                        <select name="education_level" class="form-select" required>
                            <option value="">เลือก..</option>
                            <option value="ปวช.">ปวช.</option>
                            <option value="ปวส.">ปวส.</option>
                            <option value="ป.ตรี">ป.ตรี</option>
                            <option value="ป.โท-เอก">ป.โท-เอก</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">คณะ</label>
                        <input type="text" name="faculty" class="form-control" required placeholder="ระบุคณะ">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">สาขาวิชา</label>
                        <input type="text" name="department" class="form-control" required placeholder="ระบุสาขาวิชา">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-bold">สิทธิ์การใช้งาน (Role)</label>
                    <select name="role" class="form-select" required>
                        <option value="student" selected>นักศึกษา (Student)</option>
                        <option value="teacher">อาจารย์ (Teacher)</option>
                        <option value="officer">เจ้าหน้าที่ (Officer)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-register w-100 mb-3 shadow">ยืนยันการสมัครสมาชิก</button>
                
                <div class="text-center">
                    <a href="login.php" class="text-navy text-decoration-none small fw-bold">
                        <i class="bi bi-arrow-left"></i> กลับไปหน้าเข้าสู่ระบบ
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center mt-3 text-white-50 small mb-5">
        &copy; 2026 University Activity Management System
    </div>
</body>
</html>