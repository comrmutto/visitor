<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '13792846');
define('DB_NAME', 'visitor_system');

// Email configuration
define('SMTP_HOST', 'smtp.office365.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'mrtsmtp@marugo-rubber.co.th');
define('SMTP_PASS', 'Msle254893');
define('SMTP_FROM', 'mrtsmtp@marugo-rubber.co.th');
define('SMTP_FROM_NAME', 'Visitor System');

// Meeting Room Email Configuration
define('MEETING_ROOM_1_EMAIL', 'MeetingRoom@marugo-rubber.co.th');
define('MEETING_ROOM_2_EMAIL', 'MeetingRoom2@marugo-rubber.co.th');
define('MEETING_ROOM_3_EMAIL', 'MeetingRoom3@marugo-rubber.co.th');
define('MEETING_ROOM_4_EMAIL', 'MeetingRoom4@marugo-rubber.co.th');

// ============================================================
// กำหนด credentials (ถ้ายังไม่มีใน config.php ให้เพิ่มเอง)
// ============================================================
if (!defined('ADMIN_USER')) define('ADMIN_USER', 'admin');
// Default password: Admin@1234  (ควรเปลี่ยนและย้ายไปใส่ใน config.php)
if (!defined('ADMIN_PASS')) define('ADMIN_PASS', '$2a$12$/lNhz5TD1VVlbpAfcSZTXOGXt0wzzMeLjjjUqPhw2x0XBTDCCz9VW');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
