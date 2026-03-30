<?php
require_once 'config.php';

header('Content-Type: application/json');

// เปิดการแสดง error ชั่วคราว (ปิดเมื่อใช้งานจริง)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// ตรวจสอบพารามิเตอร์
if (!isset($_GET['term'])) {
    echo json_encode(['error' => 'Missing search term']);
    exit;
}

$search_term = '%' . $_GET['term'] . '%';

// ตรวจสอบว่าตารางมีอยู่
$table_check = $conn->query("SHOW TABLES LIKE 'email_recipients'");
if ($table_check->num_rows == 0) {
    echo json_encode(['error' => 'Table email_recipients does not exist']);
    exit;
}

// นับจำนวนข้อมูลทั้งหมด
$count_result = $conn->query("SELECT COUNT(*) as total FROM email_recipients WHERE is_active = 1");
$total = $count_result->fetch_assoc()['total'];
error_log("Total active emails: " . $total);

// ค้นหาข้อมูล
$sql = "SELECT id, email, name, department 
        FROM email_recipients 
        WHERE (email LIKE ? OR name LIKE ? OR department LIKE ?) 
        AND is_active = 1 
        ORDER BY name 
        LIMIT 20";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sss", $search_term, $search_term, $search_term);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = [
        'id' => $row['id'],
        'email' => $row['email'],           // เปลี่ยนจาก value เป็น email
        'value' => $row['email'],            // เก็บไว้ทั้งสองแบบ
        'name' => $row['name'],
        'label' => $row['name'] . ' (' . $row['email'] . ')',
        'department' => $row['department']
    ];
}

// ถ้าไม่พบข้อมูล ให้ส่ง array ว่าง
if (empty($emails)) {
    echo json_encode([]);
} else {
    echo json_encode($emails);
}

$conn->close();
?>