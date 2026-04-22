<?php
// 1. เปิดระบบแจ้งเตือน Error (สำหรับแก้ปัญหา)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. ข้อมูลการเชื่อมต่อ (ตรวจสอบความถูกต้องอีกครั้ง)
$servername = "sql304.byethost7.com";
$username = "b7_40992767";
$password = "pp268345221004";
$dbname = "b7_40992767_activity_db";

// 3. เริ่มการเชื่อมต่อ
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 4. ตรวจสอบสถานะการเชื่อมต่อ
if (!$conn) {
    echo "<div style='color:red; background:#fee; padding:20px; border:2px solid red; font-family:sans-serif;'>";
    echo "<h3 style='margin-top:0;'>❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้</h3>";
    echo "<b>สาเหตุจาก MySQL:</b> " . mysqli_connect_error();
    echo "<br><br><i>คำแนะนำ: ตรวจสอบ MySQL Hostname ใน Control Panel ของ Byet.host อีกครั้ง</i>";
    echo "</div>";
    exit;
}

// 5. ตั้งค่าภาษาไทย
mysqli_set_charset($conn, "utf8mb4");

// ทดสอบสถานะ (เปิดใช้โดยการลบ // ข้างหน้าออก)
// echo "✅ เชื่อมต่อฐานข้อมูลสำเร็จ!"; 
?>