<?php
// --- ส่วนที่ 1: เปิดโหมดแสดง Error ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
include('db.php');

// ตรวจสอบสิทธิ์
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit;
}

// กำหนดตัวแปรเริ่มต้น
$user_id = "";
$student_code = "";
$username = "";
$firstname = "";
$lastname = "";
$email = "";
$faculty = "";
$department = "";
$education = "";
$role = "student"; 
$title = "เพิ่มผู้ใช้งานใหม่";
$password_required = "required"; 
$password_placeholder = "ตั้งรหัสผ่าน...";

// ดึงข้อมูลกรณีแก้ไข
if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $title = "แก้ไขข้อมูลผู้ใช้งาน";
    $password_required = ""; 
    $password_placeholder = "(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)";
    
    try {
        $sql = "SELECT * FROM tb_user WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        if ($row) {
            $student_code = isset($row['student_code']) ? $row['student_code'] : '';
            $username = $row['username'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $email = isset($row['email']) ? $row['email'] : '';
            $faculty = isset($row['faculty']) ? $row['faculty'] : '';
            $department = isset($row['department']) ? $row['department'] : '';
            $education = $row['education_level'];
            $role = $row['role'];
        }
    } catch (Exception $e) {
        die("<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>");
    }
}

// บันทึกข้อมูล
if (isset($_POST['save'])) {
    try {
        $id = $_POST['user_id'];
        $sc = mysqli_real_escape_string($conn, $_POST['student_code']);
        $uname = mysqli_real_escape_string($conn, $_POST['username']);
        $pass = mysqli_real_escape_string($conn, $_POST['password']);
        $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
        $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
        $uemail = mysqli_real_escape_string($conn, $_POST['email']);
        $ufaculty = mysqli_real_escape_string($conn, $_POST['faculty']);
        $udept = mysqli_real_escape_string($conn, $_POST['department']);
        $edu = $_POST['education_level'];
        $urole = $_POST['role'];

        if ($id != "") {
            // Update
            $sql = "UPDATE tb_user SET 
                    student_code='$sc',
                    username='$uname', 
                    firstname='$fname', 
                    lastname='$lname', 
                    email='$uemail',
                    faculty='$ufaculty',
                    department='$udept',
                    education_level='$edu', 
                    role='$urole' 
                    WHERE user_id=$id";
            mysqli_query($conn, $sql);

            if (!empty($pass)) {
                mysqli_query($conn, "UPDATE tb_user SET password='$pass' WHERE user_id=$id");
            }
        } else {
            // Insert
            $check = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$uname'");
            if (mysqli_num_rows($check) > 0) {
                echo "<script>alert('Username นี้มีผู้ใช้แล้ว!'); window.history.back();</script>";
                exit;
            }
            
            $sql = "INSERT INTO tb_user (student_code, username, password, firstname, lastname, email, faculty, department, education_level, role) 
                    VALUES ('$sc', '$uname', '$pass', '$fname', '$lname', '$uemail', '$ufaculty', '$udept', '$edu', '$urole')";
            mysqli_query($conn, $sql);
        }
        
        echo "<script>alert('บันทึกข้อมูลเรียบร้อย'); window.location='admin_users.php';</script>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --univ-navy: #1a237e; --univ-gold: #ffd600; }
        body { font-family: 'Kanit', sans-serif; background-color: #f4f7f6; padding: 40px 0; }
        .card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border:none;}
        .card-header { background: var(--univ-navy); color: white; border-radius: 20px 20px 0 0 !important; padding: 15px 25px; border-bottom: 5px solid var(--univ-gold); }
        .form-label { font-weight: 600; color: var(--univ-navy); }
        .btn-save { background: var(--univ-navy); border: none; color: white; border-radius: 50px; padding: 10px 30px; font-weight: 600; }
        .btn-save:hover { background: #0d145a; color:white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i><?php echo $title; ?></h4></div>
                    <div class="card-body p-4">
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">รหัสนักศึกษา (ถ้ามี)</label>
                                    <input type="text" name="student_code" class="form-control" value="<?php echo $student_code; ?>" placeholder="ระบุรหัสนักศึกษา">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="<?php echo $password_placeholder; ?>" <?php echo $password_required; ?>>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อจริง</label>
                                    <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" name="lastname" class="form-control" value="<?php echo $lastname; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">คณะ</label>
                                    <input type="text" name="faculty" class="form-control" value="<?php echo $faculty; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">สาขาวิชา</label>
                                    <input type="text" name="department" class="form-control" value="<?php echo $department; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ระดับการศึกษา</label>
                                    <select name="education_level" class="form-select" required>
                                        <option value="">-- เลือก --</option>
                                        <option value="-" <?php if($education=='-') echo 'selected'; ?>>บุคลากร</option>
                                        <option value="ปวช." <?php if($education=='ปวช.') echo 'selected'; ?>>ปวช.</option>
                                        <option value="ปวส." <?php if($education=='ปวส.') echo 'selected'; ?>>ปวส.</option>
                                        <option value="ป.ตรี" <?php if($education=='ป.ตรี') echo 'selected'; ?>>ป.ตรี</option>
                                        <option value="ป.โท" <?php if($education=='ป.โท') echo 'selected'; ?>>ป.โท</option>
                                        <option value="ป.เอก" <?php if($education=='ป.เอก') echo 'selected'; ?>>ป.เอก</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">สิทธิ์การใช้งาน</label>
                                <select name="role" class="form-select" required>
                                    <option value="student" <?php if($role=='student') echo 'selected'; ?>>นักศึกษา</option>
                                    <option value="teacher" <?php if($role=='teacher') echo 'selected'; ?>>อาจารย์</option>
                                    <option value="officer" <?php if($role=='officer') echo 'selected'; ?>>เจ้าหน้าที่</option>
                                    <option value="admin" <?php if($role=='admin') echo 'selected'; ?>>ผู้ดูแลระบบ</option>
                                </select>
                            </div>
                            <div class="text-end">
                                <a href="admin_users.php" class="btn btn-secondary rounded-pill px-4">ยกเลิก</a>
                                <button type="submit" name="save" class="btn btn-save rounded-pill px-4">บันทึกข้อมูล</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>