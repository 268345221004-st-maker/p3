<?php
session_start();
include('db.php');

// 1. ตรวจสอบสิทธิ์ (Admin, Teacher, Officer)
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit;
}

// 2. ระบบอัปเดตสถานะ (เมื่อกดปุ่ม อนุมัติ หรือ ไม่ผ่าน)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    $new_status = ($action == 'approve') ? 'approved' : 'rejected';
    
    mysqli_query($conn, "UPDATE tb_participation SET status = '$new_status' WHERE id = '$id'");
    echo "<script>window.location='admin_check_photos.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ตรวจสอบการส่งงาน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root { --univ-navy: #1a237e; --univ-gold: #ffd600; }
        body { font-family: 'Kanit', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background: var(--univ-navy); border-bottom: 4px solid var(--univ-gold); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .img-preview { 
            width: 100px; height: 100px; object-fit: cover; 
            border-radius: 10px; cursor: pointer; transition: 0.3s;
            border: 2px solid #eee;
        }
        .img-preview:hover { transform: scale(1.1); border-color: var(--univ-navy); }
        .status-badge { border-radius: 50px; padding: 5px 15px; font-weight: 500; font-size: 0.85rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-custom mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php"><i class="bi bi-check-all"></i> ตรวจสอบผลการเข้าร่วมกิจกรรม</a>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold text-navy">รายชื่อนักศึกษาที่ส่งหลักฐาน</h4>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">กลับหน้าหลัก</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>นักศึกษา / กิจกรรม</th>
                            <th>หลักฐาน (3 รูป)</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ดึงข้อมูลรูปภาพ พร้อมชื่อนักศึกษา และชื่อกิจกรรม
                        $sql = "SELECT p.*, u.firstname, u.lastname, a.act_name 
                                FROM tb_participation p 
                                JOIN tb_user u ON p.user_id = u.user_id 
                                JOIN tb_activity a ON p.act_id = a.act_id
                                ORDER BY p.id DESC";
                        $result = mysqli_query($conn, $sql);
                        
                        while($row = mysqli_fetch_array($result)) {
                            $path = "uploads/";
                            
                            // จัดการสี Badge ตามสถานะ
                            $st = $row['status'];
                            $badge_class = "bg-warning text-dark"; // pending
                            if($st == 'approved') $badge_class = "bg-success text-white";
                            if($st == 'rejected') $badge_class = "bg-danger text-white";
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></div>
                                <small class="text-muted"><i class="bi bi-tag"></i> <?php echo $row['act_name']; ?></small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo $path.$row['img1']; ?>" target="_blank"><img src="<?php echo $path.$row['img1']; ?>" class="img-preview"></a>
                                    <a href="<?php echo $path.$row['img2']; ?>" target="_blank"><img src="<?php echo $path.$row['img2']; ?>" class="img-preview"></a>
                                    <a href="<?php echo $path.$row['img3']; ?>" target="_blank"><img src="<?php echo $path.$row['img3']; ?>" class="img-preview"></a>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge <?php echo $badge_class; ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn