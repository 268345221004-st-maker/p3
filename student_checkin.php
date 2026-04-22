<?php
session_start();
include('db.php');

// 1. รับค่า act_id จาก QR Code
$act_id = isset($_GET['act_id']) ? mysqli_real_escape_string($conn, $_GET['act_id']) : '';

// 2. ถ้ายังไม่ Login ให้จำหน้าที่จะไปแล้วเด้งไปหน้า Login ก่อน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect_act_id=$act_id");
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลกิจกรรม
$res_act = mysqli_query($conn, "SELECT * FROM tb_activity WHERE act_id = '$act_id'");
$act = mysqli_fetch_array($res_act);

// 3. เมื่อกดปุ่มยืนยัน
if (isset($_POST['btn_confirm'])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $imgs = [];
    $error_upload = false;

    // วนลูปรับไฟล์ 3 ช่อง (img1, img2, img3)
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_FILES['pic'.$i]['name'])) {
            $ext = pathinfo($_FILES['pic'.$i]['name'], PATHINFO_EXTENSION);
            $filename = "ACT".$act_id."_U".$user_id."_".time()."_$i.".$ext;
            move_uploaded_file($_FILES['pic'.$i]['tmp_name'], $upload_dir . $filename);
            $imgs[] = $filename;
        } else {
            $error_upload = true;
        }
    }

    if (!$error_upload) {
        $sql = "INSERT INTO tb_participation (act_id, user_id, img1, img2, img3, status, checkin_time) 
                VALUES ('$act_id', '$user_id', '$imgs[0]', '$imgs[1]', '$imgs[2]', 'wait', NOW())
                ON DUPLICATE KEY UPDATE img1='$imgs[0]', img2='$imgs[1]', img3='$imgs[2]', status='wait', checkin_time=NOW()";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('ส่งหลักฐานเรียบร้อยแล้ว'); window.location='student_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('กรุณาอัปโหลดรูปภาพให้ครบทั้ง 3 ช่อง');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บันทึกการเข้าร่วมกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f0f2f5; }
        .card-upload { max-width: 500px; margin: 30px auto; border-radius: 20px; border: none; }
        .form-label { font-weight: 600; color: #495057; }
    </style>
</head>
<body>
<div class="container">
    <div class="card card-upload shadow-lg">
        <div class="card-header bg-success text-white text-center py-3" style="border-radius: 20px 20px 0 0;">
            <h4 class="mb-0">ส่งหลักฐานเข้าร่วมกิจกรรม</h4>
        </div>
        <div class="card-body p-4">
            <h5 class="text-center text-primary mb-4"><?php echo $act['act_name']; ?></h5>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">รูปที่ 1: รูปภาพขณะร่วมกิจกรรม</label>
                    <input type="file" name="pic1" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รูปที่ 2: รูปภาพป้ายกิจกรรม/สถานที่</label>
                    <input type="file" name="pic2" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">รูปที่ 3: รูปภาพหมู่หรือรูปภาพอื่นๆ</label>
                    <input type="file" name="pic3" class="form-control" accept="image/*" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="btn_confirm" class="btn btn-success btn-lg shadow">ยืนยันการเช็คชื่อ</button>
                    <a href="student_dashboard.php" class="btn btn-light border">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>