<?php
// meeting_room_emails.php
require_once 'config.php';

function getMeetingRoomEmail($room_name) {
    $room_emails = [
        'Meeting Room 1' => MEETING_ROOM_1_EMAIL,
        'Meeting Room 2' => MEETING_ROOM_2_EMAIL,
        'Meeting Room 3' => MEETING_ROOM_3_EMAIL,
        'Meeting Room 4' => MEETING_ROOM_4_EMAIL
    ];
    
    return isset($room_emails[$room_name]) ? $room_emails[$room_name] : null;
}

function getMeetingRoomName($room_email) {
    $room_names = [
        MEETING_ROOM_1_EMAIL => 'Meeting Room 1',
        MEETING_ROOM_2_EMAIL => 'Meeting Room 2',
        MEETING_ROOM_3_EMAIL => 'Meeting Room 3',
        MEETING_ROOM_4_EMAIL => 'Meeting Room 4'
    ];
    
    return isset($room_names[$room_email]) ? $room_names[$room_email] : null;
}

function getAllMeetingRooms() {
    return [
        ['name' => 'Meeting Room 1', 'email' => MEETING_ROOM_1_EMAIL],
        ['name' => 'Meeting Room 2', 'email' => MEETING_ROOM_2_EMAIL],
        ['name' => 'Meeting Room 3', 'email' => MEETING_ROOM_3_EMAIL],
        ['name' => 'Meeting Room 4', 'email' => MEETING_ROOM_4_EMAIL]
    ];
}

// ตรวจสอบว่าอีเมลห้องประชุมซ้อนทับกับอีเมลผู้รับสำเนาหรือไม่
function isMeetingRoomEmail($email) {
    $room_emails = [
        MEETING_ROOM_1_EMAIL,
        MEETING_ROOM_2_EMAIL,
        MEETING_ROOM_3_EMAIL,
        MEETING_ROOM_4_EMAIL
    ];
    
    return in_array($email, $room_emails);
}
