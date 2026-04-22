<?php
session_start();
include('db.php');

$allowed_roles = array('admin', 'teacher', 'officer');
if (!in_array($_SESSION['role'], $allowed_roles)) { header("Location: login.php"); exit; }

$act_id = $_GET['id'];
$filter_role = isset($_GET['role']) ? $_GET['role'] : 'all';

// ดึงข้อมูลกิจกรรม
$act_query = mysqli_query($conn, "SELECT * FROM tb_activity WHERE act_id = $act_id");
$act_row = mysqli_fetch_array($act_query);

// หัวข้อรายงาน
$title_map = ['all'=>'ทั้งหมด (ยกเว้นผู้ดูแลระบบ)', 'student'=>'นักศึกษา', 'teacher'=>'อาจารย์', 'officer'=>'เจ้าหน้าที่'];
$display_role = $title_map[$filter_role];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานผู้เข้าร่วม - <?php echo $display_role; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; padding: 40px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; font-size: 14px; }
        th { background: #f2f2f2; }
        .img-report { width: 80px; height: 60px; object-fit: cover; margin-right: 5px; border-radius: 4px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>รายงานรายชื่อผู้เข้าร่วมกิจกรรม</h2>
        <h3>กิจกรรม: <?php echo $act_row['act_name']; ?></h3>
        <p>วันที่จัด: <?php echo date('d/m/Y', strtotime($act_row['act_date'])); ?> | สถานที่: <?php echo $act_row['act_location']; ?></p>
        <strong>กลุ่มเป้าหมาย: <?php echo $display_role; ?></strong>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ที่</th>
                <th width="15%">รหัสนักศึกษา</th>
                <th>ชื่อ-นามสกุล</th>
                <th width="10%">บทบาท</th>
                <th width="10%">สถานะ</th>
                <th width="20%">หลักฐานรูปถ่าย</th>
                <th width="15%">เวลาเช็คชื่อ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $role_sql = ($filter_role == 'all') ? " AND u.role != 'admin' " : " AND u.role = '$filter_role' ";
            
            $sql = "SELECT u.student_code, u.firstname, u.lastname, u.role, p.status, p.checkin_time, p.img1, p.img2, p.img3 
                    FROM tb_user u 
                    LEFT JOIN tb_participation p ON u.user_id = p.user_id AND p.act_id = $act_id
                    WHERE 1=1 $role_sql 
                    ORDER BY u.role DESC, u.firstname ASC";
            
            $result = mysqli_query($conn, $sql);
            $i = 1;
            while ($row = mysqli_fetch_array($result)) {
                $st = $row['status'];
                $status_label = ($st=='present') ? 'มา' : (($st=='absent') ? 'ขาด' : 'ไม่ได้ลงทะเบียน');
            ?>
            <tr>
                <td align="center"><?php echo $i++; ?></td>
                <td align="center"><strong><?php echo $row['student_code'] ?: '-'; ?></strong></td>
                <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                <td align="center"><?php echo $row['role']; ?></td>
                <td align="center" style="font-weight:bold; color:<?php echo ($st=='present')?'green':'red';?>">
                    <?php echo $status_label; ?>
                </td>
                <td align="center">
                    <?php if($row['img1']) { ?>
                        <img src="uploads/<?php echo $row['img1']; ?>" class="img-report">
                        <img src="uploads/<?php echo $row['img2']; ?>" class="img-report">
                        <img src="uploads/<?php echo $row['img3']; ?>" class="img-report">
                    <?php } else { echo "-"; } ?>
                </td>
                <td align="center">
                    <small><?php echo $row['checkin_time'] ? date('d/m/Y H:i', strtotime($row['checkin_time'])) : '-'; ?></small>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>ออกรายงานเมื่อ: <?php echo date('d/m/Y H:i'); ?></p>
        <button class="no-print" onclick="window.print()">พิมพ์รายงานนี้</button>
    </div>

</body>
</html>