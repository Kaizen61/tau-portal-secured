-- ============================================================
-- TAU Student Portal — SECURED Version (CodeIgniter 4)
-- Full database file for direct import via phpMyAdmin / MySQL CLI.
-- This is an ALTERNATIVE to running `php spark migrate` + `php spark
-- db:seed DemoSeeder` — use this if you'd rather just import once.
-- ============================================================

CREATE DATABASE IF NOT EXISTS tau_portal_ci4;
USE tau_portal_ci4;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) DEFAULT NULL,
    student_number VARCHAR(10) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    college VARCHAR(100) DEFAULT NULL,
    year_level VARCHAR(20) DEFAULT NULL,
    failed_login_count INT DEFAULT 0,
    locked_until DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    UNIQUE KEY uniq_username (username),
    UNIQUE KEY uniq_student_number (student_number)
);

-- ------------------------------------------------------------
-- subjects
-- ------------------------------------------------------------
CREATE TABLE subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL,
    subject_name VARCHAR(150) NOT NULL,
    units INT NOT NULL DEFAULT 3,
    year_level TINYINT NOT NULL,
    semester ENUM('1st', '2nd') NOT NULL,
    teacher_id INT UNSIGNED DEFAULT NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- ------------------------------------------------------------
-- grades
-- ------------------------------------------------------------
CREATE TABLE grades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NOT NULL,
    school_year VARCHAR(9) NOT NULL,
    grade DECIMAL(4,2) DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- ------------------------------------------------------------
-- activity_logs (hash-chain audit trail — starts empty, fills up as
-- the app is used: logins, grade changes, access denials, etc.)
-- ------------------------------------------------------------
CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    username VARCHAR(50) DEFAULT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT DEFAULT NULL,
    severity ENUM('INFO', 'WARNING', 'HIGH') NOT NULL DEFAULT 'INFO',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    prev_hash CHAR(64) DEFAULT NULL,
    entry_hash CHAR(64) DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ------------------------------------------------------------
-- Seed data: users
-- Passwords are bcrypt hashes. Plaintext, for your reference only:
--   admin1 / admin123        teacher1 / teach123      teacher2 / teach123
--   Juan (2024100100) / stud123     Maria (2024100101) / stud123
--   Pedro (2025100102) / stud123
--   backup_admin (HONEYTOKEN — never use) / HoneyToken#2026!
-- ------------------------------------------------------------
INSERT INTO users (id, username, student_number, password, role, full_name, college, year_level, created_at) VALUES
(1, 'admin1', NULL, '$2b$10$DZP./je7lU8vwd3BjnRVOOhXthxVSOhiU3oHu.42q5UOZ.oewnSv6', 'admin', 'Portal Administrator', 'Office of the Registrar', NULL, NOW()),
(2, 'teacher1', NULL, '$2b$10$NVlxWyJrvG0cbU8LoHru6eailmcnfBfsXMG53GSwdQzceURoF1qWa', 'teacher', 'Prof. Ramon Dela Cruz', 'College of Information Technology', NULL, NOW()),
(3, 'teacher2', NULL, '$2b$10$NVlxWyJrvG0cbU8LoHru6eailmcnfBfsXMG53GSwdQzceURoF1qWa', 'teacher', 'Dr. Liza Fernandez', 'College of Agriculture', NULL, NOW()),
(4, NULL, '2024100100', '$2b$10$FXuq88F5Bd5DXFrMcHsNredDeC6DWRnszIT4CDlNsW6Dl2aUSfbNK', 'student', 'Juan Santos', 'College of Information Technology', '3rd Year', NOW()),
(5, NULL, '2024100101', '$2b$10$FXuq88F5Bd5DXFrMcHsNredDeC6DWRnszIT4CDlNsW6Dl2aUSfbNK', 'student', 'Maria Reyes', 'College of Information Technology', '3rd Year', NOW()),
(6, NULL, '2025100102', '$2b$10$FXuq88F5Bd5DXFrMcHsNredDeC6DWRnszIT4CDlNsW6Dl2aUSfbNK', 'student', 'Pedro Bautista', 'College of Information Technology', '2nd Year', NOW()),
(7, 'backup_admin', NULL, '$2b$10$k.3htrBfrfdwGkbmsqFgO.dEMuayLrbZ85zGSvPaIYYvNE4OGxEOK', 'admin', 'System Backup Account', NULL, NULL, NOW());

-- ------------------------------------------------------------
-- Seed data: subjects (64 total — 8 per semester across 4 years)
-- ------------------------------------------------------------
INSERT INTO subjects (subject_code, subject_name, units, year_level, semester, teacher_id) VALUES
('GE101', 'Understanding the Self', 3, 1, '1st', 2),
('GE102', 'Readings in Philippine History', 3, 1, '1st', 2),
('MATH101', 'College Algebra', 3, 1, '1st', 2),
('ENG101', 'Purposive Communication', 3, 1, '1st', 2),
('FIL101', 'Kontekstwalisadong Komunikasyon', 3, 1, '1st', 2),
('PE101', 'Physical Fitness and Wellness 1', 2, 1, '1st', 2),
('NSTP101', 'National Service Training Program 1', 3, 1, '1st', 2),
('IT101', 'Introduction to Computing', 3, 1, '1st', 2),
('GE103', 'Mathematics in the Modern World', 3, 1, '2nd', 2),
('GE104', 'Art Appreciation', 3, 1, '2nd', 2),
('MATH102', 'Plane and Spherical Trigonometry', 3, 1, '2nd', 2),
('AGRI101', 'Introduction to Agriculture', 3, 1, '2nd', 3),
('PE102', 'Physical Fitness and Wellness 2', 2, 1, '2nd', 2),
('NSTP102', 'National Service Training Program 2', 3, 1, '2nd', 2),
('IT102', 'Computer Programming 1', 3, 1, '2nd', 2),
('IT103', 'Discrete Structures 1', 3, 1, '2nd', 2),
('GE105', 'Ethics', 3, 2, '1st', 2),
('GE106', 'Science, Technology and Society', 3, 2, '1st', 2),
('PE201', 'Physical Fitness and Wellness 3', 2, 2, '1st', 2),
('IT201', 'Computer Programming 2', 3, 2, '1st', 2),
('IT202', 'Data Structures and Algorithms', 3, 2, '1st', 2),
('IT203', 'Discrete Structures 2', 3, 2, '1st', 2),
('IT204', 'Networking 1', 3, 2, '1st', 2),
('IT205', 'Human Computer Interaction', 3, 2, '1st', 2),
('GE107', 'The Contemporary World', 3, 2, '2nd', 2),
('GE108', 'Life and Works of Rizal', 3, 2, '2nd', 2),
('PE202', 'Physical Fitness and Wellness 4', 2, 2, '2nd', 2),
('IT206', 'Object-Oriented Programming', 3, 2, '2nd', 2),
('IT207', 'Networking 2', 3, 2, '2nd', 2),
('IT208', 'Information Management', 3, 2, '2nd', 2),
('IT209', 'Quantitative Methods', 3, 2, '2nd', 2),
('IT210', 'Platform Technologies', 3, 2, '2nd', 2),
('WST301', 'Web Systems and Technologies', 3, 3, '1st', 2),
('IT302', 'Systems Integration and Architecture 1', 3, 3, '1st', 2),
('IT303', 'Systems Analysis and Design', 3, 3, '1st', 2),
('IT304', 'Information Assurance and Security 1', 3, 3, '1st', 2),
('IT305', 'Advanced Database Systems', 3, 3, '1st', 2),
('IT306', 'Applications Development and Emerging Technologies', 3, 3, '1st', 2),
('IT307', 'Technopreneurship', 3, 3, '1st', 2),
('ELEC301', 'Free Elective 1', 3, 3, '1st', 2),
('IM201', 'Information Management 2', 3, 3, '2nd', 2),
('IT308', 'Systems Integration and Architecture 2', 3, 3, '2nd', 2),
('IT309', 'Information Assurance and Security 2', 3, 3, '2nd', 2),
('IT310', 'Integrative Programming and Technologies', 3, 3, '2nd', 2),
('IT311', 'Social and Professional Issues', 3, 3, '2nd', 2),
('IT312', 'Capstone Project 1 - Proposal', 3, 3, '2nd', 2),
('ELEC302', 'Free Elective 2', 3, 3, '2nd', 2),
('IT313', 'IT Elective 1', 3, 3, '2nd', 2),
('IT401', 'Capstone Project 2', 3, 4, '1st', 2),
('IT402', 'Systems Administration and Maintenance', 3, 4, '1st', 2),
('IT403', 'Advanced Elective 1', 3, 4, '1st', 2),
('IT404', 'Advanced Elective 2', 3, 4, '1st', 2),
('IT405', 'Advanced Elective 3', 3, 4, '1st', 2),
('OJT401', 'Practicum 1', 6, 4, '1st', 2),
('IT406', 'Emerging Trends in IT', 3, 4, '1st', 2),
('IT407', 'Professional Elective', 3, 4, '1st', 2),
('IT408', 'Capstone Project 3', 3, 4, '2nd', 2),
('OJT402', 'Practicum 2', 3, 4, '2nd', 2),
('IT409', 'IT Elective 2', 3, 4, '2nd', 2),
('IT410', 'IT Elective 3', 3, 4, '2nd', 2),
('IT411', 'IT Elective 4', 3, 4, '2nd', 2),
('IT412', 'Seminars and Fieldtrips', 1, 4, '2nd', 2),
('IT413', 'Comprehensive Examination Review', 3, 4, '2nd', 2),
('IT414', 'Special Topics in IT', 3, 4, '2nd', 2);

-- ------------------------------------------------------------
-- Seed data: grades (88 rows total)
--   Juan Santos  (id 4) — 6 complete/near-complete semesters (Yr1–Yr3)
--   Maria Reyes  (id 5) — 2 semesters (Yr3), 2nd sem partly ungraded
--   Pedro Bautista (id 6) — 3 semesters (Yr1–Yr2), current sem partly ungraded
-- ------------------------------------------------------------
INSERT INTO grades (student_id, subject_id, school_year, grade) VALUES
(4, 1, '2023-2024', 2.75),
(4, 2, '2023-2024', 2.25),
(4, 3, '2023-2024', 3.00),
(4, 4, '2023-2024', 2.75),
(4, 5, '2023-2024', 2.00),
(4, 6, '2023-2024', 1.00),
(4, 7, '2023-2024', 1.25),
(4, 8, '2023-2024', 1.75),
(4, 9, '2023-2024', 2.00),
(4, 10, '2023-2024', 1.25),
(4, 11, '2023-2024', 2.50),
(4, 12, '2023-2024', 2.75),
(4, 13, '2023-2024', 1.00),
(4, 14, '2023-2024', 2.25),
(4, 15, '2023-2024', 2.25),
(4, 16, '2023-2024', 2.00),
(4, 17, '2024-2025', 1.00),
(4, 18, '2024-2025', 3.00),
(4, 19, '2024-2025', 2.50),
(4, 20, '2024-2025', 2.25),
(4, 21, '2024-2025', 1.50),
(4, 22, '2024-2025', 2.50),
(4, 23, '2024-2025', 1.25),
(4, 24, '2024-2025', 1.25),
(4, 25, '2024-2025', 2.75),
(4, 26, '2024-2025', 3.00),
(4, 27, '2024-2025', 2.50),
(4, 28, '2024-2025', 3.00),
(4, 29, '2024-2025', 1.25),
(4, 30, '2024-2025', 3.00),
(4, 31, '2024-2025', 2.50),
(4, 32, '2024-2025', 1.75),
(4, 33, '2025-2026', 2.25),
(4, 34, '2025-2026', 1.75),
(4, 35, '2025-2026', 1.50),
(4, 36, '2025-2026', 2.25),
(4, 37, '2025-2026', 1.00),
(4, 38, '2025-2026', 1.75),
(4, 39, '2025-2026', 1.50),
(4, 40, '2025-2026', 3.00),
(4, 41, '2025-2026', 1.75),
(4, 42, '2025-2026', 3.00),
(4, 43, '2025-2026', 1.25),
(4, 44, '2025-2026', 2.25),
(4, 45, '2025-2026', 2.00),
(4, 46, '2025-2026', NULL),
(4, 47, '2025-2026', NULL),
(4, 48, '2025-2026', NULL),
(5, 33, '2025-2026', 1.50),
(5, 34, '2025-2026', 1.00),
(5, 35, '2025-2026', 2.25),
(5, 36, '2025-2026', 1.75),
(5, 37, '2025-2026', 1.75),
(5, 38, '2025-2026', 1.50),
(5, 39, '2025-2026', 1.00),
(5, 40, '2025-2026', 1.75),
(5, 41, '2025-2026', 2.25),
(5, 42, '2025-2026', 2.00),
(5, 43, '2025-2026', 1.00),
(5, 44, '2025-2026', 1.50),
(5, 45, '2025-2026', NULL),
(5, 46, '2025-2026', NULL),
(5, 47, '2025-2026', NULL),
(5, 48, '2025-2026', NULL),
(6, 1, '2024-2025', 2.75),
(6, 2, '2024-2025', 1.00),
(6, 3, '2024-2025', 2.75),
(6, 4, '2024-2025', 2.75),
(6, 5, '2024-2025', 1.50),
(6, 6, '2024-2025', 2.50),
(6, 7, '2024-2025', 3.00),
(6, 8, '2024-2025', 2.50),
(6, 9, '2024-2025', 1.50),
(6, 10, '2024-2025', 2.25),
(6, 11, '2024-2025', 1.50),
(6, 12, '2024-2025', 1.00),
(6, 13, '2024-2025', 1.25),
(6, 14, '2024-2025', 1.00),
(6, 15, '2024-2025', 2.75),
(6, 16, '2024-2025', 1.50),
(6, 17, '2025-2026', 1.00),
(6, 18, '2025-2026', 2.75),
(6, 19, '2025-2026', 1.50),
(6, 20, '2025-2026', 2.75),
(6, 21, '2025-2026', 2.00),
(6, 22, '2025-2026', NULL),
(6, 23, '2025-2026', NULL),
(6, 24, '2025-2026', NULL);

-- total grade rows: 88 (Juan 48, Maria 16, Pedro 24)
