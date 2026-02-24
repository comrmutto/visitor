<?php
ob_start(); // [สำคัญ] เริ่ม Buffer ทันทีเพื่อดักจับ Error หรือ Space

require_once 'config.php';
require_once 'send_email.php';
require_once 'meeting_room_emails.php';

header('Content-Type: application/json');

session_start();

// ตรวจสอบ Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean(); 
    echo json_encode(['success' => false, 'message' => 'Method not allowed']); 
    exit;
}

// ป้องกันการส่งซ้ำ
$transaction_id = md5(serialize($_POST) . time());
if (isset($_SESSION['last_transaction']) && $_SESSION['last_transaction'] === $transaction_id) {
    ob_clean(); 
    echo json_encode(['success' => true, 'message' => 'ข้อมูลถูกบันทึกแล้ว']); 
    exit;
}
$_SESSION['last_transaction'] = $transaction_id;

try {
    // รับค่าจาก $_POST
    $company_name        = trim($_POST['company_name'] ?? '');
    $visitor_name        = trim($_POST['visitor_name'] ?? '');
    $purpose             = trim($_POST['purpose'] ?? '');
    $visit_start_datetime = $_POST['visit_start_datetime'] ?? '';
    $visit_end_datetime  = $_POST['visit_end_datetime'] ?? '';
    $visit_period        = $_POST['visit_period'] ?? '';
    $visitor_type        = $_POST['visitor_type'] ?? 'Normal';
    $welcome_board       = isset($_POST['welcome_board']) ? (int)$_POST['welcome_board'] : 0;
    $factory_tour        = isset($_POST['factory_tour']) ? (int)$_POST['factory_tour'] : 0;
    $coffee_snack        = isset($_POST['coffee_snack']) ? (int)$_POST['coffee_snack'] : 0;
    $lunch               = isset($_POST['lunch']) ? (int)$_POST['lunch'] : 0;
    $has_meeting_room    = isset($_POST['meeting_room']) ? (int)$_POST['meeting_room'] : 0;
    $language            = $_POST['language'] ?? 'th';
    $microphone_request  = isset($_POST['microphone_request']) ? (int)$_POST['microphone_request'] : 0;
    $interpreter_request = isset($_POST['interpreter_request']) ? (int)$_POST['interpreter_request'] : 0;
    $meeting_date        = $has_meeting_room ? ($_POST['meeting_date'] ?? null) : null;
    $meeting_start       = $has_meeting_room ? ($_POST['meeting_start'] ?? null) : null;
    $meeting_end         = $has_meeting_room ? ($_POST['meeting_end'] ?? null) : null;
    $selected_meeting_room = $has_meeting_room ? ($_POST['meeting_room_select'] ?? null) : null;

    // รับค่า required recipients
    $required_recipients = $_POST['required_recipients'] ?? [];
    $required_emails = [];
    foreach ($required_recipients as $email) {
        $email = trim($email);
        if (!empty($email) && !in_array($email, $required_emails)) {
            $required_emails[] = $email;
        }
    }
    $required_emails_str = implode(',', $required_emails);

    // รับค่า cc recipients
    $cc_recipients = $_POST['cc_recipients'] ?? [];
    $cc_emails = [];
    foreach ($cc_recipients as $email) {
        $email = trim($email);
        if (!empty($email) && !in_array($email, $cc_emails)) {
            $cc_emails[] = $email;
        }
    }
    $cc_emails_str = implode(',', $cc_emails);

    // Validation
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
        if (empty($required_emails)) {
            throw new Exception('กรุณาเลือกอีเมลผู้รับอย่างน้อย 1 ท่าน');
        }
    }

    // Insert Database - เพิ่มฟิลด์ required_recipients และ cc_recipients
    $sql = "INSERT INTO visitors (
        company_name, visitor_name, purpose,
        visit_start_datetime, visit_end_datetime, visit_period,
        visitor_type, welcome_board, factory_tour, coffee_snack, lunch, has_meeting_room,
        meeting_date, meeting_start, meeting_end, selected_meeting_room, 
        required_recipients, cc_recipients
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

    $stmt->bind_param(
        "sssssssiiiiissssss",
        $company_name, $visitor_name, $purpose,
        $visit_start_datetime, $visit_end_datetime, $visit_period,
        $visitor_type, $welcome_board, $factory_tour, $coffee_snack, $lunch, $has_meeting_room,
        $meeting_date, $meeting_start, $meeting_end, $selected_meeting_room,
        $required_emails_str, $cc_emails_str
    );

    if (!$stmt->execute()) throw new Exception('บันทึกข้อมูลไม่สำเร็จ: ' . $stmt->error);
    $visitor_id = $stmt->insert_id;
    $stmt->close();

    // Prepare Email Data - เพิ่ม required_recipients และ cc_recipients
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
        'coffee_snack'         => $coffee_snack,
        'lunch'                => $lunch,
        'has_meeting_room'     => $has_meeting_room,
        'meeting_date'         => $meeting_date,
        'meeting_start'        => $meeting_start,
        'meeting_end'          => $meeting_end,
        'selected_meeting_room'=> $selected_meeting_room,
        'required_recipients'  => $required_emails,
        'cc_recipients'        => $cc_emails,
        'language'             => $language,
        'microphone_request'   => $microphone_request,
        'interpreter_request'  => $interpreter_request,
    ];

    // Send Email (main)
    $email_sent = sendVisitorEmail($email_data);

    // ส่งอีเมลหา IT department ถ้าเลือก Welcome Board, Factory Tour หรือ Microphone
    if ($welcome_board || $factory_tour || $microphone_request) {
        $it_result = $conn->query("SELECT email FROM email_recipients WHERE department = 'IT' AND is_active = 1");
        $it_emails = [];
        if ($it_result) {
            while ($row = $it_result->fetch_assoc()) {
                $it_emails[] = $row['email'];
            }
        }
        if (!empty($it_emails)) {
            sendDepartmentNotification($it_emails, $email_data, 'IT', $language);
        }
    }

    // ส่งอีเมลหา GA/TS department ถ้าเลือก Coffee/Snack, Lunch หรือ Interpreter
    if ($coffee_snack || $lunch || $interpreter_request) {
        $ga_result = $conn->query("SELECT email FROM email_recipients WHERE department = 'GA' AND is_active = 1");
        $ga_emails = [];
        if ($ga_result) {
            while ($row = $ga_result->fetch_assoc()) {
                $ga_emails[] = $row['email'];
            }
        }
        if (!empty($ga_emails)) {
            sendDepartmentNotification($ga_emails, $email_data, 'GA', $language);
        }
    }

    // Log Email สำหรับ Required Recipients
    if (!empty($required_emails) || ($has_meeting_room && $selected_meeting_room)) {
        $log_sql = "INSERT INTO email_logs (visitor_id, recipient_email, sent_status, sent_at, recipient_type) VALUES (?, ?, ?, NOW(), ?)";
        $log_stmt = $conn->prepare($log_sql);
        $logged = [];
        
        // Log ห้องประชุม (ถ้ามี)
        if ($has_meeting_room && $selected_meeting_room) {
            $room_email = getMeetingRoomEmail($selected_meeting_room);
            if ($room_email && !in_array($room_email, $logged)) {
                $status = $email_sent ? 'success' : 'failed';
                $type = 'meeting_room';
                $log_stmt->bind_param("isss", $visitor_id, $room_email, $status, $type);
                $log_stmt->execute();
                $logged[] = $room_email;
            }
        }
        
        // Log Required Recipients
        foreach ($required_emails as $recipient) {
            if (!in_array($recipient, $logged)) {
                $status = $email_sent ? 'success' : 'failed';
                $type = 'required';
                $log_stmt->bind_param("isss", $visitor_id, $recipient, $status, $type);
                $log_stmt->execute();
                $logged[] = $recipient;
            }
        }
        
        // Log CC Recipients
        foreach ($cc_emails as $recipient) {
            if (!in_array($recipient, $logged)) {
                $status = $email_sent ? 'success' : 'failed';
                $type = 'cc';
                $log_stmt->bind_param("isss", $visitor_id, $recipient, $status, $type);
                $log_stmt->execute();
                $logged[] = $recipient;
            }
        }
        
        $log_stmt->close();
    }

    $response = [
        'success' => true,
        'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว' . ($email_sent ? '' : ' (แต่ไม่สามารถส่งอีเมลได้)'),
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
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
?>