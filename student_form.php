<?php
session_start();
include('db.php');

// ตรวจสอบ Login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') { 
    header("Location: login.php"); 
    exit; 
}

$act_id = isset($_GET['act_id']) ? mysqli_real_escape_string($conn, $_GET['act_id']) : '';

// ดึงชื่อกิจกรรมมาแสดง
$res_act = mysqli_query($conn, "SELECT act_name FROM tb_activity WHERE act_id = '$act_id'");
$act = mysqli_fetch_array($res_act);

if (!$act) {
    echo "<script>alert('ไม่พบข้อมูลกิจกรรม'); window.location='student_dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ส่งหลักฐาน - <?php echo $act['act_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Kanit', sans-serif; }
        .preview-img { width: 100%; max-width: 200px; margin-top: 10px; display: none; border-radius: 10px; border: 2px dashed #ccc; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">ส่งหลักฐานเข้าร่วมกิจกรรม</h4>
                    <p class="text-center text-primary fw-bold"><?php echo $act['act_name']; ?></p>
                    
                    <form action="student_save.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="act_id" value="<?php echo $act_id; ?>">

                        <div class="mb-3">
                            <label class="form-label">1. รูปบรรยากาศงาน</label>
                            <input type="file" name="img1" class="form-control" accept="image/*" onchange="preview(this, 'v1')" required>
                            <img id="v1" class="preview-img">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">2. รูปเซลฟี่ของคุณ</label>
                            <input type="file" name="img2" class="form-control" accept="image/*" onchange="preview(this, 'v2')" required>
                            <img id="v2" class="preview-img">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">3. รูปหลักฐานอื่นๆ</label>
                            <input type="file" name="img3" class="form-control" accept="image/*" onchange="preview(this, 'v3')" required>
                            <img id="v3" class="preview-img">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">บันทึกข้อมูล</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function preview(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(id).src = e.target.result;
                document.getElementById(id).style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</body>
</html>