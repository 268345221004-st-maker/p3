<?php
session_start();
include('db.php');
$allowed_roles = array('admin', 'teacher', 'officer');
if (!in_array($_SESSION['role'], $allowed_roles)) { 
    header("Location: login.php"); 
    exit; 
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // ดึงสถานะปัจจุบัน
    $query = mysqli_query($conn, "SELECT activity_status FROM tb_activity WHERE act_id = $id");
    $row = mysqli_fetch_assoc($query);
    
    // สลับสถานะ (ถ้าเป็น 1 เปลี่ยนเป็น 0, ถ้าเป็น 0 เปลี่ยนเป็น 1)
    $new_status = ($row['activity_status'] == 1) ? 0 : 1;
    
    // อัปเดตลงฐานข้อมูล
    mysqli_query($conn, "UPDATE tb_activity SET activity_status = $new_status WHERE act_id = $id");
}

// กลับไปหน้า Dashboard
header("Location: admin_dashboard.php");
?>