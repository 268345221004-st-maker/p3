<?php
session_start();
include('db.php');

// ตรวจสอบ Login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') { 
    header("Location: login.php"); 
    exit; 
}

$my_id = $_SESSION['user_id'];
$display_user = $_SESSION['fullname'] ?? "นักศึกษา";

// SQL ฉบับสมบูรณ์: JOIN เพื่อเช็คว่าเรา (my_id) มีข้อมูลใน tb_participation ของกิจกรรมนั้นๆ หรือยัง
$sql = "SELECT a.*, p.part_id 
        FROM tb_activity a 
        LEFT JOIN tb_participation p ON a.act_id = p.act_id AND p.user_id = '$my_id'
        WHERE a.activity_status = 1 
        ORDER BY a.act_date DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบกิจกรรมนักศึกษา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .navbar-custom { background: #1a237e; color: white; padding: 1rem; border-bottom: 4px solid #ffd600; }
        .card-activity { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.3s; }
        .card-activity:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<nav class="navbar-custom mb-4 shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">ระบบเข้าร่วมกิจกรรม</h4>
        <div class="text-white small"><i class="bi bi-person-circle"></i> <?php echo $display_user; ?></div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) { 
                // ถ้า part_id ไม่ว่าง แปลว่ามีข้อมูลการเข้าร่วมแล้ว
                $is_registered = !empty($row['part_id']);
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-activity h-100">
                <div class="card-body">
                    <span class="badge <?php echo $is_registered ? 'bg-success' : 'bg-secondary'; ?> mb-3">
                        <?php echo $is_registered ? 'ลงทะเบียนแล้ว' : 'ยังไม่ได้ลงทะเบียน'; ?>
                    </span>
                    <h5 class="fw-bold"><?php echo $row['act_name']; ?></h5>
                    <p class="text-muted small">
                        <i class="bi bi-calendar-event"></i> <?php echo date('d/m/Y', strtotime($row['act_date'])); ?><br>
                        <i class="bi bi-geo-alt"></i> <?php echo $row['act_location']; ?>
                    </p>
                    <div class="d-grid mt-3">
                        <?php if ($is_registered): ?>
                            <button class="btn btn-outline-success disabled">
                                <i class="bi bi-check-circle-fill"></i> ส่งหลักฐานเรียบร้อย
                            </button>
                        <?php else: ?>
                            <a href="student_form.php?act_id=<?php echo $row['act_id']; ?>" class="btn btn-primary">
                                เช็คชื่อ / ส่งหลักฐาน <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            } 
        } else {
            echo '<div class="col-12 text-center py-5">ไม่มีกิจกรรมที่เปิดอยู่</div>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>