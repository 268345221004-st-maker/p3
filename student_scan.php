<?php
session_start();
include('db.php');

// 1. รับค่า ID กิจกรรม (ตัวอย่างเป็นเลข 3 ตาม URL ของคุณ)
$act_id = isset($_GET['act_id']) ? $_GET['act_id'] : '3'; 

// 2. สร้าง URL สำหรับ QR Code โดยใช้ API ที่เสถียร
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($act_id);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าร่วมกิจกรรม - RUS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        :root { --univ-navy: #1a237e; --univ-gold: #ffd600; }
        body { font-family: 'Kanit', sans-serif; background-color: #f4f7f6; margin: 0; }
        .navbar-custom { background: var(--univ-navy); padding: 15px; border-bottom: 4px solid var(--univ-gold); color: white; }
        .card-main { border: none; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); margin-top: -30px; background: white; z-index: 10; }
        .qr-box { border: 2px solid #f0f0f0; border-radius: 20px; padding: 20px; display: inline-block; background: #fff; }
        .btn-scan { background: var(--univ-gold); color: var(--univ-navy); border: none; border-radius: 50px; padding: 15px 30px; font-weight: 600; width: 100%; transition: 0.3s; box-shadow: 0 4px 15px rgba(255, 214, 0, 0.3); }
        .btn-scan:hover { background: #ffca28; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="navbar-custom text-center">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="student_dashboard.php" class="text-white text-decoration-none small"><i class="bi bi-chevron-left"></i> กลับหน้าหลัก</a>
            <h5 class="mb-0 fw-bold">สแกน QR Code เพื่อเช็คชื่อ</h5>
            <div class="small opacity-75">สวัสดี, <?php echo $_SESSION['fullname']; ?></div>
        </div>
    </div>

    <div class="container d-flex justify-content-center pb-5">
        <div class="col-md-6 col-lg-5">
            <div class="card card-main p-4 text-center shadow">
                <p class="text-muted mb-3 small">บันทึกรูปภาพนี้ไว้สแกน หรือใช้กล้องสแกนจากหน้าจอได้ทันที</p>
                
                <div class="qr-box mb-3">
                    <img src="<?php echo $qr_url; ?>" id="current-qr" crossorigin="anonymous" style="width: 220px; height: 220px;" alt="Activity QR Code">
                </div>
                
                <div class="mb-4">
                    <button type="button" onclick="downloadQRCode()" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-download"></i> บันทึกรูปเข้าเครื่อง
                    </button>
                </div>

                <hr class="text-muted opacity-25 mb-4">

                <h6 class="fw-bold mb-3" style="color: var(--univ-navy);">มีรูป QR Code ในอัลบั้มแล้ว?</h6>
                
                <label for="qr-file-upload" class="btn btn-scan">
                    <i class="bi bi-images me-2"></i> กดสแกน (เลือกจากอัลบั้ม)
                </label>
                <input type="file" id="qr-file-upload" accept="image/*" style="display:none">
                
                <div id="result-status" class="mt-3 small fw-bold"></div>
                <div id="qr-reader-internal" style="display:none;"></div>
            </div>
        </div>
    </div>

    <script>
        // 1. ฟังก์ชันบันทึกรูปภาพ (รองรับทั้ง PC และมือถือ)
        async function downloadQRCode() {
            const qrImage = document.getElementById('current-qr');
            try {
                const response = await fetch(qrImage.src);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "activity_qr_<?php echo $act_id; ?>.png";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } catch (err) {
                // กรณีฉุกเฉิน: เปิดรูปในแท็บใหม่เพื่อให้ผู้ใช้กดค้างเพื่อบันทึก
                window.open(qrImage.src, '_blank');
            }
        }

        // 2. ฟังก์ชันสแกนไฟล์ภาพจากอัลบั้ม
        const qrParser = new Html5Qrcode("qr-reader-internal");
        const fileSelector = document.getElementById('qr-file-upload');
        const statusDisplay = document.getElementById('result-status');

        fileSelector.addEventListener('change', event => {
            if (event.target.files.length === 0) return;
            
            const selectedFile = event.target.files[0];
            statusDisplay.innerText = "กำลังประมวลผลรูปภาพ...";
            statusDisplay.className = "mt-3 small fw-bold text-primary";

            qrParser.scanFile(selectedFile, true)
            .then(decodedText => {
                // สำเร็จ! เด้งไปหน้าส่งหลักฐาน 3 รูปทันที
                statusDisplay.innerText = "สแกนสำเร็จ! กำลังไปหน้าส่งงาน...";
                statusDisplay.className = "mt-3 small fw-bold text-success";
                
                setTimeout(() => {
                    window.location.href = "student_form.php?act_id=" + decodedText;
                }, 1000);
            })
            .catch(err => {
                statusDisplay.innerText = "ไม่พบ QR Code ในรูปภาพนี้ กรุณาใช้รูปที่ชัดเจน";
                statusDisplay.className = "mt-3 small fw-bold text-danger";
            });
        });
    </script>
</body>
</html>