<?php
session_start();
include('db.php'); 

// ตรวจสอบสิทธิ์
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit;
}

// ระบบ เปิด-ปิด กิจกรรม
if (isset($_GET['toggle_id']) && isset($_GET['current_status'])) {
    $id = mysqli_real_escape_string($conn, $_GET['toggle_id']);
    $new_status = ($_GET['current_status'] == 1) ? 0 : 1;
    mysqli_query($conn, "UPDATE tb_activity SET activity_status = $new_status WHERE act_id = $id");
    header("Location: admin_dashboard.php");
    exit;
}

// ระบบลบกิจกรรม
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM tb_activity WHERE act_id = $id");
    header("Location: admin_dashboard.php");
    exit;
}

// ดึงจำนวนนักศึกษาทั้งหมด
$sql_all_students = "SELECT COUNT(*) as total FROM tb_user WHERE role = 'student'";
$res_all = mysqli_query($conn, $sql_all_students);
$row_all = mysqli_fetch_assoc($res_all);
$total_students_in_system = (int)$row_all['total'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการกิจกรรม - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #1a237e; --blue-header: #5dade2; --gold: #ffd600; }
        body { background-color: #f4f7f6; font-family: 'Kanit', sans-serif; }
        .navbar-custom { background-color: var(--navy); padding: 12px 0; border-bottom: 3px solid var(--gold); }
        .nav-btn { background: rgba(255, 255, 255, 0.1); color: white; border-radius: 50px; margin: 0 5px; padding: 8px 16px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .nav-btn:hover { background: var(--gold); color: var(--navy); }
        .content-card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 30px; }
        .table thead th { background-color: var(--blue-header); color: white; padding: 15px; border: none; font-weight: 500; }
        
        /* สไตล์ปุ่มจัดการ */
        .btn-group-manage { display: flex; flex-direction: column; gap: 5px; align-items: center; }
        .btn-row { display: flex; gap: 5px; }
        .btn-half { width: 42px; height: 38px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
        .btn-half:hover { opacity: 0.8; transform: scale(1.05); }
        .btn-full-print { width: 89px; height: 35px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; text-decoration: none; border: none; background-color: #45b39d; }
        
        .bg-purple { background-color: #8e44ad; }
        .bg-yellow { background-color: #f1c40f; color: #333 !important; }
        .bg-red { background-color: #e74c3c; }
        .bg-navy-light { background-color: #2c3e50; }
        .stat-badge { font-size: 0.85rem; min-width: 85px; padding: 8px 12px; border-radius: 8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-custom shadow">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <div class="text-white fs-5"><i class="bi bi-shield-lock-fill me-2"></i><b>ADMIN PANEL</b></div>
        <div class="d-flex align-items-center">
            <a href="admin_dashboard.php" class="nav-btn bg-white bg-opacity-25">Dashboard</a>
            <a href="admin_form.php" class="nav-btn">เพิ่มกิจกรรม</a>
            <a href="admin_users.php" class="nav-btn"><i class="bi bi-people-fill me-1"></i> ผู้ใช้งาน</a>
            <a href="logout.php" class="nav-btn bg-danger">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 mt-4">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--navy);">จัดการกิจกรรมและการลงทะเบียน</h4>
                <p class="text-muted small mb-0">ตรวจสอบสถานะนักศึกษาและรูปภาพหลักฐาน</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary p-2 mb-1">นักศึกษาในระบบ: <?php echo $total_students_in_system; ?> คน</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th style="border-radius: 10px 0 0 0;">ลำดับ</th>
                        <th class="text-start">ชื่อกิจกรรม / QR Code</th>
                        <th>วันที่จัด</th>
                        <th>ลงทะเบียนแล้ว</th>
                        <th>สถานะสมัคร</th>
                        <th>เปิด/ปิด</th>
                        <th style="border-radius: 0 10px 0 0;">จัดการ / พิมพ์</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.*, 
                            (SELECT COUNT(*) FROM tb_participation p WHERE p.act_id = a.act_id) as count_registered
                            FROM tb_activity a
                            ORDER BY a.act_id DESC";
                    
                    $res = mysqli_query($conn, $sql);
                    $i = 1; 
                    while($row = mysqli_fetch_array($res)) {
                        $is_open = $row['activity_status'];
                        $registered = (int)$row['count_registered'];
                        $qr_link = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $row['act_id'];
                    ?>
                    <tr>
                        <td><span class="text-muted"><?php echo $i++; ?></span></td>
                        <td class="text-start">
                            <div class="fw-bold mb-1"><?php echo $row['act_name']; ?></div>
                            <img src="<?php echo $qr_link; ?>" alt="QR" style="width: 45px; height: 45px; border: 1px solid #ddd; border-radius: 4px;" title="QR สำหรับกิจกรรมนี้">
                        </td>
                        <td><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($row['act_date'])); ?></td>
                        <td>
                            <div class="stat-badge border bg-light d-inline-block">
                                <b class="text-primary"><?php echo $registered; ?></b> / <?php echo $total_students_in_system; ?>
                            </div>
                        </td>
                        <td>
                            <?php if($is_open == 1): ?>
                                <span class="badge rounded-pill bg-success px-3">เปิดสมัคร</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-secondary px-3">ปิดรับ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                    onclick="if(confirm('เปลี่ยนสถานะกิจกรรมหรือไม่?')) location.href='?toggle_id=<?php echo $row['act_id']; ?>&current_status=<?php echo $is_open; ?>'" 
                                    <?php echo ($is_open == 1) ? 'checked' : ''; ?>>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group-manage">
                                <div class="btn-row">
                                    <button class="btn-half bg-navy-light" title="ตรวจหลักฐาน" 
                                            onclick="viewEvidence(<?php echo $row['act_id']; ?>, '<?php echo htmlspecialchars($row['act_name']); ?>')">
                                        <i class="bi bi-images"></i>
                                    </button>
                                    <a href="admin_qrcode_view.php?id=<?php echo $row['act_id']; ?>" class="btn-half bg-purple" title="ขยาย QR Code">
                                        <i class="bi bi-qr-code"></i>
                                    </a>
                                </div>
                                <div class="btn-row">
                                    <a href="admin_form.php?id=<?php echo $row['act_id']; ?>" class="btn-half bg-yellow" title="แก้ไข"><i class="bi bi-pencil-square"></i></a>
                                    <a href="?delete=<?php echo $row['act_id']; ?>" class="btn-half bg-red" onclick="return confirm('ลบกิจกรรมนี้หรือไม่?')"><i class="bi bi-trash"></i></a>
                                </div>
                                <a href="admin_print_list.php?id=<?php echo $row['act_id']; ?>" target="_blank" class="btn-full-print" title="พิมพ์รายชื่อผู้เข้าร่วม">
                                    <i class="bi bi-printer-fill me-1"></i> พิมพ์รายชื่อ
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="evidenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title" id="evidenceTitle">รายชื่อผู้เข้าร่วมและหลักฐาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="evidenceContent">
                <div class="text-center p-5">กำลังโหลดข้อมูล...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function viewEvidence(actId, actName) {
    $('#evidenceTitle').text('กิจกรรม: ' + actName);
    $('#evidenceModal').modal('show');
    $('#evidenceContent').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><br>กำลังโหลดรายชื่อนักศึกษา...</div>');

    $.ajax({
        url: 'admin_get_evidence.php',
        type: 'GET',
        data: { id: actId },
        success: function(response) {
            $('#evidenceContent').html(response);
        },
        error: function() {
            $('#evidenceContent').html('<p class="text-center text-danger p-5">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>');
        }
    });
}
</script>

</body>
</html>