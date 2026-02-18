<?php session_start(); $_SESSION["form_token"] = bin2hex(random_bytes(16)); $form_token = $_SESSION["form_token"]; ?>
<?php
require_once 'config.php';

// Fetch email recipients for dropdown
$email_query = "SELECT id, email, name, department FROM email_recipients WHERE is_active = 1 ORDER BY name";
$email_result = $conn->query($email_query);
$email_recipients = [];
if ($email_result) {
    while ($row = $email_result->fetch_assoc()) {
        $email_recipients[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="shortcut icon" href="favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="favicon/site.webmanifest" />
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

<div class="nav-links" style="position:fixed;top:20px;left:20px;display:flex;gap:10px;z-index:100;">
    <a href="dashboard.php" style="background:var(--card-bg);border:1px solid var(--border-color);color:var(--primary-color);padding:8px 14px;border-radius:8px;font-size:.85rem;text-decoration:none;backdrop-filter:blur(5px);transition:all 0.3s ease;">
        <i class="fas fa-chart-area"></i> Dashboard
    </a>
    <a href="admin.php" style="background:var(--card-bg);border:1px solid var(--border-color);color:var(--accent-color);padding:8px 14px;border-radius:8px;font-size:.85rem;text-decoration:none;backdrop-filter:blur(5px);transition:all 0.3s ease;">
        <i class="fas fa-cog"></i> Admin
    </a>
</div>
<div class="container">
<header>
    <div class="header-top">
        
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
    </div>
    
    <div class="header-content">
        <h1>
            <img src="images/logo.png" alt="VISITOR SYSTEM" style="height:50px;margin-right:10px;">
            VISITOR <span class="highlight">SYSTEM</span>
        </h1>
        <p data-i18n="subtitle">ระบบจัดการผู้มาติดต่อและจองห้องประชุม</p>
    </div>
</header>

    <form id="visitorForm" method="POST" action="save_visitor.php">
        <input type="hidden" name="_token" value="<?= $form_token ?>">
        <div class="glass-card form-section">
            <h2 data-i18n="section_visitor"><i class="fas fa-user-astronaut"></i> ข้อมูลผู้มาติดต่อ</h2>
            
            <div class="form-group floating-group">
                <input type="text" id="company_name" name="company_name" required placeholder=" ">
                <label for="company_name"><span data-i18n="company">บริษัท/หน่วยงาน</span> <span class="required">*</span></label>
            </div>

            <div class="form-group floating-group">
                <input type="text" id="visitor_name" name="visitor_name" required placeholder=" ">
                <label for="visitor_name"><span data-i18n="visitor_name">ชื่อผู้มาติดต่อ</span> <span class="required">*</span></label>
            </div>

            <div class="form-group floating-group">
                <textarea id="purpose" name="purpose" rows="3" required placeholder=" "></textarea>
                <label for="purpose"><span data-i18n="purpose">วัตถุประสงค์</span> <span class="required">*</span></label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="static-label"><span data-i18n="start_time">เวลาเริ่มเข้าเยี่ยม</span> <span class="required">*</span></label>
                    <input type="datetime-local" id="visit_start_datetime" name="visit_start_datetime" required onchange="updateMeetingDate(); updatePeriodFromTime();">
                </div>

                <div class="form-group">
                    <label class="static-label"><span data-i18n="end_time">เวลาสิ้นสุดเข้าเยี่ยม</span> <span class="required">*</span></label>
                    <input type="datetime-local" id="visit_end_datetime" name="visit_end_datetime" required onchange="updatePeriodFromTime();">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="static-label" data-i18n="period">ช่วงเวลา</label>
                    <div class="select-wrapper">
                        <select id="visit_period" name="visit_period" onchange="updateTimeFromPeriod()">
                            <option value="" data-i18n="select_period">เลือกช่วงเวลา</option>
                            <option value="morning" data-i18n="morning">เช้า (08:00-12:00)</option>
                            <option value="afternoon" data-i18n="afternoon">บ่าย (13:00-17:00)</option>
                            <option value="full_day" data-i18n="full_day">เต็มวัน (08:00-17:00)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="static-label" data-i18n="visitor_type">ประเภทผู้มาติดต่อ</label>
                    <div class="select-wrapper">
                        <select id="visitor_type" name="visitor_type">
                            <option value="Normal">Normal</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group switch-group">
                    <label for="welcome_board" data-i18n="need_welcome">ต้องการ Welcome Board?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="welcome_board_check" onchange="document.getElementById('welcome_board').value = this.checked ? '1' : '0'">
                        <label for="welcome_board_check" class="slider"></label>
                        <input type="hidden" id="welcome_board" name="welcome_board" value="0">
                    </div>
                </div>

                <div class="form-group switch-group">
                    <label for="factory_tour" data-i18n="need_tour">ต้องการเยี่ยมชมโรงงาน?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="factory_tour_check" onchange="document.getElementById('factory_tour').value = this.checked ? '1' : '0'">
                        <label for="factory_tour_check" class="slider"></label>
                        <input type="hidden" id="factory_tour" name="factory_tour" value="0">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group switch-group">
                    <label for="meeting_room" data-i18n="need_meeting">ต้องการจองห้องประชุม?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="meeting_room_check" onchange="document.getElementById('meeting_room').value = this.checked ? '1' : '0'; toggleMeetingFields();">
                        <label for="meeting_room_check" class="slider"></label>
                        <input type="hidden" id="meeting_room" name="meeting_room" value="0" onchange="toggleMeetingFields()">
                    </div>
                </div>
            </div>
        </div>

        <div id="meetingSection" class="glass-card form-section" style="display: none;">
            <h2 data-i18n="section_meeting"><i class="fas fa-door-open"></i> รายละเอียดการจอง</h2>
            
        <div class="form-group">
            <label class="static-label" data-i18n="meeting_date">วันที่จองห้องประชุม <span class="required">*</span></label>
            <input type="date" id="meeting_date" name="meeting_date" class="meeting-field" required>
            <small class="field-note">* วันที่เริ่มต้นดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ แต่สามารถแก้ไขได้</small>
        </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="static-label"><span data-i18n="meeting_start">เริ่มประชุม</span> <span class="required">*</span></label>
                    <input type="time" id="meeting_start" name="meeting_start" class="meeting-field" required>
                </div>

                <div class="form-group">
                    <label class="static-label"><span data-i18n="meeting_end">สิ้นสุดประชุม</span> <span class="required">*</span></label>
                    <input type="time" id="meeting_end" name="meeting_end" class="meeting-field" required>
                </div>
            </div>

            <div class="form-group">
                <label class="static-label"><span data-i18n="select_room">เลือกห้องประชุม</span> <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select id="meeting_room_select" name="meeting_room_select" class="meeting-field" required>
                        <option value="" data-i18n="select_room_ph">เลือกห้องประชุม</option>
                        <option value="Meeting Room 1">Meeting Room 1</option>
                        <option value="Meeting Room 2">Meeting Room 2</option>
                        <option value="Meeting Room 3">Meeting Room 3</option>
                        <option value="Meeting Room 4">Meeting Room 4</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="static-label" data-i18n="cc_email">อีเมลผู้รับสำเนา</label>
                <div class="email-selector">
                     <div class="email-search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="emailSearch" class="email-search-input" placeholder="ค้นหาชื่อหรืออีเมล..." data-i18n-placeholder="search_ph">
                        <div id="emailSearchResults" class="search-results"></div>
                    </div>
                    <div class="selected-emails" id="selectedEmails"></div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="btn-content"><i class="fas fa-paper-plane"></i> <span data-i18n="btn_submit">ยืนยันข้อมูล</span></span>
                <div class="btn-glow"></div>
            </button>
            <button type="reset" class="btn-reset">
                <i class="fas fa-rotate-left"></i> <span data-i18n="btn_reset">ล้างค่า</span>
            </button>
        </div>
    </form>
</div>
    <script src="script.js"></script>
    <script>
        // ฟังก์ชันตั้งค่าวันที่เริ่มต้นเป็นวันนี้ เวลา 08:00 และ 17:30
        function setDefaultDateTime() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            
            // ตั้งค่าเริ่มต้น: 08:00 น.
            const startDateTime = `${year}-${month}-${day}T08:00`;
            document.getElementById('visit_start_datetime').value = startDateTime;
            
            // ตั้งค่าสิ้นสุด: 17:30 น.
            const endDateTime = `${year}-${month}-${day}T17:30`;
            document.getElementById('visit_end_datetime').value = endDateTime;
            
            // อัปเดตวันที่จองห้องประชุม
            updateMeetingDate();
        }

        // ฟังก์ชันอัปเดตช่วงเวลาตามเวลาที่เลือก
        function updatePeriodFromTime() {
            const startTime = document.getElementById('visit_start_datetime').value;
            const endTime = document.getElementById('visit_end_datetime').value;
            const periodSelect = document.getElementById('visit_period');
            
            if (startTime && endTime) {
                const startHour = parseInt(startTime.split('T')[1].split(':')[0]);
                const startMin = parseInt(startTime.split('T')[1].split(':')[1]);
                const endHour = parseInt(endTime.split('T')[1].split(':')[0]);
                const endMin = parseInt(endTime.split('T')[1].split(':')[1]);
                
                // เช้า (08:00-12:00)
                if (startHour === 8 && startMin === 0 && endHour === 12 && endMin === 0) {
                    periodSelect.value = 'morning';
                }
                // บ่าย (13:00-17:00)
                else if (startHour === 13 && startMin === 0 && endHour === 17 && endMin === 0) {
                    periodSelect.value = 'afternoon';
                }
                // เต็มวัน (08:00-17:00)
                else if (startHour === 8 && startMin === 0 && endHour === 17 && endMin === 0) {
                    periodSelect.value = 'full_day';
                }
                else {
                    periodSelect.value = '';
                }
            }
        }

        // ฟังก์ชันอัปเดตเวลาตามช่วงเวลาที่เลือก
        function updateTimeFromPeriod() {
            const period = document.getElementById('visit_period').value;
            const startDate = document.getElementById('visit_start_datetime');
            const endDate = document.getElementById('visit_end_datetime');
            
            if (!startDate.value || !endDate.value) return;
            
            const datePart = startDate.value.split('T')[0]; // ดึงวันที่
            
            switch(period) {
                case 'morning':
                    startDate.value = datePart + 'T08:00';
                    endDate.value = datePart + 'T12:00';
                    break;
                case 'afternoon':
                    startDate.value = datePart + 'T13:00';
                    endDate.value = datePart + 'T17:00';
                    break;
                case 'full_day':
                    startDate.value = datePart + 'T08:00';
                    endDate.value = datePart + 'T17:00';
                    break;
                default:
                    // ถ้าเลือกค่าว่าง ไม่ต้องเปลี่ยนแปลง
                    break;
            }
            
            // อัปเดตวันที่จองห้องประชุม
            updateMeetingDate();
        }

        // ฟังก์ชันอัปเดตวันที่จองห้องประชุม
        function updateMeetingDate() {
            const visitStart = document.getElementById('visit_start_datetime').value;
            const meetingDate = document.getElementById('meeting_date');
            
            // ลบ !meetingDate.value ออก เพื่อให้มันอัปเดตค่าใหม่ทับเสมอ
            if (visitStart && meetingDate) {
                const datePart = visitStart.split('T')[0];
                meetingDate.value = datePart;
            }
        }

        // เรียกใช้เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            setDefaultDateTime();
        });

        // Inline fix for toggle
        function toggleMeetingFields() {
           const isChecked = document.getElementById('meeting_room_check').checked;
           const section = document.getElementById('meetingSection');
           const inputs = section.querySelectorAll('input, select');
           
           if (isChecked) {
               section.style.display = 'block';
               section.style.opacity = '0';
               setTimeout(() => section.style.opacity = '1', 10);
               
               // กำหนด required เฉพาะฟิลด์ที่จำเป็น
               inputs.forEach(input => {
                   if(input.id !== 'emailSearch') {
                       input.setAttribute('required', 'required');
                   }
               });
               
               // ตั้งค่าวันที่เริ่มต้นให้ meeting_date (ถ้ายังไม่มีค่า)
               const visitStart = document.getElementById('visit_start_datetime').value;
               const meetingDate = document.getElementById('meeting_date');
               if (visitStart && meetingDate && !meetingDate.value) {
                   const datePart = visitStart.split('T')[0];
                   meetingDate.value = datePart;
               }
           } else {
               section.style.display = 'none';
               inputs.forEach(input => input.removeAttribute('required'));
           }
        }
        
        // Initial check
        toggleMeetingFields();
    </script>
<script>
    // Force CSS to update when theme changes
    (function() {
        const themeToggle = document.getElementById('themeToggle');
        const themeIconLight = document.getElementById('themeIconLight');
        const themeIconDark = document.getElementById('themeIconDark');
        
        // ตรวจสอบธีมที่บันทึกไว้
        const savedTheme = localStorage.getItem('vms_theme') || 'dark';
        
        // ฟังก์ชันตั้งค่าธีม
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
            
            // Force repaint
            document.body.style.display = 'none';
            document.body.offsetHeight; // Trigger reflow
            document.body.style.display = '';
        }
        
        // ตั้งค่าธีมเริ่มต้น
        setTheme(savedTheme);
        
        // Event listener สำหรับ toggle theme
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                if (this.checked) {
                    setTheme('light');
                } else {
                    setTheme('dark');
                }
            });
        }
    })();
</script>
</body>
</html>