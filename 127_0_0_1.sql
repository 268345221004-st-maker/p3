-- 1. เคลียร์ตารางเก่าออกก่อน
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tb_participation`, `tb_activity`, `tb_user`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 2. สร้างตารางผู้ใช้งาน (tb_user) - เพิ่มฟิลด์ คณะ/สาขา/อีเมล ตามไฟล์ PHP
-- --------------------------------------------------------
CREATE TABLE `tb_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_code` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `education_level` enum('ปวช.', 'ปวส.', 'ป.ตรี', 'ป.โท', 'ป.เอก') DEFAULT 'ป.ตรี',
  `role` enum('admin', 'student', 'teacher', 'officer') DEFAULT 'student',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 3. สร้างตารางกิจกรรม (tb_activity)
-- --------------------------------------------------------
CREATE TABLE `tb_activity` (
  `act_id` int(11) NOT NULL AUTO_INCREMENT,
  `act_name` varchar(200) NOT NULL,
  `act_date` date NOT NULL,
  `act_location` varchar(200) NOT NULL,
  `act_detail` text DEFAULT NULL,
  `activity_status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`act_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 4. สร้างตารางการเข้าร่วม (tb_participation)
-- --------------------------------------------------------
CREATE TABLE `tb_participation` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('wait', 'present', 'absent', 'approved', 'rejected') DEFAULT 'wait',
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `reg_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `checkin_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`part_id`),
  KEY `act_id` (`act_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 5. ข้อมูลเริ่มต้น (User: admin / Pass: 1234)
-- --------------------------------------------------------
INSERT INTO `tb_user` (`username`, `password`, `firstname`, `lastname`, `education_level`, `role`) VALUES
('admin', '1234', 'ผู้ดูแล', 'ระบบ', 'ป.ตรี', 'admin');

-- --------------------------------------------------------
-- 6. เชื่อมความสัมพันธ์ (Foreign Keys)
-- --------------------------------------------------------
ALTER TABLE `tb_participation`
  ADD CONSTRAINT `fk_act_id` FOREIGN KEY (`act_id`) REFERENCES `tb_activity` (`act_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`) ON DELETE CASCADE;

COMMIT;