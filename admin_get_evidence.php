<?php
include('db.php');
$act_id = mysqli_real_escape_string($conn, $_GET['id']);

// JOIN เพื่อดึง firstname และ lastname มาแสดงแทนไอดี
$sql = "SELECT p.*, u.firstname, u.lastname 
        FROM tb_participation p
        INNER JOIN tb_user u ON p.user_id = u.user_id
        WHERE p.act_id = '$act_id'
        ORDER BY p.reg_date DESC";
$result = mysqli_query($conn, $sql);

echo '<div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start">ชื่อ-นามสกุลจริง</th>
                    <th>รูปบรรยากาศ (1)</th>
                    <th>รูปเซลฟี่ (2)</th>
                    <th>รูปหลักฐาน (3)</th>
                    <th>เวลาที่ส่ง</th>
                </tr>
            </thead>
            <tbody>';

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        // แสดงชื่อและนามสกุลจริง
        echo '<td class="text-start fw-bold" style="color: #1a237e;">'.$row['firstname'].' '.$row['lastname'].'</td>';
        
        // แสดงรูปภาพ (คลิกที่รูปเพื่อเปิดดูรูปใหญ่)
        for($i=1; $i<=3; $i++) {
            $img = $row['img'.$i];
            echo '<td>';
            if($img) {
                echo '<a href="uploads/'.$img.'" target="_blank">
                        <img src="uploads/'.$img.'" class="img-thumb" style="width:70px; height:70px;">
                      </a>';
            } else {
                echo '<span class="text-muted small">ไม่มีรูป</span>';
            }
            echo '</td>';
        }
        
        echo '<td><small class="text-muted">'.date('d/m/Y H:i', strtotime($row['reg_date'])).'</small></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5" class="py-5 text-muted">ยังไม่มีใครลงทะเบียนและส่งรูปในกิจกรรมนี้</td></tr>';
}
echo '</tbody></table></div>';
?>