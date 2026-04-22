<?php
session_start();
include('db.php');

// ตรวจสอบสิทธิ์ผู้ใช้งาน
$allowed_roles = array('admin', 'teacher', 'officer');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    exit("ไม่มีสิทธิ์เข้าถึงหน้าหนี้");
}

// รับค่าไอดีกิจกรรม
$act_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($act_id)) {
    exit("ไม่พบไอดีกิจกรรม");
}

// 1. ดึงข้อมูลกิจกรรม
$sql_act = "SELECT * FROM tb_activity WHERE act_id = '$act_id'";
$res_act = mysqli_query($conn, $sql_act);
$act = mysqli_fetch_assoc($res_act);

// 2. ดึงรายชื่อผู้ที่เข้าร่วมกิจกรรมนี้ (JOIN ตาราง User)
$sql_list = "SELECT u.firstname, u.lastname, u.education_level, p.reg_date 
             FROM tb_participation p
             JOIN tb_user u ON p.user_id = u.user_id
             WHERE p.act_id = '$act_id'
             ORDER BY u.firstname ASC";
$res_list = mysqli_query($conn, $sql_list);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อ - <?php echo $act['act_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background: #fff; color: #000; }
        .header-title { text-align: center; margin-top: 30px; margin-bottom: 30px; }
        .table-report { width: 100%; border-collapse: collapse; }
        .table-report th, .table-report td { 
            border: 1px solid #000 !important; 
            padding: 8px; 
            vertical-align: middle;
        }
        .table-report th { background-color: #f2f2f2 !important; }
        
        /* ตั้งค่าสำหรับการพิมพ์ */
        @media print {
            .no-print { display: none; }
            @page { size: A4; margin: 1cm; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="container mt-4">
    <div class="no-print text-center mb-4">
        <button onclick="window.print();" class="btn btn-primary btn-lg">
            <i class="bi bi-printer"></i> คลิกเพื่อพิมพ์หน้านี้
        </button>
        <button onclick="window.close();" class="btn btn-outline-secondary btn-lg">ปิดหน้าต่าง</button>
        <hr>
    </div>

    <div class="header-title">
        <h2 class="fw-bold">รายชื่อผู้เข้าร่วมกิจกรรม</h2>
        <h3 class="text-decoration-underline"><?php echo $act['act_name']; ?></h3>
        <p class="mt-2">
            วันที่จัดกิจกรรม: <?php echo date('d/m/Y', strtotime($act['act_date'])); ?> <br>
            สถานที่: <?php echo $act['act_location']; ?>
        </p>
    </div>

    <table class="table table-report">
        <thead>
            <tr class="text-center">
                <th width="5%">ลำดับ</th>
                <th width="40%">ชื่อ-นามสกุล</th>
                <th width="15%">ระดับการศึกษา</th>
                <th width="25%">วันที่/เวลา ที่บันทึก</th>
                <th width="15%">ลายเซ็น</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            if(mysqli_num_rows($res_list) > 0) {
                while($row = mysqli_fetch_array($res_list)) { 
            ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                <td class="text-center"><?php echo $row['education_level']; ?></td>
                <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['reg_date'])); ?></td>
                <td></td>
            </tr>
            <?php 
                }
            } else {
                echo '<tr><td colspan="5" class="text-center py-4">ยังไม่มีผู้เข้าร่วมกิจกรรมนี้</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="mt-5 d-flex justify-content-between">
        <div class="text-start">
            <p>สรุปจำนวนผู้เข้าร่วมทั้งหมด: <strong><?php echo mysqli_num_rows($res_list); ?></strong> คน</p>
        </div>
        <div class="text-center" style="margin-right: 50px;">
            <p>ลงชื่อ...........................................................ผู้ตรวจสอบ<br>
            (...........................................................)<br>
            วันที่ ......../......../........</p>
        </div>
    </div>
</div>

</body>
</html>