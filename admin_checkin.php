<?php
session_start();
include('db.php');

$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) { 
    header("Location: login.php"); exit; 
}

$act_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// ปรับ Query ให้ดึงชื่อไฟล์รูปภาพ (img1, img2, img3) ออกมาด้วย
$sql = "SELECT u.user_id, u.firstname, u.lastname, u.education_level, 
               p.status, p.checkin_time, p.img1, p.img2, p.img3
        FROM tb_user u 
        LEFT JOIN tb_participation p ON u.user_id = p.user_id AND p.act_id = '$act_id'
        WHERE u.role = 'student' 
        ORDER BY u.firstname ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบหลักฐานการเข้าร่วม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Kanit', sans-serif; }
        .card { border-radius: 15px; }
        .img-thumbnail-custom { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer; }
        .text-navy { color: #1a237e; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-navy"><i class="bi bi-person-check-fill me-2"></i>ตรวจสอบหลักฐานและสถานะ</h5>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ชื่อ-นามสกุล</th>
                        <th>หลักฐาน (รูปภาพ)</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">เวลาที่บันทึก</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_array($result)) { ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold"><?php echo $row['firstname']." ".$row['lastname']; ?></span><br>
                            <small class="text-muted"><?php echo $row['education_level']; ?></small>
                        </td>
                        <td>
                            <?php if($row['img1']): ?>
                                <div class="d-flex gap-1">
                                    <img src="uploads/<?php echo $row['img1']; ?>" class="img-thumbnail-custom border" data-bs-toggle="modal" data-bs-target="#viewImg<?php echo $row['user_id']; ?>">
                                    <span class="badge bg-light text-dark align-self-center border">3 รูป</span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">ไม่มีหลักฐาน</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                            if($row['status'] == 'present') echo '<span class="badge bg-success px-3">เข้าร่วมแล้ว</span>';
                            elseif($row['status'] == 'wait') echo '<span class="badge bg-warning text-dark px-3">รอตรวจสอบ</span>';
                            else echo '<span class="badge bg-danger px-3">ยังไม่เข้าร่วม</span>';
                            ?>
                        </td>
                        <td class="text-center small text-muted"><?php echo $row['checkin_time'] ? date('d/m/Y H:i', strtotime($row['checkin_time'])) : '-'; ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <?php if($row['status'] == 'wait' || $row['status'] == 'absent'): ?>
                                    <a href="admin_checkin_process.php?act_id=<?php echo $act_id; ?>&uid=<?php echo $row['user_id']; ?>&action=present" class="btn btn-sm btn-success" onclick="return confirm('ยืนยันการอนุมัติ?')">อนุมัติ</a>
                                <?php endif; ?>
                                
                                <?php if($row['status'] == 'wait' || $row['status'] == 'present'): ?>
                                    <a href="admin_checkin_process.php?act_id=<?php echo $act_id; ?>&uid=<?php echo $row['user_id']; ?>&action=absent" class="btn btn-sm btn-danger" onclick="return confirm('ปฏิเสธการเข้าร่วม?')">ไม่ผ่าน</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <?php if($row['img1']): ?>
                    <div class="modal fade" id="viewImg<?php echo $row['user_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow rounded-4">
                                <div class="modal-header">
                                    <h5 class="modal-title">หลักฐานของ <?php echo $row['firstname']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body bg-light">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <p class="small fw-bold mb-1">รูปที่ 1</p>
                                            <img src="uploads/<?php echo $row['img1']; ?>" class="img-fluid rounded shadow-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="small fw-bold mb-1">รูปที่ 2</p>
                                            <img src="uploads/<?php echo $row['img2']; ?>" class="img-fluid rounded shadow-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="small fw-bold mb-1">รูปที่ 3</p>
                                            <img src="uploads/<?php echo $row['img3']; ?>" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>