<?php
/**
 * send_email.php
 * ส่งอีเมลแจ้งเตือนผู้มาติดต่อ + ส่ง Meeting Request (ICS) เข้า Outlook
 */

$autoload_path = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'meeting_room_emails.php';

// คำแปลสำหรับอีเมล 2 ภาษา
$email_translations = [
    'th' => [
        'visitor_info' => 'ข้อมูลผู้มาติดต่อ',
        'company' => 'บริษัท/หน่วยงาน',
        'visitor_name' => 'ชื่อผู้มาติดต่อ',
        'purpose' => 'วัตถุประสงค์',
        'visit_datetime' => 'วันและเวลาเข้าเยี่ยม',
        'start_date' => 'วันที่เริ่ม',
        'end_date' => 'วันที่สิ้นสุด',
        'additional_info' => 'ข้อมูลเพิ่มเติม',
        'visitor_type' => 'ประเภท',
        'vip' => '👑 VIP',
        'normal' => 'Normal',
        'welcome_board' => 'Welcome Board',
        'factory_tour' => 'Factory Tour',
        'coffee_snack' => 'กาแฟ-น้ำดื่ม',
        'lunch' => 'อาหารกลางวัน',
        'yes' => 'ต้องการ',
        'no' => 'ไม่ต้องการ',
        'meeting_details' => 'รายละเอียดการจองห้องประชุม',
        'meeting_room' => 'ห้องประชุม',
        'meeting_date' => 'วันที่จอง',
        'meeting_time' => 'เวลา',
        'start_time' => 'เวลาเริ่ม',
        'end_time' => 'เวลาสิ้นสุด',
        'required_recipients' => 'ผู้รับ (Required)',
        'cc_recipients' => 'ผู้รับสำเนา (CC)',
        'note' => 'หมายเหตุ',
        'meeting_note' => 'กรุณาตรวจสอบและเตรียมความพร้อมของห้องประชุมตามวันและเวลาที่ระบุ',
        'auto_email' => 'อีเมลนี้ส่งโดยอัตโนมัติจากระบบ VMS',
        'time' => 'เวลา'
    ],
    'en' => [
        'visitor_info' => 'Visitor Information',
        'company' => 'Company/Department',
        'visitor_name' => 'Visitor Name',
        'purpose' => 'Purpose of Visit',
        'visit_datetime' => 'Visit Date & Time',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'additional_info' => 'Additional Information',
        'visitor_type' => 'Type',
        'vip' => '👑 VIP',
        'normal' => 'Normal',
        'welcome_board' => 'Welcome Board',
        'factory_tour' => 'Factory Tour',
        'coffee_snack' => 'Coffee & Drinks',
        'lunch' => 'Lunch',
        'yes' => 'Yes',
        'no' => 'No',
        'meeting_details' => 'Meeting Room Booking Details',
        'meeting_room' => 'Meeting Room',
        'meeting_date' => 'Booking Date',
        'meeting_time' => 'Time',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'required_recipients' => 'Required Recipients',
        'cc_recipients' => 'CC Recipients',
        'note' => 'Note',
        'meeting_note' => 'Please check and prepare the meeting room according to the specified date and time',
        'auto_email' => 'This email is automatically sent by VMS System',
        'time' => 'Time'
    ]
];

// ============================================================
// ฟังก์ชันหลัก: ส่งอีเมลแจ้งเตือนผู้มาติดต่อ
// ============================================================

function sendVisitorEmail($visitor_data) {
    require_once 'config.php';

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer not found.');
        return false;
    }

    try {
        $lang = $visitor_data['language'] ?? 'th';
        
        // เตรียมผู้รับทั้งหมด
        $all_recipients = [];
        
        // เพิ่มห้องประชุม (ถ้ามี)
        if (!empty($visitor_data['has_meeting_room']) && !empty($visitor_data['selected_meeting_room'])) {
            $room_email = getMeetingRoomEmail($visitor_data['selected_meeting_room']);
            if ($room_email) {
                $all_recipients[] = $room_email;
            }
        }
        
        // เพิ่ม Required Recipients (To)
        if (!empty($visitor_data['required_recipients'])) {
            foreach ($visitor_data['required_recipients'] as $email) {
                $email = trim($email);
                if (!empty($email) && !in_array($email, $all_recipients)) {
                    $all_recipients[] = $email;
                }
            }
        }
        
        // ถ้ามีผู้รับ ให้ส่งอีเมลครั้งเดียว
        if (!empty($all_recipients)) {
            $subject = ($lang === 'th') 
                ? "แจ้งเตือน: ผู้มาติดต่อใหม่ - " . $visitor_data['visitor_name']
                : "Notification: New Visitor - " . $visitor_data['visitor_name'];
            
            $body = createEmailContent($visitor_data, $lang);
            
            // สร้าง ICS ถ้ามีการจองห้องประชุม
            $ics_content = null;
            if (!empty($visitor_data['has_meeting_room'])) {
            $ics_content = generateICS($visitor_data, $all_recipients, $visitor_data['cc_recipients'] ?? []);
                $subject = ($lang === 'th')
                    ? "Meeting Request: " . $visitor_data['visitor_name'] . " - " . $visitor_data['company_name']
                    : "Meeting Request: " . $visitor_data['visitor_name'] . " - " . $visitor_data['company_name'];
            }
            
            // ส่งอีเมลโดยแยก Required และ CC
            return _sendSMTPWithCC($all_recipients, $visitor_data['cc_recipients'] ?? [], $subject, $body, $ics_content, $lang);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error in sendVisitorEmail: " . $e->getMessage());
        return false;
    }
}

// ============================================================
// ฟังก์ชันสร้างเนื้อหา iCalendar (.ics) พร้อม attendees ทั้งหมด
// ============================================================
function generateICS($data, $all_attendees = [], $cc_attendees = []) {
    $start_str = $data['meeting_date'] . ' ' . $data['meeting_start'];
    $end_str   = $data['meeting_date'] . ' ' . $data['meeting_end'];
    
    // แปลงเวลาไทย (GMT+7) เป็น UTC (GMT+0) สำหรับไฟล์ ICS
    $timezone_th = new DateTimeZone('Asia/Bangkok');
    $timezone_utc = new DateTimeZone('UTC');

    $dt_start = new DateTime($start_str, $timezone_th);
    $dt_start->setTimezone($timezone_utc);
    
    $dt_end = new DateTime($end_str, $timezone_th);
    $dt_end->setTimezone($timezone_utc);

    $dt_start_fmt = $dt_start->format('Ymd\THis\Z');
    $dt_end_fmt   = $dt_end->format('Ymd\THis\Z');
    $dt_now_fmt   = gmdate('Ymd\THis\Z');

    $uid = uniqid('vms_') . '@marugo-rubber.co.th';
    $summary = "Visitor: " . $data['visitor_name'] . " (" . $data['company_name'] . ")";
    $description = "Topic: " . $data['purpose'] . "\\nVisitor Type: " . $data['visitor_type'];
    $location = $data['selected_meeting_room'];
    
    // ICS Format
    $ics = "BEGIN:VCALENDAR\r\n";
    $ics .= "VERSION:2.0\r\n";
    $ics .= "PRODID:-//Marugo Rubber//VMS System//EN\r\n";
    $ics .= "METHOD:REQUEST\r\n"; 
    $ics .= "BEGIN:VEVENT\r\n";
    $ics .= "UID:$uid\r\n";
    $ics .= "DTSTAMP:$dt_now_fmt\r\n";
    $ics .= "DTSTART:$dt_start_fmt\r\n";
    $ics .= "DTEND:$dt_end_fmt\r\n";
    $ics .= "SUMMARY:$summary\r\n";
    $ics .= "DESCRIPTION:$description\r\n";
    $ics .= "LOCATION:$location\r\n";
    $ics .= "ORGANIZER;CN=VMS System:MAILTO:" . SMTP_FROM . "\r\n";
    
    // เพิ่ม Required/Room attendees (REQ-PARTICIPANT)
    $added_attendees = [];
    foreach ($all_attendees as $attendee) {
        $attendee_clean = strtolower(trim($attendee));
        if (empty($attendee_clean) || in_array($attendee_clean, $added_attendees)) continue;
        $added_attendees[] = $attendee_clean;
        $ics .= "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$attendee}:MAILTO:{$attendee}\r\n";
    }
    
    // เพิ่ม CC attendees (OPT-PARTICIPANT)
    foreach ($cc_attendees as $attendee) {
        $attendee = trim($attendee);
        $attendee_clean = strtolower($attendee);
        if (empty($attendee_clean) || in_array($attendee_clean, $added_attendees)) continue;
        $added_attendees[] = $attendee_clean;
        $ics .= "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=OPT-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$attendee}:MAILTO:{$attendee}\r\n";
    }
    
    $ics .= "END:VEVENT\r\n";
    $ics .= "END:VCALENDAR\r\n";

    return $ics;
}

// ============================================================
// ฟังก์ชัน internal: ส่ง SMTP พร้อมแยก To และ CC
// ============================================================
function _sendSMTPWithCC(array $to_list, array $cc_list, string $subject, string $html_body, $ical_content = null, $lang = 'th'): bool {
    $mail = new PHPMailer(true);

    try {
        // --- ตั้งค่า SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mrtsmtp@marugo-rubber.co.th';
        $mail->Password   = 'Msle254893';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // --- ผู้ส่ง ---
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);

        // --- ผู้รับหลัก (To) ---
        $unique_to = array_unique($to_list);
        foreach ($unique_to as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($email);
            }
        }

        // --- ผู้รับสำเนา (CC) ---
        $unique_cc = array_unique($cc_list);
        foreach ($unique_cc as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // ตรวจสอบว่าไม่อยู่ใน To แล้ว
                if (!in_array($email, $unique_to)) {
                    $mail->addCC($email);
                }
            }
        }

        if (empty($mail->getToAddresses())) {
            return false;
        }

        // --- เนื้อหาอีเมล ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</div>', '<br/>'], "\n", $html_body));

        // --- ถ้ามีข้อมูล ICS ให้ใส่ใน property Ical ---
        if ($ical_content) {
            $mail->Ical = $ical_content;
        }

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer Error [{$subject}]: " . $mail->ErrorInfo);
        return false;
    }
}

// ============================================================
// ฟังก์ชันสร้างเนื้อหาอีเมล (รองรับ 2 ภาษาและปรับ CSS)
// ============================================================
function createEmailContent($visitor_data, $lang = 'th') {
    global $email_translations;
    $t = $email_translations[$lang];
    
    $meeting_room_name = htmlspecialchars($visitor_data['selected_meeting_room'] ?? '');
    $company_name      = htmlspecialchars($visitor_data['company_name'] ?? '');
    $visitor_name      = htmlspecialchars($visitor_data['visitor_name'] ?? '');
    $purpose           = htmlspecialchars($visitor_data['purpose'] ?? '');
    $visitor_type      = $visitor_data['visitor_type'] ?? 'Normal';
    $welcome_board     = !empty($visitor_data['welcome_board']);
    $factory_tour      = !empty($visitor_data['factory_tour']);
    $coffee_snack      = !empty($visitor_data['coffee_snack']);
    $lunch             = !empty($visitor_data['lunch']);
    $has_meeting_room  = !empty($visitor_data['has_meeting_room']);

    $start_fmt = !empty($visitor_data['visit_start_datetime'])
        ? date('d/m/Y H:i', strtotime($visitor_data['visit_start_datetime'])) : '—';
    $end_fmt   = !empty($visitor_data['visit_end_datetime'])
        ? date('d/m/Y H:i', strtotime($visitor_data['visit_end_datetime'])) : '—';

    // ---- CSS ที่ปรับปรุงใหม่ให้อ่านง่าย ----
    $css = "
        body { 
            font-family: 'Sarabun', 'Segoe UI', Arial, sans-serif !important; 
            background-color: #f4f7fc !important; 
            margin: 0 !important; 
            padding: 20px !important;
            line-height: 1.6 !important;
            color: #222222 !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        * { box-sizing: border-box; }
        .email-wrapper { 
            max-width: 650px !important; 
            margin: 0 auto !important; 
            background-color: #ffffff !important; 
            border-radius: 12px !important; 
            overflow: hidden !important; 
            border: 1px solid #dde3ed !important;
        }
        .email-header { 
            padding: 30px 35px !important; 
            text-align: center !important; 
            background: linear-gradient(135deg, #0B6B4A 0%, #1B4D8A 100%) !important;
        }
        .email-header h2 { 
            margin: 0 0 8px 0 !important; 
            font-size: 24px !important; 
            font-weight: 700 !important;
            color: #000816ff !important;
        }
        .email-header p { 
            margin: 0 !important; 
            font-size: 15px !important; 
            color: #000816ff !important;
        }
        .email-body { 
            padding: 30px 35px !important; 
            background-color: #ffffff !important;
        }
        .info-section { 
            margin-bottom: 24px !important; 
            background-color: #f5f7fb !important;
            border-radius: 10px !important;
            padding: 20px !important;
            border: 1px solid #dde3ed !important;
        }
        .info-section h3 { 
            color: #1a2b45 !important; 
            font-size: 16px !important; 
            font-weight: 700 !important;
            border-left: 5px solid #3498db !important;
            padding-left: 12px !important;
            margin: 0 0 16px 0 !important;
            background-color: transparent !important;
        }
        .info-row { 
            width: 100% !important;
            margin-bottom: 10px !important; 
            font-size: 14px !important;
            display: block !important;
        }
        .info-label { 
            font-weight: 700 !important; 
            color: #4a5568 !important;
            display: inline-block !important;
            min-width: 160px !important;
            vertical-align: top !important;
        }
        .info-value { 
            color: #1a202c !important; 
            display: inline-block !important;
            font-weight: 500 !important;
        }
        .badge { 
            display: inline-block !important; 
            padding: 4px 12px !important; 
            border-radius: 50px !important; 
            font-size: 13px !important; 
            font-weight: 700 !important; 
        }
        .badge-vip { 
            background-color: #fef5e7 !important; 
            color: #92400e !important; 
            border: 1px solid #f9d67a !important;
        }
        .badge-normal { 
            background-color: #e8f0fe !important; 
            color: #1e40af !important; 
            border: 1px solid #93c5fd !important;
        }
        .badge-yes {
            display: inline-block !important;
            background-color: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #6ee7b7 !important;
            padding: 3px 10px !important;
            border-radius: 30px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
        }
        .badge-no {
            display: inline-block !important;
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fca5a5 !important;
            padding: 3px 10px !important;
            border-radius: 30px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
        }
        .meeting-box { 
            background-color: #ffffff !important; 
            border: 2px solid #dde3ed !important; 
            border-radius: 10px !important; 
            padding: 20px !important; 
            margin-top: 12px !important;
        }
        .meeting-box h4 { 
            margin: 0 0 16px 0 !important; 
            color: #065f46 !important; 
            font-size: 16px !important; 
            font-weight: 700 !important;
            border-bottom: 2px solid #dde3ed !important;
            padding-bottom: 10px !important;
            background-color: transparent !important;
        }
        .highlight { 
            background-color: #dbeafe !important; 
            padding: 3px 10px !important; 
            border-radius: 20px !important; 
            font-weight: 700 !important; 
            color: #1e3a8a !important; 
            display: inline-block !important;
            font-size: 13px !important;
        }
        .note-box { 
            background-color: #fffbeb !important; 
            border: 1px solid #fcd34d !important; 
            color: #78350f !important; 
            padding: 14px 18px !important; 
            border-radius: 10px !important; 
            margin: 20px 0 10px 0 !important; 
            font-size: 14px !important;
        }
        .chip-container {
            margin-top: 10px !important;
        }
        .chip { 
            display: inline-block !important; 
            background-color: #e2e8f0 !important; 
            color: #2d3748 !important; 
            border-radius: 30px !important; 
            padding: 5px 14px !important; 
            font-size: 13px !important; 
            font-weight: 500 !important;
            border: 1px solid #cbd5e0 !important;
            margin: 3px 4px 3px 0 !important;
        }
        .chip-required { 
            background-color: #dbeafe !important; 
            border-color: #3b82f6 !important;
            color: #1e3a8a !important;
            font-weight: 600 !important;
        }
        .chip-cc { 
            background-color: #e2e8f0 !important; 
            border-color: #94a3b8 !important;
        }
        .email-footer { 
            text-align: center !important; 
            padding: 18px 35px !important; 
            background-color: #f0f4f8 !important; 
            color: #4a5568 !important; 
            font-size: 12px !important; 
            border-top: 1px solid #dde3ed !important; 
        }
        .separator {
            height: 1px !important;
            background-color: #e2e8f0 !important;
            margin: 16px 0 !important;
            border: none !important;
        }
    ";

    // ---- Header ----
    $header_html = "
        <div class='email-header'>
            <h2>📅 " . ($lang === 'th' ? 'แจ้งเตือนการนัดหมาย' : 'Meeting Notification') . "</h2>
            <p>" . ($lang === 'th' ? "รายละเอียดผู้มาติดต่อและห้องประชุม" : "Visitor and meeting details") . "</p>
        </div>";

    // ---- Badges ----
    $vip_badge = $visitor_type === 'VIP'
        ? "<span class='badge badge-vip'>👑 {$t['vip']}</span>"
        : "<span class='badge badge-normal'>{$t['normal']}</span>";

    $wb_badge = $welcome_board 
        ? "<span class='badge-yes'>✅ {$t['yes']}</span>" 
        : "<span class='badge-no'>❌ {$t['no']}</span>";
    
    $ft_badge = $factory_tour 
        ? "<span class='badge-yes'>✅ {$t['yes']}</span>" 
        : "<span class='badge-no'>❌ {$t['no']}</span>";
    
    $coffee_badge = $coffee_snack 
        ? "<span class='badge-yes'>✅ {$t['yes']}</span>" 
        : "<span class='badge-no'>❌ {$t['no']}</span>";
    
    $lunch_badge = $lunch 
        ? "<span class='badge-yes'>✅ {$t['yes']}</span>" 
        : "<span class='badge-no'>❌ {$t['no']}</span>";

    // ---- Meeting room section ----
    $meeting_section = '';
    if ($has_meeting_room) {
        $m_date  = !empty($visitor_data['meeting_date'])
            ? date('d/m/Y', strtotime($visitor_data['meeting_date'])) : '—';
        $m_start = htmlspecialchars($visitor_data['meeting_start'] ?? '—');
        $m_end   = htmlspecialchars($visitor_data['meeting_end']   ?? '—');
        
        $meeting_section = "
        <div class='info-section'>
            <h3>🏢 {$t['meeting_details']}</h3>
            <div class='meeting-box'>
                <h4>{$meeting_room_name}</h4>
                <div class='info-row'>
                    <span class='info-label'>{$t['meeting_date']}:</span>
                    <span class='info-value'><span class='highlight'>{$m_date}</span></span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>{$t['start_time']}:</span>
                    <span class='info-value'><span class='highlight'>{$m_start} " . ($lang === 'th' ? 'น.' : '') . "</span></span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>{$t['end_time']}:</span>
                    <span class='info-value'><span class='highlight'>{$m_end} " . ($lang === 'th' ? 'น.' : '') . "</span></span>
                </div>
            </div>
        </div>";
    }

    // ---- Required recipients section ----
    $required_section = '';
    if (!empty($visitor_data['required_recipients'])) {
        $chips = '';
        foreach ($visitor_data['required_recipients'] as $email) {
            $chips .= "<span class='chip chip-required'>📧 " . htmlspecialchars(trim($email)) . "</span>";
        }
        $required_section = "
        <div class='info-section'>
            <h3>📧 {$t['required_recipients']}</h3>
            <div class='chip-container'>{$chips}</div>
        </div>";
    }

    // ---- CC recipients section ----
    $cc_section = '';
    if (!empty($visitor_data['cc_recipients'])) {
        $chips = '';
        foreach ($visitor_data['cc_recipients'] as $email) {
            $chips .= "<span class='chip chip-cc'>📨 " . htmlspecialchars(trim($email)) . "</span>";
        }
        $cc_section = "
        <div class='info-section'>
            <h3>📨 {$t['cc_recipients']}</h3>
            <div class='chip-container'>{$chips}</div>
        </div>";
    }

    // ---- Meeting room note ----
    $note_section = '';
    if ($has_meeting_room) {
        $note_section = "
        <div class='note-box'>
            <strong>📌 {$t['note']}:</strong> {$t['meeting_note']}
        </div>";
    }

    $sent_time = date('d/m/Y H:i:s');

    // ---- Assemble full HTML ----
    return "<!DOCTYPE html>
<html lang='{$lang}'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>" . ($lang === 'th' ? 'แจ้งเตือนการนัดหมาย' : 'Meeting Notification') . "</title>
    <style>{$css}</style>
</head>
<body>
<div class='email-wrapper'>

    {$header_html}

    <div class='email-body'>

        <div class='info-section'>
            <h3>📋 {$t['visitor_info']}</h3>
            <div class='info-row'>
                <span class='info-label'>{$t['company']}:</span>
                <span class='info-value'><strong>{$company_name}</strong></span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['visitor_name']}:</span>
                <span class='info-value'><strong>{$visitor_name}</strong></span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['purpose']}:</span>
                <span class='info-value'>{$purpose}</span>
            </div>
        </div>

        <div class='info-section'>
            <h3>⏰ {$t['visit_datetime']}</h3>
            <div class='info-row'>
                <span class='info-label'>{$t['start_date']}:</span>
                <span class='info-value'><span class='highlight'>{$start_fmt}</span></span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['end_date']}:</span>
                <span class='info-value'><span class='highlight'>{$end_fmt}</span></span>
            </div>
        </div>

        <div class='info-section'>
            <h3>🏷️ {$t['additional_info']}</h3>
            <div class='info-row'>
                <span class='info-label'>{$t['visitor_type']}:</span>
                <span class='info-value'>{$vip_badge}</span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['welcome_board']}:</span>
                <span class='info-value'>{$wb_badge}</span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['factory_tour']}:</span>
                <span class='info-value'>{$ft_badge}</span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['coffee_snack']}:</span>
                <span class='info-value'>{$coffee_badge}</span>
            </div>
            <div class='info-row'>
                <span class='info-label'>{$t['lunch']}:</span>
                <span class='info-value'>{$lunch_badge}</span>
            </div>
        </div>

        {$meeting_section}
        {$required_section}
        {$cc_section}
        {$note_section}

        <div class='separator'></div>

    </div>

    <div class='email-footer'>
        {$t['auto_email']} | {$t['time']}: {$sent_time} " . ($lang === 'th' ? 'น.' : '') . "
    </div>

</div>
</body>
</html>";

} // Added closing brace here

// ============================================================
// ฟังก์ชันส่งอีเมลแจ้ง department เฉพาะ (IT / GA / TS)
// ============================================================
function sendDepartmentNotification(array $to_emails, array $visitor_data, string $dept, string $lang = 'th') {
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) return false;

    global $email_translations;
    $t = $email_translations[$lang];

    $company_name  = htmlspecialchars($visitor_data['company_name'] ?? '');
    $visitor_name  = htmlspecialchars($visitor_data['visitor_name'] ?? '');
    $purpose       = htmlspecialchars($visitor_data['purpose'] ?? '');
    $welcome_board = !empty($visitor_data['welcome_board']);
    $factory_tour  = !empty($visitor_data['factory_tour']);
    $coffee_snack  = !empty($visitor_data['coffee_snack']);
    $lunch         = !empty($visitor_data['lunch']);

    $start_fmt = !empty($visitor_data['visit_start_datetime'])
        ? date('d/m/Y H:i', strtotime($visitor_data['visit_start_datetime'])) : '—';
    $end_fmt   = !empty($visitor_data['visit_end_datetime'])
        ? date('d/m/Y H:i', strtotime($visitor_data['visit_end_datetime'])) : '—';

    // 🌟 ส่วนที่เพิ่มใหม่: ถ้าเป็น GA ให้เพิ่มอีเมลแผนก TS เข้าไปด้วย
    if ($dept === 'GA') {
        require_once 'config.php';
        global $conn;
        if ($conn) {
            $ts_query = "SELECT email FROM email_recipients WHERE department = 'TS' AND is_active = 1";
            $ts_result = $conn->query($ts_query);
            if ($ts_result) {
                while ($row = $ts_result->fetch_assoc()) {
                    $ts_email = trim($row['email']);
                    if (!empty($ts_email) && !in_array($ts_email, $to_emails)) {
                        $to_emails[] = $ts_email; // นำอีเมล TS เพิ่มเข้าไปในกลุ่มผู้รับ
                    }
                }
            }
        }
        $dept_display = 'GA / TS'; // เปลี่ยนชื่อแสดงผล
    } else {
        $dept_display = $dept;
    }

    // 🌟 กำหนดหัวข้อตามแผนก
    if ($dept === 'IT') {
        $dept_title = ($lang === 'th') ? 'แผนก IT — กรุณาเตรียมการ' : 'IT Dept — Please Prepare';
        $items_html = '';
        if ($welcome_board) $items_html .= '<li>✅ Welcome Board</li>';
        if ($factory_tour)  $items_html .= '<li>✅ Factory Tour</li>';
        $dept_color = '#1B4D8A';
    } else { // กรณีเป็น GA / TS
        $dept_title = ($lang === 'th') ? 'แผนก GA — กรุณาเตรียมการ' : 'GA Dept — Please Prepare';
        $items_html = '';
        if ($coffee_snack) $items_html .= '<li>✅ ' . ($lang === 'th' ? 'กาแฟ-น้ำดื่ม (Coffee & Drinks)' : 'Coffee & Drinks') . '</li>';
        if ($lunch)        $items_html .= '<li>✅ ' . ($lang === 'th' ? 'อาหารกลางวัน (Lunch)' : 'Lunch') . '</li>';
        $dept_color = '#0B6B4A';
    }

    // 🌟 ตั้งชื่อ Subject ให้ครอบคลุม
    $subject = ($lang === 'th')
        ? "[{$dept_display}] เตรียมการต้อนรับผู้มาติดต่อ: {$visitor_name}"
        : "[{$dept_display}] Visitor Preparation Required: {$visitor_name}";

    $lbl_company    = $lang === 'th' ? 'บริษัท/หน่วยงาน' : 'Company';
    $lbl_visitor    = $lang === 'th' ? 'ชื่อผู้มาติดต่อ' : 'Visitor Name';
    $lbl_purpose    = $lang === 'th' ? 'วัตถุประสงค์' : 'Purpose';
    $lbl_start      = $lang === 'th' ? 'วันที่เริ่ม' : 'Start';
    $lbl_end        = $lang === 'th' ? 'วันที่สิ้นสุด' : 'End';
    $lbl_visitor_info = $lang === 'th' ? 'ข้อมูลผู้มาติดต่อ' : 'Visitor Information';
    $lbl_prepare    = $lang === 'th' ? 'รายการที่ต้องเตรียม' : 'Items to Prepare';
    $lbl_subtitle   = $lang === 'th' ? 'มีผู้มาติดต่อที่ต้องการการเตรียมการจากท่าน' : 'A visitor requires your preparation';
    $lbl_auto       = $lang === 'th' ? 'อีเมลนี้ส่งโดยอัตโนมัติจากระบบ VMS' : 'This email is automatically sent by VMS System';
    $sent_time = date('d/m/Y H:i:s');

$body = "<!DOCTYPE html>
<html lang='{$lang}'>
<head>
<meta charset='UTF-8'>
<style>
/* ตั้งค่าพื้นฐานเผื่ออีเมลบางระบบอ่านค่าได้ */
body { font-family: 'Sarabun', 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background-color: #e2e8f0; }
.lbl { font-weight: bold; color: #4a5568; width: 140px; vertical-align: top; padding-bottom: 12px; }
.val { color: #0f172a; padding-bottom: 12px; }
ul { margin: 10px 0 0 20px; padding: 0; color: #047857; font-size: 16px; }
li { margin-bottom: 8px; }
</style>
</head>
<body style='background-color: #e2e8f0; padding: 20px;'>

<table align='center' width='100%' style='max-width: 600px; background-color: #f1f5f9; border-radius: 12px; border: 1px solid #cbd5e1; border-spacing: 0; border-collapse: separate; overflow: hidden; margin: 0 auto;' cellpadding='0' cellspacing='0'>
    
    <tr>
        <td bgcolor='#ffffff' style='padding: 25px 30px; text-align: center; background-color: #ffffff; border-bottom: 3px dashed #cbd5e1;'>
            <h2 style='margin: 0; font-size: 26px; color: #1e293b;'>🔔 {$dept_title}</h2>
            <p style='margin: 8px 0 0; color: #64748b; font-size: 16px;'>{$lbl_subtitle}</p>
        </td>
    </tr>
    
    <tr>
        <td bgcolor='#f1f5f9' style='padding: 30px; background-color: #f1f5f9;'>
            
            <table width='100%' style='background-color: #ffffff; border-radius: 8px; border-left: 6px solid #1e293b; margin-bottom: 25px; border-spacing: 0; border-collapse: separate; box-shadow: 0 2px 4px rgba(0,0,0,0.02);' cellpadding='0' cellspacing='0'>
                <tr>
                    <td bgcolor='#ffffff' style='padding: 20px; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;'>
                        <h3 style='margin: 0 0 15px; font-size: 20px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;'>📋 {$lbl_visitor_info}</h3>
                        
                        <table width='100%' cellpadding='0' cellspacing='0' style='font-size: 16px;'>
                            <tr>
                                <td class='lbl'>{$lbl_company}:</td>
                                <td class='val'><strong>{$company_name}</strong></td>
                            </tr>
                            <tr>
                                <td class='lbl'>{$lbl_visitor}:</td>
                                <td class='val'><strong>{$visitor_name}</strong></td>
                            </tr>
                            <tr>
                                <td class='lbl'>{$lbl_purpose}:</td>
                                <td class='val'>{$purpose}</td>
                            </tr>
                            <tr>
                                <td class='lbl'>{$lbl_start}:</td>
                                <td class='val'>{$start_fmt}</td>
                            </tr>
                            <tr>
                                <td class='lbl' style='padding-bottom: 0;'>{$lbl_end}:</td>
                                <td class='val' style='padding-bottom: 0;'>{$end_fmt}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table width='100%' style='background-color: #ffffff; border-radius: 8px; border-left: 6px solid #1e293b; border-spacing: 0; border-collapse: separate; box-shadow: 0 2px 4px rgba(0,0,0,0.02);' cellpadding='0' cellspacing='0'>
                <tr>
                    <td bgcolor='#ffffff' style='padding: 20px; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;'>
                        <h3 style='margin: 0 0 15px; font-size: 20px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;'>📌 {$lbl_prepare}</h3>
                        <div style='font-size: 16px; color: #047857;'>
                            <ul>{$items_html}</ul>
                        </div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    
    <tr>
        <td bgcolor='#ffffff' style='padding: 15px; text-align: center; background-color: #ffffff; color: #64748b; font-size: 14px; border-top: 3px dashed #cbd5e1;'>
            {$lbl_auto} | {$sent_time}
        </td>
    </tr>
</table>

</body>
</html>";

    // ตรวจสอบก่อนส่งเพื่อไม่ให้ระบบพยายามส่งอีเมลถ้าไม่มีผู้รับ
    if (empty($to_emails)) {
        return false;
    }

    return _sendSMTPWithCC($to_emails, [], $subject, $body, null, $lang);
}