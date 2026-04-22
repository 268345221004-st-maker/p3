<?php
session_start();
include('db.php');

// 1. รับค่าและป้องกัน SQL Injection
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// รับค่า redirect_act_id (กรณีสแกน QR Code มา)
$redirect_act_id = isset($_POST['redirect_act_id']) ? mysqli_real_escape_string($conn, $_POST['redirect_act_id']) : '';

// 2. ค้นหาผู้ใช้ (ตรวจสอบ Username และ Password ตรงๆ ตามโครงสร้าง DB ของคุณ)
$sql = "SELECT * FROM tb_user WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);

    // 3. สร้าง Session เพื่อใช้ในหน้าอื่นๆ
    $_SESSION['user_id']  = $row['user_id'];
    $_SESSION['fullname'] = $row['firstname'] . ' ' . $row['lastname'];
    $_SESSION['role']     = $row['role'];

    // 4. ตรวจสอบกลุ่มผู้ดูแลระบบ
    $admins = array('admin', 'teacher', 'officer');

    if (in_array($row['role'], $admins)) {
        // ถ้าเป็น Admin/Teacher/Officer ไปหน้าหลังบ้าน
        header("Location: admin_dashboard.php");
        exit;
    } else {
        // ถ้าเป็นนักศึกษา
        if (!empty($redirect_act_id)) {
            // ถ้าสแกน QR มา ให้เด้งไปหน้าส่งรูปกิจกรรมนั้นทันที
            header("Location: student_form.php?act_id=$redirect_act_id");
        } else {
            // ถ้า Login ปกติ ไปหน้า Dashboard นักศึกษา
            header("Location: student_dashboard.php");
        }
        exit;
    }
} else {
    // กรณีใส่รหัสผิด
    echo "<script>
            alert('Username หรือ Password ไม่ถูกต้อง');
            window.location='login.php" . ($redirect_act_id ? "?redirect_act_id=$redirect_act_id" : "") . "';
          </script>";
}
?>