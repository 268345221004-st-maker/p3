<?php
session_start();
include('db.php');
$allowed_roles = array('admin', 'teacher', 'officer');
if (!in_array($_SESSION['role'], $allowed_roles)) { 
    header("Location: login.php"); 
    exit; 
}

// ลบผู้ใช้งาน (Logic เดิม)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // ห้ามลบตัวเอง
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('ไม่สามารถลบตัวเองได้ขณะล็อกอิน'); window.location='admin_users.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM tb_user WHERE user_id = $id");
        echo "<script>window.location='admin_users.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8 Kohl">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการผู้ใช้งาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .navbar-admin { background: #1a237e; color: white; padding: 15px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .btn-action { border-radius: 8px; transition: 0.3s; }
    </style>
</head>
<body>
    <div class="navbar-admin mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h4 class="mb-0">ระบบจัดการผู้ใช้งาน</h4>
            <div>
                <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm me-2">หน้าหลัก</a>
                <a href="logout.php" class="btn btn-danger btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">รายชื่อผู้ใช้งานในระบบ</h5>
                <a href="admin_user_form.php" class="btn btn-primary btn-action shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มผู้ใช้ใหม่
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสนักศึกษา</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ระดับการศึกษา</th>
                            <th>สิทธิ์</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM tb_user ORDER BY role DESC, firstname ASC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)) {
                            $role_color = ($row['role'] == 'admin') ? 'badge bg-danger' : 'badge bg-info text-dark';
                        ?>
                        <tr>
                            <td><strong><?php echo $row['student_code'] ?: '-'; ?></strong></td>
                            <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                            <td><?php echo $row['education_level']; ?></td>
                            <td><span class="<?php echo $role_color; ?> rounded-pill px-3"><?php echo $row['role']; ?></span></td>
                            <td class="text-center">
                                <a href="admin_user_form.php?id=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-sm btn-action shadow-sm">
                                    <i class="bi bi-pencil-fill"></i> แก้ไข
                                </a>
                                <?php if($row['user_id'] != $_SESSION['user_id']) { ?>
                                    <a href="?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm btn-action shadow-sm" onclick="return confirm('ยืนยันที่จะลบผู้ใช้นี้?');">
                                        <i class="bi bi-trash-fill"></i> ลบ
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>