<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ Admin
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit;
}

// รับค่า ID กิจกรรม
$act_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($act_id)) {
    header("Location: admin_dashboard.php");
    exit;
}

// ดึงชื่อกิจกรรม
$sql_act = "SELECT act_name FROM tb_activity WHERE act_id = '$act_id'";
$res_act = mysqli_query($conn, $sql_act);
$act_info = mysqli_fetch_assoc($res_act);

// ดึงรายชื่อผู้เข้าร่วมและรูปภาพหลักฐาน
$sql_evidence = "SELECT p.*, u.firstname, u.lastname, u.user_id 
                  FROM tb_participation p 
                  JOIN tb_user u ON p.user_id = u.user_id 
                  WHERE p.act_id = '$act_id' 
                  ORDER BY p.reg_date DESC";
$res_evidence = mysqli_query($conn, $sql_evidence);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบหลักฐาน - <?php echo $act_info['act_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #1a237e; --gold: #ffd600; }
        body { background-color: #f4f7f6; font-family: 'Kanit', sans-serif; }
        .navbar-custom { background-color: var(--navy); padding: 12px 0; border-bottom: 3px solid var(--gold); color: white; }
        .evidence-card { border: none; border-radius: 15px; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .img-evidence { width: 100%; height: 150px; object-fit: cover; border-radius: 10px; cursor: pointer; transition: 0.3s; border: 1px solid #ddd; }
        .img-evidence:hover { opacity: 0.8; transform: scale(1.02); }
        .user-info { border-left: 4px solid var(--navy); padding-left: 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-custom shadow mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-images me-2"></i>ตรวจสอบรูปภาพหลักฐาน</span>
        <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-3">กลับหน้าหลัก</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-4 shadow-sm">
                <h4 class="fw-bold mb-1" style="color: var(--navy);">กิจกรรม: <?php echo $act_info['act_name']; ?></h4>
                <p class="text-muted mb-0 small">รายชื่อนักศึกษาที่ส่งหลักฐานและสแกนเช็คชื่อเรียบร้อยแล้ว</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if (mysqli_num_rows($res_evidence) > 0): ?>
            <?php while($row = mysqli_fetch_array($res_evidence)): ?>
                <div class="col-12">
                    <div class="card evidence-card p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                                <div class="user-info">
                                    <h5 class="fw-bold mb-0"><?php echo $row['firstname'] . " " . $row['lastname']; ?></h5>
                                    <p class="text-muted small mb-2">รหัสนักศึกษา: <?php echo $row['user_id']; ?></p>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-clock me-1"></i>ส่งเมื่อ: <?php echo date('d/m/Y H:i', strtotime($row['reg_date'])); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-12">
                                <div class="row g-2">
                                    <?php for($i=1; $i<=3; $i++): 
                                        $img_field = "img".$i;
                                        $img_url = "uploads/" . $row[$img_field];
                                    ?>
                                        <div class="col-4">
                                            <?php if (!empty($row[$img_field])): ?>
                                                <a href="<?php echo $img_url; ?>" target="_blank">
                                                    <img src="<?php echo $img_url; ?>" class="img-evidence" alt="หลักฐานที่ <?php echo $i; ?>">
                                                </a>
                                                <div class="text-center mt-1 small text-muted">รูปที่ <?php echo $i; ?></div>
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center text-muted rounded" style="height: 150px; font-size: 12px;">ไม่มีรูป</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
                <p class="mt-3 text-muted">ยังไม่มีผู้ส่งหลักฐานสำหรับกิจกรรมนี้</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>