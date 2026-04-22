<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

// ตรวจสอบว่ามีการส่งค่า POST มาจริงหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_code = mysqli_real_escape_string($conn, $_POST['student_code']);
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $password     = mysqli_real_escape_string($conn, $_POST['password']);
    $firstname    = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname     = mysqli_real_escape_string($conn, $_POST['lastname']);
    $education    = mysqli_real_escape_string($conn, $_POST['education_level'] ?? ''); // กันเหนียวถ้าค่าไม่มา
    $role         = 'student';

    // 1. เช็ค Username ซ้ำ
    $check_sql    = "SELECT * FROM tb_user WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username นี้มีคนใช้แล้ว'); window.history.back();</script>";
        exit;
    }

    // 2. บันทึกข้อมูล
    $sql = "INSERT INTO tb_user (student_code, username, password, firstname, lastname, education_level, role) 
            VALUES ('$student_code', '$username', '$password', '$firstname', '$lastname', '$education', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
    } else {
        // ถ้าพังตรงนี้ จะโชว์เลยว่าชื่อคอลัมน์ใน DB ไม่ตรง หรือ SQL ผิดตรงไหน
        echo "<div style='color:red; border:1px solid red; padding:15px;'>";
        echo "<h4>❌ สมัครสมาชิกไม่สำเร็จ</h4>";
        echo "<b>สาเหตุ:</b> " . mysqli_error($conn) . "<br>";
        echo "<b>SQL:</b> " . $sql;
        echo "</div>";
    }
} else {
    echo "ไม่มีข้อมูลส่งมา";
}
?>