<?php
session_start();
include('db.php');

// 1. ตรวจสอบ Login (ถ้าไม่มี Session ให้เด้งไป Login)
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit; 
}

$user_id = $_SESSION['user_id'];
$act_id = mysqli_real_escape_string($conn, $_POST['act_id']);

// 2. ฟังก์ชันอัปโหลดรูป
function uploadImage($fileInputName, $user_id, $act_id) {
    if (empty($_FILES[$fileInputName]["name"])) return "";
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $ext = pathinfo($_FILES[$fileInputName]["name"], PATHINFO_EXTENSION);
    $newfilename = $user_id . "_" . $act_id . "_" . time() . "_" . rand(10,99) . "." . $ext;
    
    if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $target_dir . $newfilename)) {
        return $newfilename;
    }
    return "";
}

$img1 = uploadImage('img1', $user_id, $act_id);
$img2 = uploadImage('img2', $user_id, $act_id);
$img3 = uploadImage('img3', $user_id, $act_id);

// 3. ตรวจสอบว่าเคยส่งข้อมูลหรือยัง
$check = mysqli_query($conn, "SELECT * FROM tb_participation WHERE user_id = '$user_id' AND act_id = '$act_id'");

if (mysqli_num_rows($check) > 0) {
    // กรณี Update
    $sql = "UPDATE tb_participation SET 
            img1 = '$img1', img2 = '$img2', img3 = '$img3', 
            status = 'present', checkin_time = NOW()
            WHERE user_id = '$user_id' AND act_id = '$act_id'";
} else {
    // กรณี Insert (เปลี่ยน reg_date เป็น checkin_time ตามไฟล์ SQL)
    $sql = "INSERT INTO tb_participation (user_id, act_id, img1, img2, img3, status, checkin_time) 
            VALUES ('$user_id', '$act_id', '$img1', '$img2', '$img3', 'present', NOW())";
}

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('บันทึกข้อมูลเรียบร้อยแล้ว');
            window.location = 'student_dashboard.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>