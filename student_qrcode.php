<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

if (!isset($_GET['id'])) {
    echo "ไม่พบกิจกรรม"; exit;
}

$act_id = $_GET['id'];
// สร้างข้อมูลใน QR Code ให้เป็นเลข ID ของกิจกรรม
$qr_data = $act_id; 
// ใช้ API สร้าง QR Code
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code กิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        body { font-family: 'Kanit', sans-serif; background: #f4f7f6; padding: 20px; }
        .card-custom { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: none; text-align: center; overflow: hidden; }
        .btn-navy { background-color: #1a237e; color: white; border-radius: 50px; padding: 12px 30px; width: 100%; margin-bottom: 10px; }
        .btn-gold { background-color: #ffd600; color: #1a237e; border-radius: 50px; padding: 12px 30px; width: 100%; font-weight: bold; }
        #qr-image { max-width: 100%; border: 10px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4">
            <h4 class="mb-3 text-primary">QR Code กิจกรรม</h4>
            
            <img src="<?php echo $qr_url; ?>" id="qr-image" alt="QR Code">
            
            <a href="<?php echo $qr_url; ?>" download="activity_qr.png" class="btn btn-outline-secondary btn-sm mb-4 rounded-pill">
                <i class="bi bi-download"></i> บันทึกรูปเข้าเครื่อง
            </a>

            <hr>

            <h5 class="mb-3">สแกนเพื่อเข้าร่วม</h5>
            
            <label for="qr-input-file" class="btn btn-gold shadow">
                <i class="bi bi-qr-code-scan"></i> กดสแกน (จากอัลบั้มรูป)
            </label>
            <input type="file" id="qr-input-file" accept="image/*" style="display:none">
            
            <p id="scan-status" class="mt-3 text-muted small">เลือกรูป QR Code จากเครื่องเพื่อเช็คชื่อ</p>
        </div>
    </div>
</div>

<script>
    const html5QrCode = new Html5Qrcode("reader-placeholder"); // ต้องมี instance แม้ไม่ได้ใช้กล้องสด
    const fileInput = document.getElementById('qr-input-file');
    const statusText = document.getElementById('scan-status');

    fileInput.addEventListener('change', e => {
        if (e.target.files.length == 0) {
            return;
        }

        const imageFile = e.target.files[0];
        statusText.innerText = "กำลังประมวลผล...";

        // คำสั่งสแกนรูปภาพ
        html5QrCode.scanFile(imageFile, true)
        .then(decodedText => {
            // สำเร็จ! decodedText คือเลข ID กิจกรรม
            statusText.innerText = "สแกนสำเร็จ! กำลังไปหน้าส่งงาน...";
            statusText.style.color = "green";
            
            // เด้งไปหน้าส่งรูป 3 รูป
            setTimeout(() => {
                window.location.href = "student_form.php?act_id=" + decodedText;
            }, 1000);
        })
        .catch(err => {
            // ล้มเหลว
            statusText.innerText = "สแกนไม่ผ่าน กรุณาใช้รูป QR Code ที่ชัดเจน";
            statusText.style.color = "red";
            console.log(`Error scanning file. Reason: ${err}`);
        });
    });
</script>
<div id="reader-placeholder" style="display:none;"></div>

</body>
</html>