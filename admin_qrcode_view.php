<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ (Admin, Teacher, Officer)
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit;
}

// รับ ID กิจกรรม
$act_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($act_id)) {
    header("Location: admin_dashboard.php");
    exit;
}

// ดึงข้อมูลกิจกรรมเพื่อเอาชื่อมาโชว์
$sql = "SELECT * FROM tb_activity WHERE act_id = '$act_id'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo "ไม่พบข้อมูลกิจกรรม";
    exit;
}

// --- ส่วนสำคัญ: สร้าง Link สำหรับสแกนให้เด้งไปหน้า student_form.php ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// ดึงที่อยู่โฟลเดอร์ปัจจุบัน (เพื่อให้สแกนแล้วเจอไฟล์ในเครื่องหรือโฮสต์จริง)
$current_dir = dirname($_SERVER['PHP_SELF']); 

// สร้าง URL เต็มไปยังไฟล์ student_form.php พร้อมส่งค่า act_id
$registration_url = $protocol . "://" . $host . $current_dir . "/student_form.php?act_id=" . $act_id;

// สร้าง QR Code จาก URL ด้านบน (ขนาด 300x300)
$qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($registration_url);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code สำหรับลงทะเบียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #1a237e; --gold: #ffd600; }
        body { background-color: #f4f7f6; font-family: 'Kanit', sans-serif; }
        .qr-container { 
            max-width: 500px; 
            margin: 50px auto; 
            background: white; 
            border-radius: 30px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #eee;
        }
        .qr-header { background: var(--navy); color: white; padding: 30px; text-align: center; }
        .qr-body { padding: 40px; text-align: center; }
        .qr-image { 
            background: #fff; 
            padding: 20px; 
            border: 2px dashed #ddd; 
            border-radius: 20px; 
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-action { border-radius: 50px; padding: 10px 25px; font-weight: 500; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .qr-container { box-shadow: none; margin: 0; max-width: 100%; border: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="qr-container">
        <div class="qr-header">
            <h4 class="fw-bold mb-1">สแกนเพื่อลงทะเบียน</h4>
            <div class="badge bg-warning text-dark px-3 py-2 rounded-pill mt-2">
                <i class="bi bi-calendar-check me-1"></i> <?php echo $row['act_name']; ?>
            </div>
        </div>

        <div class="qr-body text-center">
            <div class="qr-image">
                <img src="<?php echo $qr_api_url; ?>" alt="QR Code" class="img-fluid" style="width: 250px; height: 250px;">
            </div>
            
            <div class="mb-4">
                <p class="text-muted mb-0 small">รหัสกิจกรรม: #<?php echo $act_id; ?></p>
                <p class="fw-bold mt-1" style="color: var(--navy);">วันที่จัด: <?php echo date('d/m/Y', strtotime($row['act_date'])); ?></p>
            </div>

            <div class="d-flex justify-content-center gap-2 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-action shadow-sm">
                    <i class="bi bi-printer me-2"></i>พิมพ์ / บันทึก PDF
                </button>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-action shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>ย้อนกลับ
                </a>
            </div>
        </div>
        <div class="pb-4 text-center">
            <small class="text-muted small">ระบบเช็คชื่อกิจกรรมอัตโนมัติ</small>
        </div>
    </div>
</div>

</body>
</html>