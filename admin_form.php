<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) { 
    header("Location: login.php"); 
    exit; 
}

// กำหนดค่าเริ่มต้น
$act_id = ""; $act_name = ""; $act_date = date('Y-m-d'); 
$act_location = ""; $act_detail = "";
$title = "เพิ่มกิจกรรมใหม่";

// ดึงข้อมูลกรณี "แก้ไข"
if (isset($_GET['id'])) {
    $act_id = mysqli_real_escape_string($conn, $_GET['id']);
    $title = "แก้ไขข้อมูลกิจกรรม";
    $result = mysqli_query($conn, "SELECT * FROM tb_activity WHERE act_id = '$act_id'");
    $row = mysqli_fetch_array($result);
    if($row) {
        $act_name = $row['act_name'];
        $act_date = $row['act_date'];
        $act_location = $row['act_location'];
        $act_detail = $row['act_detail'];
    }
}

// ระบบบันทึก
if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['act_name']);
    $date = mysqli_real_escape_string($conn, $_POST['act_date']);
    $loc = mysqli_real_escape_string($conn, $_POST['act_location']);
    $det = mysqli_real_escape_string($conn, $_POST['act_detail']);
    $id_post = mysqli_real_escape_string($conn, $_POST['act_id']);

    if ($id_post != "") {
        $sql = "UPDATE tb_activity SET act_name='$name', act_date='$date', act_location='$loc', act_detail='$det' WHERE act_id='$id_post'";
    } else {
        $sql = "INSERT INTO tb_activity (act_name, act_date, act_location, act_detail, activity_status) VALUES ('$name', '$date', '$loc', '$det', 1)";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('บันทึกสำเร็จ'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; background-color: #f4f7f6; }</style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px; border-radius: 15px;">
            <h3 class="text-center mb-4 text-primary fw-bold"><?php echo $title; ?></h3>
            <form method="post">
                <input type="hidden" name="act_id" value="<?php echo $act_id; ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อกิจกรรม</label>
                    <input type="text" name="act_name" class="form-control" value="<?php echo $act_name; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">วันที่</label>
                    <input type="date" name="act_date" class="form-control" value="<?php echo $act_date; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">สถานที่</label>
                    <input type="text" name="act_location" class="form-control" value="<?php echo $act_location; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">รายละเอียด</label>
                    <textarea name="act_detail" class="form-control" rows="3"><?php echo $act_detail; ?></textarea>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" name="save" class="btn btn-primary btn-lg">บันทึกข้อมูล</button>
                    <a href="admin_dashboard.php" class="btn btn-light border">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>