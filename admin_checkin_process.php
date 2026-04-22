<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    exit("Permission Denied");
}

if (isset($_GET['act_id']) && isset($_GET['uid']) && isset($_GET['action'])) {
    $act_id = mysqli_real_escape_string($conn, $_GET['act_id']);
    $user_id = mysqli_real_escape_string($conn, $_GET['uid']);
    $action = $_GET['action']; // 'present' หรือ 'absent'

    // ตรวจสอบก่อนว่าเคยมีข้อมูลใน tb_participation หรือยัง
    $check_sql = "SELECT * FROM tb_participation WHERE act_id = '$act_id' AND user_id = '$user_id'";
    $check_res = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        // ถ้ามีข้อมูลแล้วให้ UPDATE
        $sql = "UPDATE tb_participation SET status = '$action' WHERE act_id = '$act_id' AND user_id = '$user_id'";
    } else {
        // ถ้ายังไม่มี (กรณีแอดมินกดอนุมัติคนทียังไม่ได้สแกน) ให้ INSERT
        $sql = "INSERT INTO tb_participation (user_id, act_id, status, checkin_time) 
                VALUES ('$user_id', '$act_id', '$action', NOW())";
    }

    if (mysqli_query($conn, $sql)) {
        // สำเร็จ: ส่งกลับไปหน้าเดิมพร้อม id กิจกรรม
        header("Location: admin_checkin.php?id=$act_id");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>