<?php
ob_start(); // [สำคัญ] เริ่ม Buffer ทันทีเพื่อดักจับ Error หรือ Space

require_once 'config.php';
require_once 'send_email.php';
require_once 'meeting_room_emails.php';

header('Content-Type: application/json');

session_start();

// ... (ส่วนโค้ดตรวจสอบ Request และ Transaction ID เหมือนเดิม) ...
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean(); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit;
}
$transaction_id = md5(serialize($_POST) . time());
if (isset($_SESSION['last_transaction']) && $_SESSION['last_transaction'] === $transaction_id) {
    ob_clean(); echo json_encode(['success' => true, 'message' => 'ข้อมูลถูกบันทึกแล้ว']); exit;
}
$_SESSION['last_transaction'] = $transaction_id;

try {
    // ... (ส่วนรับค่า $_POST และ Validation เหมือนเดิมทุกประการ) ...
    $company_name        = trim($_POST['company_name'] ?? '');
    $visitor_name        = trim($_POST['visitor_name'] ?? '');
    $purpose             = trim($_POST['purpose'] ?? '');
    $visit_start_datetime = $_POST['visit_start_datetime'] ?? '';
    $visit_end_datetime  = $_POST['visit_end_datetime'] ?? '';
    $visit_period        = $_POST['visit_period'] ?? '';
    $visitor_type        = $_POST['visitor_type'] ?? 'Normal';
    $welcome_board       = isset($_POST['welcome_board']) ? (int)$_POST['welcome_board'] : 0;
    $factory_tour        = isset($_POST['factory_tour']) ? (int)$_POST['factory_tour'] : 0;
    $has_meeting_room    = isset($_POST['meeting_room']) ? (int)$_POST['meeting_room'] : 0;
    $language            = $_POST['language'] ?? 'th';
    $meeting_date          = $has_meeting_room ? ($_POST['meeting_date'] ?? null) : null;
    $meeting_start         = $has_meeting_room ? ($_POST['meeting_start'] ?? null) : null;
    $meeting_end           = $has_meeting_room ? ($_POST['meeting_end'] ?? null) : null;
    $selected_meeting_room = $has_meeting_room ? ($_POST['meeting_room_select'] ?? null) : null;

    $all_recipients    = $_POST['email_recipients'] ?? [];
    $email_recipients  = [];
    foreach ($all_recipients as $email) {
        $email = trim($email);
        if (!empty($email) && !in_array($email, $email_recipients)) {
            $email_recipients[] = $email;
        }
    }
    $email_recipients_str = implode(',', $email_recipients);

    if (empty($company_name) || empty($visitor_name) || empty($purpose) || empty($visit_start_datetime) || empty($visit_end_datetime)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }
    if (strtotime($visit_start_datetime) > strtotime($visit_end_datetime)) {
        throw new Exception('วันที่สิ้นสุดต้องมากกว่าวันที่เริ่ม');
    }
    if ($has_meeting_room) {
        if (empty($meeting_date) || empty($meeting_start) || empty($meeting_end) || empty($selected_meeting_room)) {
            throw new Exception('กรุณากรอกข้อมูลการจองห้องประชุมให้ครบถ้วน');
        }
        if ($meeting_start >= $meeting_end) {
            throw new Exception('เวลาเริ่มประชุมต้องน้อยกว่าเวลาสิ้นสุดประชุม');
        }
    }

    // Insert Database
    $sql = "INSERT INTO visitors (
        company_name, visitor_name, purpose,
        visit_start_datetime, visit_end_datetime, visit_period,
        visitor_type, welcome_board, factory_tour, has_meeting_room,
        meeting_date, meeting_start, meeting_end, selected_meeting_room, email_recipients
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

    $stmt->bind_param(
        "sssssssiiisssss",
        $company_name, $visitor_name, $purpose,
        $visit_start_datetime, $visit_end_datetime, $visit_period,
        $visitor_type, $welcome_board, $factory_tour, $has_meeting_room,
        $meeting_date, $meeting_start, $meeting_end, $selected_meeting_room, $email_recipients_str
    );

    if (!$stmt->execute()) throw new Exception('บันทึกข้อมูลไม่สำเร็จ: ' . $stmt->error);
    $visitor_id = $stmt->insert_id;
    $stmt->close();

    // Prepare Email Data
    $email_data = [
        'id'                   => $visitor_id,
        'company_name'         => $company_name,
        'visitor_name'         => $visitor_name,
        'purpose'              => $purpose,
        'visit_start_datetime' => $visit_start_datetime,
        'visit_end_datetime'   => $visit_end_datetime,
        'visit_period'         => $visit_period,
        'visitor_type'         => $visitor_type,
        'welcome_board'        => $welcome_board,
        'factory_tour'         => $factory_tour,
        'has_meeting_room'     => $has_meeting_room,
        'meeting_date'         => $meeting_date,
        'meeting_start'        => $meeting_start,
        'meeting_end'          => $meeting_end,
        'selected_meeting_room'=> $selected_meeting_room,
        'email_recipients'     => $email_recipients,
        'language'             => $language
    ];

    // Send Email
    $email_sent = sendVisitorEmail($email_data);

    // Log Email
    if (!empty($email_recipients) || ($has_meeting_room && $selected_meeting_room)) {
        $log_sql = "INSERT INTO email_logs (visitor_id, recipient_email, sent_status, sent_at) VALUES (?, ?, ?, NOW())";
        $log_stmt = $conn->prepare($log_sql);
        $logged = [];
        if ($has_meeting_room && $selected_meeting_room) {
            $room_email = getMeetingRoomEmail($selected_meeting_room);
            if ($room_email && !in_array($room_email, $logged)) {
                $status = $email_sent ? 'success' : 'failed';
                $log_stmt->bind_param("iss", $visitor_id, $room_email, $status);
                $log_stmt->execute();
                $logged[] = $room_email;
            }
        }
        foreach ($email_recipients as $recipient) {
            if (!in_array($recipient, $logged)) {
                $status = $email_sent ? 'success' : 'failed';
                $log_stmt->bind_param("iss", $visitor_id, $recipient, $status);
                $log_stmt->execute();
                $logged[] = $recipient;
            }
        }
        $log_stmt->close();
    }

    $response = [
        'success' => true,
        'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว' . (isset($email_sent) && !$email_sent ? ' (แต่ไม่สามารถส่งอีเมลได้)' : ''),
        'visitor_id' => $visitor_id,
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];
}

// --- ส่วนส่งผลลัพธ์สุดท้าย ---
if (ob_get_length()) {
    ob_clean(); // ล้าง html error หรือ warning ที่อาจเกิดขึ้นก่อนหน้านี้ทิ้ง
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
exit();
?>