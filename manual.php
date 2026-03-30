<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการใช้งาน - VMS System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="shortcut icon" href="favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="favicon/site.webmanifest" />
    <style>
        :root {
            /* Dark Mode (ค่าเริ่มต้น) */
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.6);
            --primary-color: #0ea5e9;
            --accent-color: #8b5cf6;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --input-bg: rgba(15, 23, 42, 0.6);
            --card-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            --toggle-bg: #334155;
            --toggle-handle: #94a3b8;
            --header-bg: linear-gradient(135deg, #1e293b, #0f172a);
        }

        /* Light Mode Theme */
        [data-theme="light"] {
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.8);
            --primary-color: #0284c7;
            --accent-color: #7c3aed;
            --text-main: #0f172a;
            --text-muted: #475569;
            --border-color: rgba(0, 0, 0, 0.1);
            --input-bg: rgba(255, 255, 255, 0.9);
            --card-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            --toggle-bg: #cbd5e1;
            --toggle-handle: #475569;
            --header-bg: linear-gradient(135deg, #0284c7, #7c3aed);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 30px 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        /* Header */
        .manual-header {
            background: var(--header-bg);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            transition: background 0.3s ease;
        }

        .header-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        /* Theme Switcher */
        .theme-switch-container,
        .lang-switch-container {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            padding: 6px 12px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
        }

        .theme-icon,
        .lang-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            transition: color 0.3s ease;
        }

        .theme-icon.active,
        .lang-text.active {
            color: white;
            text-shadow: 0 0 8px rgba(255,255,255,0.5);
        }

        .switch-theme,
        .switch-lang {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 18px;
            margin: 0 2px;
        }

        .switch-theme input,
        .switch-lang input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-theme,
        .slider-lang {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255,255,255,0.2);
            transition: .3s;
            border-radius: 34px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .slider-theme:before,
        .slider-lang:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 2px;
            bottom: 1px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .slider-theme,
        input:checked + .slider-lang {
            background-color: var(--primary-color);
        }

        input:checked + .slider-theme:before,
        input:checked + .slider-lang:before {
            transform: translateX(18px);
        }

        .nav-links {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .manual-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .manual-header h1 i {
            margin-right: 15px;
            color: var(--primary-color);
        }

        .manual-header .version {
            background: rgba(255,255,255,0.1);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }

        /* Table of Contents */
        .toc {
            background: var(--card-bg);
            padding: 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .toc h2 {
            color: var(--text-main);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .toc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .toc-item {
            background: var(--input-bg);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.3s;
            border-left: 4px solid var(--primary-color);
        }

        .toc-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--accent-color);
        }

        .toc-item i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* Content */
        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 50px;
            scroll-margin-top: 20px;
        }

        .section-title {
            color: var(--text-main);
            font-size: 1.8rem;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title i {
            color: var(--primary-color);
            font-size: 2rem;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--input-bg);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--border-color);
        }

        .card h3 {
            color: var(--text-main);
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h3 i {
            color: var(--primary-color);
        }

        .step-list {
            list-style: none;
        }

        .step-list li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
            color: var(--text-muted);
        }

        .step-list li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
        }

        /* Info Boxes */
        .info-box {
            background: rgba(14, 165, 233, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            color: var(--text-main);
        }

        .warning-box {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            color: var(--text-main);
        }

        .success-box {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            color: var(--text-main);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            color: var(--text-main);
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 12px;
            font-weight: 500;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover {
            background: rgba(255,255,255,0.05);
        }

        code {
            background: var(--bg-color);
            color: var(--primary-color);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        pre {
            background: var(--bg-color);
            color: var(--text-main);
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid var(--border-color);
        }

        /* FAQ */
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            background: var(--input-bg);
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-main);
        }

        .faq-answer {
            padding: 20px;
            display: none;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        /* Footer */
        .manual-footer {
            background: var(--header-bg);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .manual-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .manual-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .manual-header h1 {
                font-size: 1.8rem;
            }
            .content {
                padding: 20px;
            }
            .header-controls {
                position: static;
                justify-content: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="manual-header">
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-arrow-left"></i> <span data-i18n="back_to_system">กลับสู่ระบบ</span></a>
            </div>
            
            <!-- Header Controls (Theme + Language) -->
            <div class="header-controls">
                <!-- Theme Switcher -->
                <div class="theme-switch-container">
                    <i class="fas fa-sun theme-icon" id="themeIconLight"></i>
                    <label class="switch-theme">
                        <input type="checkbox" id="themeToggle">
                        <span class="slider-theme"></span>
                    </label>
                    <i class="fas fa-moon theme-icon" id="themeIconDark"></i>
                </div>
                
                <!-- Language Switcher -->
                <div class="lang-switch-container">
                    <span class="lang-text" id="langLabel_TH">TH</span>
                    <label class="switch-lang">
                        <input type="checkbox" id="languageToggle">
                        <span class="slider-lang"></span>
                    </label>
                    <span class="lang-text" id="langLabel_EN">EN</span>
                </div>
            </div>

            <h1>
                <i class="fas fa-book-open"></i>
                <span data-i18n="manual_title">คู่มือการใช้งาน VMS System</span>
            </h1>
            <p data-i18n="manual_subtitle">Visitor Management System - ระบบจัดการผู้มาติดต่อและจองห้องประชุม</p>
            <span class="version">
                <span data-i18n="version_label">เวอร์ชัน</span> 
                <span data-i18n="version_number">2.0.0</span> | 
                <span data-i18n="version_date">อัพเดตล่าสุด: กุมภาพันธ์ 2569</span>
            </span>
        </div>

        <!-- Translation Data -->
        <script>
            const translations = {
                th: {
                    back_to_system: "กลับสู่ระบบ",
                    manual_title: "คู่มือการใช้งาน VMS System",
                    manual_subtitle: "Visitor Management System - ระบบจัดการผู้มาติดต่อและจองห้องประชุม",
                    version_label: "เวอร์ชัน",
                    version_number: "2.0.0",
                    version_date: "อัพเดตล่าสุด: กุมภาพันธ์ 2569",
                    toc_title: "สารบัญ",
                    overview: "ภาพรวมระบบ",
                    getting_started: "เริ่มต้นใช้งาน",
                    add_visitor: "การเพิ่มผู้มาติดต่อ",
                    meeting_room: "การจองห้องประชุม",
                    email: "ระบบอีเมลแจ้งเตือน",
                    dashboard: "Dashboard",
                    admin: "การจัดการระบบ",
                    faq: "FAQ",
                    troubleshoot: "การแก้ไขปัญหา",
                    overview_desc: "วัตถุประสงค์และคุณสมบัติหลัก",
                    getting_started_desc: "วิธีการเริ่มต้นใช้งานระบบ",
                    add_visitor_desc: "ขั้นตอนการบันทึกข้อมูลผู้มาติดต่อ",
                    meeting_room_desc: "วิธีการจองห้องประชุม",
                    email_desc: "การทำงานของระบบอีเมล",
                    dashboard_desc: "ภาพรวมสถิติและรายงาน",
                    admin_desc: "การจัดการข้อมูลในระบบ",
                    faq_desc: "คำถามที่พบบ่อย",
                    troubleshoot_desc: "การแก้ไขปัญหาเบื้องต้น",
                    overview_title: "ภาพรวมระบบ (Overview)",
                    objective_title: "วัตถุประสงค์",
                    objective_desc: "ระบบ VMS (Visitor Management System) ถูกพัฒนาขึ้นเพื่อจัดการข้อมูลผู้มาติดต่อ จองห้องประชุม และส่งอีเมลแจ้งเตือนอัตโนมัติ เพื่อเพิ่มประสิทธิภาพในการต้อนรับและบริหารจัดการผู้มาติดต่อ",
                    features_title: "คุณสมบัติหลัก",
                    feature1: "บันทึกข้อมูลผู้มาติดต่อ (บริษัท, ชื่อ, วัตถุประสงค์)",
                    feature2: "เลือกระยะเวลาเข้าเยี่ยม (เริ่ม-สิ้นสุด)",
                    feature3: "จองห้องประชุมพร้อมแนบไฟล์ปฏิทิน (ICS)",
                    feature4: "ส่งอีเมลแจ้งเตือนอัตโนมัติไปยังผู้เกี่ยวข้อง",
                    feature5: "รองรับ 2 ภาษา (ไทย/อังกฤษ)",
                    feature6: "รองรับ Dark/Light Mode",
                    feature7: "Dashboard แสดงสถิติและรายงาน",
                    users_title: "กลุ่มผู้ใช้งาน",
                    receptionist: "พนักงาน",
                    receptionist_desc: "บันทึกข้อมูลผู้มาติดต่อ",
                    manager: "ผู้จัดการ",
                    manager_desc: "ดูรายงานและสถิติ",
                    admin: "Admin",
                    admin_desc: "จัดการข้อมูลพื้นฐาน",
                    sysadmin: "ผู้ดูแลระบบ",
                    sysadmin_desc: "ตั้งค่าและบำรุงรักษา",
                    getting_started_title: "เริ่มต้นใช้งาน",
                    getting_started_note: "ก่อนเริ่มใช้งาน ตรวจสอบว่ามีการตั้งค่าฐานข้อมูลและ SMTP ในไฟล์ config.php เรียบร้อยแล้ว",
                    login_title: "การเข้าสู่ระบบ",
                    login_step1: "เปิดเบราว์เซอร์และพิมพ์ URL:",
                    login_step2: "ระบบจะแสดงหน้าฟอร์มบันทึกข้อมูลผู้มาติดต่อ",
                    login_step3: "สามารถเปลี่ยนภาษาได้ที่มุมบนขวา (TH/EN)",
                    login_step4: "สามารถเปลี่ยนธีมได้ที่ปุ่ม Sun/Moon",
                    menu_title: "โครงสร้างเมนู",
                    add_visitor_menu: "เพิ่มผู้มาติดต่อ:",
                    add_visitor_desc_menu: "หน้าฟอร์มหลัก (index.php)",
                    dashboard_menu: "Dashboard:",
                    dashboard_desc_menu: "ดูสถิติและรายงาน",
                    admin_menu: "Admin:",
                    admin_desc_menu: "จัดการข้อมูล (ต้องมีสิทธิ์)",
                    
                    // เพิ่ม key สำหรับส่วน Add Visitor
                    add_visitor_title: "การเพิ่มผู้มาติดต่อ",
                    steps_title: "ขั้นตอนการบันทึกข้อมูล",
                    step: "ขั้นตอน",
                    detail: "รายละเอียด",
                    example: "ตัวอย่าง",
                    step1: "1. ข้อมูลบริษัท",
                    step1_detail: "กรอกชื่อบริษัทหรือหน่วยงาน",
                    step1_example: "บริษัท มารูโกะ จำกัด",
                    step2: "2. ชื่อผู้มาติดต่อ",
                    step2_detail: "กรอกชื่อ-นามสกุล",
                    step2_example: "นายสมชาย ใจดี",
                    step3: "3. วัตถุประสงค์",
                    step3_detail: "ระบุเหตุผลที่มาติดต่อ",
                    step3_example: "ประชุมหารือโครงการใหม่",
                    step4: "4. เวลาเริ่ม-สิ้นสุด",
                    step4_detail: "เลือกวันที่และเวลา",
                    step4_example: "เริ่ม: 24/02/2569 08:00<br>สิ้นสุด: 24/02/2569 17:30",
                    step5: "5. ช่วงเวลา",
                    step5_detail: "เลือกช่วงเวลาอัตโนมัติ",
                    step5_example: "เต็มวัน (08:00-17:00)",
                    step6: "6. ประเภท",
                    step6_detail: "เลือก VIP หรือ Normal",
                    step7: "7. บริการเสริม",
                    step7_detail: "เลือก Welcome Board / เยี่ยมชมโรงงาน / ไมค์โครโฟน",
                    step7_detail2: "ระบบจะส่งอีเมลแจ้งเตือน IT อัตโนมัติ",
                    step8: "8. บริการเสริม",
                    step8_detail: "เลือก กาแฟ-น้ำดื่ม / อาหารกลางวัน / ล่าม",
                    step8_detail2: "ระบบจะส่งอีเมลแจ้งเตือน GA อัตโนมัติ",
                    step9: "9. จองห้องประชุม",
                    step9_detail: "เลือก ห้องประชุม",
                    yes: "ต้องการ",
                    no: "ไม่ต้องการ",
                    warning: "ข้อควรระวัง:",
                    required_note: "ฟิลด์ที่มีเครื่องหมาย * (ดอกจัน) จำเป็นต้องกรอกทุกครั้ง",
                    
                    // เพิ่ม key สำหรับส่วน Meeting Room
                    meeting_room_title: "การจองห้องประชุม",
                    how_to_book: "วิธีการจอง",
                    book_step1: "เลื่อนสวิตช์ 'ต้องการจองห้องประชุม?' เป็น Yes",
                    book_step2: "ระบบจะแสดงฟิลด์เพิ่มเติมให้กรอก",
                    book_step3: "วันที่จอง: ดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ (แก้ไขได้)",
                    book_step4: "เลือกเวลาเริ่มและสิ้นสุดประชุม",
                    book_step5: "เลือกห้องประชุม (1-4)",
                    book_step6: "เลือกอีเมลผู้รับสำเนา (ค้นหาชื่อ/อีเมล)",
                    time_validation: "การตรวจสอบเวลา",
                    time_validation_desc: "ระบบจะตรวจสอบความถูกต้องของเวลา:",
                    time_rule1: "เวลาประชุมต้องอยู่ในช่วงเวลาเข้าเยี่ยม",
                    time_rule2: "เวลาเริ่มต้องน้อยกว่าเวลาสิ้นสุด",
                    time_rule3: "ถ้าไม่ถูกต้อง ระบบจะแจ้งเตือน",
                    meeting_request: "การส่ง Meeting Request",
                    meeting_request_desc: "เมื่อบันทึกข้อมูล ระบบจะ:",
                    meeting_request1: "ส่งอีเมลพร้อมไฟล์ ICS ไปยังห้องประชุม",
                    meeting_request2: "ส่ง CC ไปยังอีเมลที่เลือก",
                    meeting_request3: "ผู้รับสามารถตอบรับ/ปฏิเสธใน Outlook",
                    tip: "เคล็ดลับ:",
                    email_search_tip: "การเลือกอีเมลผู้รับสำเนา สามารถพิมพ์ค้นหาแบบ real-time และเลือกได้หลายคน",
                    
                    // เพิ่ม key สำหรับส่วน Email
                    email_title: "ระบบอีเมลแจ้งเตือน",
                    email_system: "การทำงานของระบบอีเมล",
                    meeting_request_label: "Meeting Request:",
                    meeting_request_desc2: "ส่งไปยังอีเมลห้องประชุม (พร้อม ICS)",
                    cc_email_label: "CC Email:",
                    cc_email_desc: "ส่งสำเนาไปยังผู้เกี่ยวข้อง",
                    language_label: "ภาษา:",
                    language_desc: "เนื้อหาอีเมลเปลี่ยนตามภาษาที่เลือกในฟอร์ม",
                    format_label: "รูปแบบ:",
                    format_desc: "HTML สวยงาม อ่านง่าย",
                    ics_title: "ไฟล์ปฏิทิน (ICS)",
                    ics_desc: "เมื่อมีการจองห้องประชุม ระบบจะสร้างไฟล์ ICS แนบไปกับอีเมล ซึ่งมีข้อมูล:",
                    ics_info1: "ชื่อผู้มาติดต่อและบริษัท",
                    ics_info2: "วันที่และเวลาประชุม",
                    ics_info3: "ห้องประชุมที่เลือก",
                    ics_info4: "ผู้เข้าร่วมประชุมทั้งหมด",
                    
                    // เพิ่ม key สำหรับส่วน Dashboard
                    dashboard_title: "Dashboard (ภาพรวมสถิติ)",
                    dashboard_components: "องค์ประกอบ Dashboard",
                    total_stats: "สถิติรวม:",
                    total_stats_desc: "จำนวนผู้มาติดต่อ, VIP, การจองห้อง",
                    bar_chart: "กราฟแท่ง:",
                    bar_chart_desc: "แสดงจำนวนรายเดือน (แยก VIP/Normal)",
                    pie_chart: "กราฟวงกลม:",
                    pie_chart_desc: "สัดส่วน VIP vs Normal",
                    data_table: "ตารางข้อมูล:",
                    data_table_desc: "รายชื่อผู้มาติดต่อในเดือนที่เลือก",
                    filter_title: "การกรองข้อมูล",
                    filter1: "เลือกปีที่ต้องการดูข้อมูล",
                    filter2: "คลิกที่เดือนในตารางเพื่อดูข้อมูลละเอียด",
                    filter3: "ระบบแสดงจำนวนผู้มาติดต่อในแต่ละเดือน",
                    dashboard_tip: "Dashboard รองรับการเปลี่ยนภาษาและธีมเช่นเดียวกับหน้าหลัก",
                    
                    // เพิ่ม key สำหรับส่วน Version History
                    version_title: "ประวัติการอัพเดท",
                    version: "เวอร์ชัน",
                    date: "วันที่",
                    details: "รายละเอียด",
                    version_date1: "ก.พ. 2569",
                    version_desc1: "เพิ่ม Dark/Light Mode, ปรับปรุง UI, รองรับ 2 ภาษา",
                    version_date2: "ก.พ. 2569",
                    version_desc2: "เพิ่มระบบ Dashboard และสถิติ",
                    version_date3: "ก.พ. 2569",
                    version_desc3: "เปิดตัวระบบครั้งแรก",
                    developed_by: "พัฒนาโดย IT Department"
                },
                en: {
                    back_to_system: "Back to System",
                    manual_title: "VMS System User Manual",
                    manual_subtitle: "Visitor Management System - Manage visitors and meeting room bookings",
                    version_label: "Version",
                    version_number: "2.0.0", 
                    version_date: "Last updated: February 2026",
                    toc_title: "Table of Contents",
                    overview: "System Overview",
                    getting_started: "Getting Started",
                    add_visitor: "Add Visitor",
                    meeting_room: "Meeting Room Booking",
                    email: "Email Notification",
                    dashboard: "Dashboard",
                    admin: "Admin Panel",
                    faq: "FAQ",
                    troubleshoot: "Troubleshooting",
                    overview_desc: "Objectives and key features",
                    getting_started_desc: "How to get started",
                    add_visitor_desc: "Steps to add visitor information",
                    meeting_room_desc: "How to book a meeting room",
                    email_desc: "Email notification system",
                    dashboard_desc: "Statistics and reports",
                    admin_desc: "System management",
                    faq_desc: "Frequently asked questions",
                    troubleshoot_desc: "Basic troubleshooting",
                    overview_title: "System Overview",
                    objective_title: "Objectives",
                    objective_desc: "The VMS (Visitor Management System) was developed to manage visitor information, book meeting rooms, and send automatic email notifications to relevant parties to improve reception efficiency and visitor management.",
                    features_title: "Key Features",
                    feature1: "Record visitor information (Company, Name, Purpose)",
                    feature2: "Select visit duration (Start-End)",
                    feature3: "Book meeting rooms with calendar attachment (ICS)",
                    feature4: "Send automatic email notifications to relevant parties",
                    feature5: "Support 2 languages (Thai/English)",
                    feature6: "Support Dark/Light Mode",
                    feature7: "Dashboard showing statistics and reports",
                    users_title: "User Groups",
                    receptionist: "Employee",
                    receptionist_desc: "Record visitor information",
                    manager: "Manager",
                    manager_desc: "View reports and statistics",
                    admin: "Admin",
                    admin_desc: "Manage basic data",
                    sysadmin: "System Administrator",
                    sysadmin_desc: "Configure and maintain the system",
            // เพิ่ม key สำหรับส่วน Getting Started (ภาษาอังกฤษ)
                    getting_started_title: "Getting Started",
                    getting_started_note: "Before using, make sure the database and SMTP settings in config.php are configured correctly.",
                    login_title: "Login",
                    login_step1: "Open browser and type URL:",
                    login_step2: "System will display the visitor registration form",
                    login_step3: "Change language at top right corner (TH/EN)",
                    login_step4: "Change theme using Sun/Moon button",
                    menu_title: "Menu Structure",
                    add_visitor_menu: "Add Visitor:",
                    add_visitor_desc_menu: "Main form page (index.php)",
                    dashboard_menu: "Dashboard:",
                    dashboard_desc_menu: "View statistics and reports",
                    admin_menu: "Admin:",
                    admin_desc_menu: "Manage data (requires permission)",
                    
                    // เพิ่ม key สำหรับส่วน Add Visitor (ภาษาอังกฤษ)
                    add_visitor_title: "Add Visitor",
                    steps_title: "Steps to Record Information",
                    step: "Step",
                    detail: "Detail",
                    example: "Example",
                    step1: "1. Company Information",
                    step1_detail: "Enter company or organization name",
                    step1_example: "Marugo Rubber Co., Ltd.",
                    step2: "2. Visitor Name",
                    step2_detail: "Enter first name and last name",
                    step2_example: "Mr. Somchai Jaidee",
                    step3: "3. Purpose",
                    step3_detail: "Specify reason for visit",
                    step3_example: "New project meeting",
                    step4: "4. Start-End Time",
                    step4_detail: "Select date and time",
                    step4_example: "Start: 24/02/2026 08:00<br>End: 24/02/2026 17:30",
                    step5: "5. Time Period",
                    step5_detail: "Select automatic time period",
                    step5_example: "Full day (08:00-17:00)",
                    step6: "6. Type",
                    step6_detail: "Select VIP or Normal",
                    step7: "7. Additional Services",
                    step7_detail: "Select Welcome Board / Factory Tour / Microphone",
                    step7_detail2: "System will send email notification to IT automatically",
                    step8: "8. Additional Services",
                    step8_detail: "Select Coffee-Water / Lunch / Translator",
                    step8_detail2: "System will send email notification to GA automatically",
                    step9: "9. Meeting Room",
                    step9_detail: "Select Meeting Room",
                    yes: "Yes",
                    no: "No",
                    warning: "Warning:",
                    required_note: "Fields marked with * (asterisk) are required.",
                    
                    // เพิ่ม key สำหรับส่วน Meeting Room (ภาษาอังกฤษ)
                    meeting_room_title: "Meeting Room Booking",
                    how_to_book: "How to Book",
                    book_step1: "Toggle 'Need meeting room?' to Yes",
                    book_step2: "System will show additional fields",
                    book_step3: "Booking date: Auto-filled from visit start date (editable)",
                    book_step4: "Select meeting start and end time",
                    book_step5: "Select meeting room (1-4)",
                    book_step6: "Select CC recipients (search by name/email)",
                    time_validation: "Time Validation",
                    time_validation_desc: "System will validate the time:",
                    time_rule1: "Meeting time must be within visit duration",
                    time_rule2: "Start time must be before end time",
                    time_rule3: "If invalid, system will show warning",
                    meeting_request: "Meeting Request",
                    meeting_request_desc: "When saving, the system will:",
                    meeting_request1: "Send email with ICS file to meeting room",
                    meeting_request2: "Send CC to selected emails",
                    meeting_request3: "Recipients can accept/decline in Outlook",
                    tip: "Tip:",
                    email_search_tip: "You can search and select multiple CC recipients in real-time",
                    
                    // เพิ่ม key สำหรับส่วน Email (ภาษาอังกฤษ)
                    email_title: "Email Notification",
                    email_system: "Email System",
                    meeting_request_label: "Meeting Request:",
                    meeting_request_desc2: "Sent to meeting room email (with ICS)",
                    cc_email_label: "CC Email:",
                    cc_email_desc: "Send copy to relevant parties",
                    language_label: "Language:",
                    language_desc: "Email content changes based on form language",
                    format_label: "Format:",
                    format_desc: "Beautiful HTML, easy to read",
                    ics_title: "Calendar File (ICS)",
                    ics_desc: "When booking a meeting room, system generates ICS file attached to email with:",
                    ics_info1: "Visitor name and company",
                    ics_info2: "Meeting date and time",
                    ics_info3: "Selected meeting room",
                    ics_info4: "All meeting participants",
                    
                    // เพิ่ม key สำหรับส่วน Dashboard (ภาษาอังกฤษ)
                    dashboard_title: "Dashboard (Statistics Overview)",
                    dashboard_components: "Dashboard Components",
                    total_stats: "Total Statistics:",
                    total_stats_desc: "Number of visitors, VIP, room bookings",
                    bar_chart: "Bar Chart:",
                    bar_chart_desc: "Monthly counts (separated by VIP/Normal)",
                    pie_chart: "Pie Chart:",
                    pie_chart_desc: "VIP vs Normal ratio",
                    data_table: "Data Table:",
                    data_table_desc: "List of visitors in selected month",
                    filter_title: "Data Filtering",
                    filter1: "Select year to view data",
                    filter2: "Click on month in table to view details",
                    filter3: "System displays visitor count per month",
                    dashboard_tip: "Dashboard supports language and theme switching like main page",
                    
                    // เพิ่ม key สำหรับส่วน Version History (ภาษาอังกฤษ)
                    version_title: "Version History",
                    version: "Version",
                    date: "Date",
                    details: "Details",
                    version_date1: "Feb 2026",
                    version_desc1: "Added Dark/Light Mode, UI improvements, bilingual support",
                    version_date2: "Feb 2026",
                    version_desc2: "Added Dashboard and statistics system",
                    version_date3: "Feb 2026",
                    version_desc3: "Initial system launch",
                    
                    developed_by: "Developed by IT Department"
                }
            };
        </script>

        <!-- Theme and Language Switcher Script -->
        <script>
            // Theme Switcher
            (function() {
                const themeToggle = document.getElementById('themeToggle');
                const themeIconLight = document.getElementById('themeIconLight');
                const themeIconDark = document.getElementById('themeIconDark');
                
                const savedTheme = localStorage.getItem('vms_theme') || 'dark';
                
                function setTheme(theme) {
                    if (theme === 'light') {
                        document.documentElement.setAttribute('data-theme', 'light');
                        localStorage.setItem('vms_theme', 'light');
                        if (themeToggle) themeToggle.checked = true;
                        if (themeIconLight) themeIconLight.classList.add('active');
                        if (themeIconDark) themeIconDark.classList.remove('active');
                    } else {
                        document.documentElement.setAttribute('data-theme', 'dark');
                        localStorage.setItem('vms_theme', 'dark');
                        if (themeToggle) themeToggle.checked = false;
                        if (themeIconLight) themeIconLight.classList.remove('active');
                        if (themeIconDark) themeIconDark.classList.add('active');
                    }
                }
                
                setTheme(savedTheme);
                
                if (themeToggle) {
                    themeToggle.addEventListener('change', function() {
                        setTheme(this.checked ? 'light' : 'dark');
                    });
                }
            })();

            // Language Switcher
            (function() {
                const langToggle = document.getElementById('languageToggle');
                const langLabelTH = document.getElementById('langLabel_TH');
                const langLabelEN = document.getElementById('langLabel_EN');
                
                const savedLang = localStorage.getItem('vms_lang') || 'th';
                
                function setLanguage(lang) {
                    localStorage.setItem('vms_lang', lang);
                    
                    if (lang === 'en') {
                        langLabelTH.classList.remove('active');
                        langLabelEN.classList.add('active');
                        if (langToggle) langToggle.checked = true;
                    } else {
                        langLabelTH.classList.add('active');
                        langLabelEN.classList.remove('active');
                        if (langToggle) langToggle.checked = false;
                    }
                    
                    // Update all elements with data-i18n
                    document.querySelectorAll('[data-i18n]').forEach(el => {
                        const key = el.getAttribute('data-i18n');
                        if (translations[lang] && translations[lang][key]) {
                            el.textContent = translations[lang][key];
                        }
                    });
                }
                
                setLanguage(savedLang);
                
                if (langToggle) {
                    langToggle.addEventListener('change', function() {
                        setLanguage(this.checked ? 'en' : 'th');
                    });
                }
            })();
        </script>

        <!-- Table of Contents -->
        <div class="toc">
            <h2><i class="fas fa-list"></i> <span data-i18n="toc_title">สารบัญ</span></h2>
            <div class="toc-grid">
                <a href="#overview" class="toc-item"><i class="fas fa-info-circle"></i> <span data-i18n="overview">ภาพรวมระบบ</span><br><small data-i18n="overview_desc">วัตถุประสงค์และคุณสมบัติหลัก</small></a>
                <a href="#getting-started" class="toc-item"><i class="fas fa-rocket"></i> <span data-i18n="getting_started">เริ่มต้นใช้งาน</span><br><small data-i18n="getting_started_desc">วิธีการเริ่มต้นใช้งานระบบ</small></a>
                <a href="#add-visitor" class="toc-item"><i class="fas fa-user-plus"></i> <span data-i18n="add_visitor">การเพิ่มผู้มาติดต่อ</span><br><small data-i18n="add_visitor_desc">ขั้นตอนการบันทึกข้อมูลผู้มาติดต่อ</small></a>
                <a href="#meeting-room" class="toc-item"><i class="fas fa-door-open"></i> <span data-i18n="meeting_room">การจองห้องประชุม</span><br><small data-i18n="meeting_room_desc">วิธีการจองห้องประชุม</small></a>
                <a href="#email" class="toc-item"><i class="fas fa-envelope"></i> <span data-i18n="email">ระบบอีเมลแจ้งเตือน</span><br><small data-i18n="email_desc">การทำงานของระบบอีเมล</small></a>
                <a href="#dashboard" class="toc-item"><i class="fas fa-chart-bar"></i> <span data-i18n="dashboard">Dashboard</span><br><small data-i18n="dashboard_desc">ภาพรวมสถิติและรายงาน</small></a>
            </div>
        </div>

<!-- Content -->
<div class="content">
    <!-- ภาพรวมระบบ -->
    <section id="overview" class="section">
        <h2 class="section-title">
            <i class="fas fa-info-circle"></i>
            <span data-i18n="overview_title">ภาพรวมระบบ (Overview)</span>
        </h2>
        
        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-bullseye"></i> <span data-i18n="objective_title">วัตถุประสงค์</span></h3>
                <p data-i18n="objective_desc">ระบบ VMS (Visitor Management System) ถูกพัฒนาขึ้นเพื่อจัดการข้อมูลผู้มาติดต่อ จองห้องประชุม และส่งอีเมลแจ้งเตือนอัตโนมัติ เพื่อเพิ่มประสิทธิภาพในการต้อนรับและบริหารจัดการผู้มาติดต่อ</p>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-star"></i> <span data-i18n="features_title">คุณสมบัติหลัก</span></h3>
                <ul class="step-list">
                    <li data-i18n="feature1">บันทึกข้อมูลผู้มาติดต่อ (บริษัท, ชื่อ, วัตถุประสงค์)</li>
                    <li data-i18n="feature2">เลือกระยะเวลาเข้าเยี่ยม (เริ่ม-สิ้นสุด)</li>
                    <li data-i18n="feature3">จองห้องประชุมพร้อมแนบไฟล์ปฏิทิน (ICS)</li>
                    <li data-i18n="feature4">ส่งอีเมลแจ้งเตือนอัตโนมัติไปยังผู้เกี่ยวข้อง</li>
                    <li data-i18n="feature5">รองรับ 2 ภาษา (ไทย/อังกฤษ)</li>
                    <li data-i18n="feature6">รองรับ Dark/Light Mode</li>
                    <li data-i18n="feature7">Dashboard แสดงสถิติและรายงาน</li>
                </ul>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-users"></i> <span data-i18n="users_title">กลุ่มผู้ใช้งาน</span></h3>
                <ul class="step-list">
                    <li><strong data-i18n="receptionist">พนักงาน:</strong> <span data-i18n="receptionist_desc">บันทึกข้อมูลผู้มาติดต่อ</span></li>
                    <li><strong data-i18n="manager">GA:</strong> <span data-i18n="manager_desc">ดูรายงานและสถิติ</span></li>
                    <li><strong data-i18n="admin">Admin:</strong> <span data-i18n="admin_desc">จัดการข้อมูลพื้นฐาน</span></li>
                    <li><strong data-i18n="sysadmin">ผู้ดูแลระบบ:</strong> <span data-i18n="sysadmin_desc">ตั้งค่าและบำรุงรักษา</span></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- เริ่มต้นใช้งาน -->
    <section id="getting-started" class="section">
        <h2 class="section-title">
            <i class="fas fa-rocket"></i>
            <span data-i18n="getting_started_title">เริ่มต้นใช้งาน (Getting Started)</span>
        </h2>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong data-i18n="note">หมายเหตุ:</strong> <span data-i18n="getting_started_note">ก่อนเริ่มใช้งาน ตรวจสอบว่ามีการตั้งค่าฐานข้อมูลและ SMTP ในไฟล์ config.php เรียบร้อยแล้ว</span>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-sign-in-alt"></i> <span data-i18n="login_title">การเข้าสู่ระบบ</span></h3>
                <ol class="step-list">
                    <li data-i18n="login_step1">เปิดเบราว์เซอร์และพิมพ์ </br>URL: <a href="http://sys.marugo-rubber.co.th/visitor/"><code>http://sys.marugo-rubber.co.th/visitor/</code></a></li>
                    <li data-i18n="login_step2">ระบบจะแสดงหน้าฟอร์มบันทึกข้อมูลผู้มาติดต่อ</li>
                    <li data-i18n="login_step3">สามารถเปลี่ยนภาษาได้ที่มุมบนขวา (TH/EN)</li>
                    <li data-i18n="login_step4">สามารถเปลี่ยนธีมได้ที่ปุ่ม Sun/Moon</li>
                </ol>
            </div>

            <div class="card">
                <h3><i class="fas fa-sitemap"></i> <span data-i18n="menu_title">โครงสร้างเมนู</span></h3>
                <ul class="step-list">
                    <li><i class="fas fa-plus-circle"></i> <strong data-i18n="add_visitor_menu">เพิ่มผู้มาติดต่อ:</strong> <span data-i18n="add_visitor_desc_menu">หน้าฟอร์มหลัก (index.php)</span></li>
                    <li><i class="fas fa-chart-area"></i> <strong data-i18n="dashboard_menu">Dashboard:</strong> <span data-i18n="dashboard_desc_menu">ดูสถิติและรายงาน</span></li>
                    <li><i class="fas fa-cog"></i> <strong data-i18n="admin_menu">Admin:</strong> <span data-i18n="admin_desc_menu">จัดการข้อมูล (ต้องมีสิทธิ์)</span></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- การเพิ่มผู้มาติดต่อ -->
    <section id="add-visitor" class="section">
        <h2 class="section-title">
            <i class="fas fa-user-plus"></i>
            <span data-i18n="add_visitor_title">การเพิ่มผู้มาติดต่อ (Add Visitor)</span>
        </h2>

        <div class="card-grid">
            <div class="card" style="grid-column: span 2;">
                <h3><i class="fas fa-list-ol"></i> <span data-i18n="steps_title">ขั้นตอนการบันทึกข้อมูล</span></h3>
                
                <table>
                    <tr>
                        <th data-i18n="step">ขั้นตอน</th>
                        <th data-i18n="detail">รายละเอียด</th>
                        <th data-i18n="example">ตัวอย่าง</th>
                    </tr>
                    <tr>
                        <td data-i18n="step1">1. ข้อมูลบริษัท</td>
                        <td data-i18n="step1_detail">กรอกชื่อบริษัทหรือหน่วยงาน</td>
                        <td data-i18n="step1_example">บริษัท มารูโกะ จำกัด</td>
                    </tr>
                    <tr>
                        <td data-i18n="step2">2. ชื่อผู้มาติดต่อ</td>
                        <td data-i18n="step2_detail">กรอกชื่อ-นามสกุล</td>
                        <td data-i18n="step2_example">นายสมชาย ใจดี</td>
                    </tr>
                    <tr>
                        <td data-i18n="step3">3. วัตถุประสงค์</td>
                        <td data-i18n="step3_detail">ระบุเหตุผลที่มาติดต่อ</td>
                        <td data-i18n="step3_example">ประชุมหารือโครงการใหม่</td>
                    </tr>
                    <tr>
                        <td data-i18n="step4">4. เวลาเริ่ม-สิ้นสุด</td>
                        <td data-i18n="step4_detail">เลือกวันที่และเวลา</td>
                        <td data-i18n="step4_example">เริ่ม: 24/02/2026 08:00<br>สิ้นสุด: 24/02/2026 17:30</td>
                    </tr>
                    <tr>
                        <td data-i18n="step5">5. ช่วงเวลา</td>
                        <td data-i18n="step5_detail">เลือกช่วงเวลาอัตโนมัติ</td>
                        <td data-i18n="step5_example">เต็มวัน (08:00-17:00)</td>
                    </tr>
                    <tr>
                        <td data-i18n="step6">6. ประเภท</td>
                        <td data-i18n="step6_detail">เลือก VIP หรือ Normal</td>
                        <td><span class="badge badge-vip">VIP</span> หรือ <span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td data-i18n="step7">7. บริการเสริม</td>
                        <td>
                            <span data-i18n="step7_detail">เลือก Welcome Board / เยี่ยมชมโรงงาน / ไมค์โครโฟน</span>
                            <br>
                            <small data-i18n="step7_detail2">ระบบจะส่งอีเมลแจ้งเตือน IT อัตโนมัติ</small>
                        </td>
                        <td><span class="badge badge-yes">✅ <span data-i18n="yes">ต้องการ</span></span> / <span class="badge badge-no">❌ <span data-i18n="no">ไม่ต้องการ</span></span></td>
                    </tr>
                    <tr>
                        <td data-i18n="step8">8. บริการเสริม</td>
                        <td>
                            <span data-i18n="step8_detail">เลือก กาแฟ-น้ำดื่ม / อาหารกลางวัน / ล่าม</span>
                            <br>
                            <small data-i18n="step8_detail2">ระบบจะส่งอีเมลแจ้งเตือน GA อัตโนมัติ</small>
                        </td>
                        <td><span class="badge badge-yes">✅ <span data-i18n="yes">ต้องการ</span></span> / <span class="badge badge-no">❌ <span data-i18n="no">ไม่ต้องการ</span></span></td>
                    </tr>
                    <tr>
                        <td data-i18n="step9">9. จองห้องประชุม</td>
                        <td data-i18n="step9_detail">เลือก ห้องประชุม</td>
                        <td><span class="badge badge-yes">✅ <span data-i18n="yes">ต้องการ</span></span> / <span class="badge badge-no">❌ <span data-i18n="no">ไม่ต้องการ</span></span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="warning-box">
            <i class="fas fa-exclamation-triangle"></i>
            <strong data-i18n="warning">ข้อควรระวัง:</strong> <span data-i18n="required_note">ฟิลด์ที่มีเครื่องหมาย * (ดอกจัน) จำเป็นต้องกรอกทุกครั้ง</span>
        </div>
    </section>

    <!-- การจองห้องประชุม -->
    <section id="meeting-room" class="section">
        <h2 class="section-title">
            <i class="fas fa-door-open"></i>
            <span data-i18n="meeting_room_title">การจองห้องประชุม (Meeting Room Booking)</span>
        </h2>

        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-check-circle"></i> <span data-i18n="how_to_book">วิธีการจอง</span></h3>
                <ol class="step-list">
                    <li data-i18n="book_step1">เลื่อนสวิตช์ "ต้องการจองห้องประชุม?" เป็น Yes</li>
                    <li data-i18n="book_step2">ระบบจะแสดงฟิลด์เพิ่มเติมให้กรอก</li>
                    <li data-i18n="book_step3">วันที่จอง: ดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ (แก้ไขได้)</li>
                    <li data-i18n="book_step4">เลือกเวลาเริ่มและสิ้นสุดประชุม</li>
                    <li data-i18n="book_step5">เลือกห้องประชุม (1-4)</li>
                    <li data-i18n="book_step6">เลือกอีเมลผู้รับสำเนา (ค้นหาชื่อ/อีเมล)</li>
                </ol>
            </div>

            <div class="card">
                <h3><i class="fas fa-clock"></i> <span data-i18n="time_validation">การตรวจสอบเวลา</span></h3>
                <p data-i18n="time_validation_desc">ระบบจะตรวจสอบความถูกต้องของเวลา:</p>
                <ul class="step-list">
                    <li data-i18n="time_rule1">เวลาประชุมต้องอยู่ในช่วงเวลาเข้าเยี่ยม</li>
                    <li data-i18n="time_rule2">เวลาเริ่มต้องน้อยกว่าเวลาสิ้นสุด</li>
                    <li data-i18n="time_rule3">ถ้าไม่ถูกต้อง ระบบจะแจ้งเตือน</li>
                </ul>
            </div>

            <div class="card">
                <h3><i class="fas fa-envelope"></i> <span data-i18n="meeting_request">การส่ง Meeting Request</span></h3>
                <p data-i18n="meeting_request_desc">เมื่อบันทึกข้อมูล ระบบจะ:</p>
                <ul class="step-list">
                    <li data-i18n="meeting_request1">ส่งอีเมลพร้อมไฟล์ ICS ไปยังห้องประชุม</li>
                    <li data-i18n="meeting_request2">ส่ง CC ไปยังอีเมลที่เลือก</li>
                    <li data-i18n="meeting_request3">ผู้รับสามารถตอบรับ/ปฏิเสธใน Outlook</li>
                </ul>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-lightbulb"></i>
            <strong data-i18n="tip">เคล็ดลับ:</strong> <span data-i18n="email_search_tip">การเลือกอีเมลผู้รับสำเนา สามารถพิมพ์ค้นหาแบบ real-time และเลือกได้หลายคน</span>
        </div>
    </section>

    <!-- ระบบอีเมลแจ้งเตือน -->
    <section id="email" class="section">
        <h2 class="section-title">
            <i class="fas fa-envelope"></i>
            <span data-i18n="email_title">ระบบอีเมลแจ้งเตือน (Email Notification)</span>
        </h2>

        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-paper-plane"></i> <span data-i18n="email_system">การทำงานของระบบอีเมล</span></h3>
                <ul class="step-list">
                    <li><strong data-i18n="meeting_request_label">Meeting Request:</strong> <span data-i18n="meeting_request_desc2">ส่งไปยังอีเมลห้องประชุม (พร้อม ICS)</span></li>
                    <li><strong data-i18n="cc_email_label">CC Email:</strong> <span data-i18n="cc_email_desc">ส่งสำเนาไปยังผู้เกี่ยวข้อง</span></li>
                    <li><strong data-i18n="language_label">ภาษา:</strong> <span data-i18n="language_desc">เนื้อหาอีเมลเปลี่ยนตามภาษาที่เลือกในฟอร์ม</span></li>
                    <li><strong data-i18n="format_label">รูปแบบ:</strong> <span data-i18n="format_desc">HTML สวยงาม อ่านง่าย</span></li>
                </ul>
            </div>

            <div class="card">
                <h3><i class="fas fa-calendar-alt"></i> <span data-i18n="ics_title">ไฟล์ปฏิทิน (ICS)</span></h3>
                <p data-i18n="ics_desc">เมื่อมีการจองห้องประชุม ระบบจะสร้างไฟล์ ICS แนบไปกับอีเมล ซึ่งมีข้อมูล:</p>
                <ul class="step-list">
                    <li data-i18n="ics_info1">ชื่อผู้มาติดต่อและบริษัท</li>
                    <li data-i18n="ics_info2">วันที่และเวลาประชุม</li>
                    <li data-i18n="ics_info3">ห้องประชุมที่เลือก</li>
                    <li data-i18n="ics_info4">ผู้เข้าร่วมประชุมทั้งหมด</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Dashboard -->
    <section id="dashboard" class="section">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i>
            <span data-i18n="dashboard_title">Dashboard (ภาพรวมสถิติ)</span>
        </h2>

        <div class="card-grid">
            <div class="card">
                <h3><i class="fas fa-chart-pie"></i> <span data-i18n="dashboard_components">องค์ประกอบ Dashboard</span></h3>
                <ul class="step-list">
                    <li><strong data-i18n="total_stats">สถิติรวม:</strong> <span data-i18n="total_stats_desc">จำนวนผู้มาติดต่อ, VIP, การจองห้อง</span></li>
                    <li><strong data-i18n="bar_chart">กราฟแท่ง:</strong> <span data-i18n="bar_chart_desc">แสดงจำนวนรายเดือน (แยก VIP/Normal)</span></li>
                    <li><strong data-i18n="pie_chart">กราฟวงกลม:</strong> <span data-i18n="pie_chart_desc">สัดส่วน VIP vs Normal</span></li>
                    <li><strong data-i18n="data_table">ตารางข้อมูล:</strong> <span data-i18n="data_table_desc">รายชื่อผู้มาติดต่อในเดือนที่เลือก</span></li>
                </ul>
            </div>

            <div class="card">
                <h3><i class="fas fa-filter"></i> <span data-i18n="filter_title">การกรองข้อมูล</span></h3>
                <ul class="step-list">
                    <li data-i18n="filter1">เลือกปีที่ต้องการดูข้อมูล</li>
                    <li data-i18n="filter2">คลิกที่เดือนในตารางเพื่อดูข้อมูลละเอียด</li>
                    <li data-i18n="filter3">ระบบแสดงจำนวนผู้มาติดต่อในแต่ละเดือน</li>
                </ul>
            </div>
        </div>

        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <strong data-i18n="tip">Tip:</strong> <span data-i18n="dashboard_tip">Dashboard รองรับการเปลี่ยนภาษาและธีมเช่นเดียวกับหน้าหลัก</span>
        </div>
    </section>

    <!-- Version History -->
    <section class="section">
        <h2 class="section-title">
            <i class="fas fa-history"></i>
            <span data-i18n="version_title">ประวัติการอัพเดท (Version History)</span>
        </h2>

        <div class="card">
            <table>
                <tr>
                    <th data-i18n="version">เวอร์ชัน</th>
                    <th data-i18n="date">วันที่</th>
                    <th data-i18n="details">รายละเอียด</th>
                </tr>
                <tr>
                    <td>2.0.0</td>
                    <td data-i18n="version_date1">ก.พ. 2569</td>
                    <td data-i18n="version_desc1">เพิ่ม Dark/Light Mode, ปรับปรุง UI, รองรับ 2 ภาษา</td>
                </tr>
                <tr>
                    <td>1.5.0</td>
                    <td data-i18n="version_date2">ก.พ. 2569</td>
                    <td data-i18n="version_desc2">เพิ่มระบบ Dashboard และสถิติ</td>
                </tr>
                <tr>
                    <td>1.0.0</td>
                    <td data-i18n="version_date3">ก.พ. 2569</td>
                    <td data-i18n="version_desc3">เปิดตัวระบบครั้งแรก</td>
                </tr>
            </table>
        </div>
    </section>
</div>

        <!-- Footer -->
        <div class="manual-footer">
            <p><i class="fas fa-copyright"></i> 2569 Visitor Management System (VMS) | <span data-i18n="developed_by">พัฒนาโดย IT Department</span></p>
        </div>
    </div>

    <script>
        // Smooth scroll สำหรับลิงก์ในสารบัญ
        document.querySelectorAll('.toc-item, a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // FAQ accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                this.parentElement.classList.toggle('active');
            });
        });
    </script>
</body>
</html>