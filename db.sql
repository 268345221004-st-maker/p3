CREATE DATABASE activity_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE activity_db;

-- ตารางผู้ใช้งาน
CREATE TABLE tb_user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    education_level ENUM('ปวช.', 'ปวส.', 'ป.ตรี', 'ป.โท', 'ป.เอก') NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student'
);

-- เพิ่ม Admin (pass: 1234) และ นักศึกษาตัวอย่าง
INSERT INTO tb_user (username, password, firstname, lastname, education_level, role) VALUES 
('admin', '1234', 'Admin', 'System', 'ป.ตรี', 'admin'),
('student1', '1234', 'สมชาย', 'ใจดี', 'ปวส.', 'student'),
('student2', '1234', 'สมหญิง', 'เรียนเก่ง', 'ป.ตรี', 'student');

-- ตารางกิจกรรม
CREATE TABLE tb_activity (
    act_id INT AUTO_INCREMENT PRIMARY KEY,
    act_name VARCHAR(200) NOT NULL,
    act_date DATE NOT NULL,
    act_location VARCHAR(200) NOT NULL,
    act_detail TEXT
);

-- ตารางการเข้าร่วมกิจกรรม (เช็คชื่อ)
CREATE TABLE tb_participation (
    part_id INT AUTO_INCREMENT PRIMARY KEY,
    act_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('wait', 'present', 'absent') DEFAULT 'wait',
    FOREIGN KEY (act_id) REFERENCES tb_activity(act_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES tb_user(user_id) ON DELETE CASCADE
);