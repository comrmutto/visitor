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
    <a href="manual.php" style="background:var(--card-bg);border:1px solid var(--border-color);color:var(--accent-color);padding:8px 14px;border-radius:8px;font-size:.85rem;text-decoration:none;backdrop-filter:blur(5px);transition:all 0.3s ease;">
        <i class="fas fa-book"></i> Manual
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
                    <input type="datetime-local" id="visit_start_datetime" name="visit_start_datetime" required onchange="updateMeetingDate();">
                </div>

                <div class="form-group">
                    <label class="static-label"><span data-i18n="end_time">เวลาสิ้นสุดเข้าเยี่ยม</span> <span class="required">*</span></label>
                    <input type="datetime-local" id="visit_end_datetime" name="visit_end_datetime" required>
                </div>
            </div>
            
            <div class="form-row">
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
                    <label for="coffee_snack" data-i18n="need_coffee">ต้องการกาแฟ-น้ำดื่ม?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="coffee_snack_check" onchange="document.getElementById('coffee_snack').value = this.checked ? '1' : '0'">
                        <label for="coffee_snack_check" class="slider"></label>
                        <input type="hidden" id="coffee_snack" name="coffee_snack" value="0">
                    </div>
                </div>

                <div class="form-group switch-group">
                    <label for="lunch" data-i18n="need_lunch">ต้องการอาหารกลางวัน?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="lunch_check" onchange="document.getElementById('lunch').value = this.checked ? '1' : '0'">
                        <label for="lunch_check" class="slider"></label>
                        <input type="hidden" id="lunch" name="lunch" value="0">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group switch-group">
                    <label for="microphone_request" data-i18n="need_microphone">ต้องการไมค์?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="microphone_request_check" onchange="document.getElementById('microphone_request').value = this.checked ? '1' : '0'">
                        <label for="microphone_request_check" class="slider"></label>
                        <input type="hidden" id="microphone_request" name="microphone_request" value="0">
                    </div>
                </div>
                <div class="form-group switch-group">
                    <label for="interpreter_request" data-i18n="need_interpreter">ต้องการล่าม?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="interpreter_request_check" onchange="document.getElementById('interpreter_request').value = this.checked ? '1' : '0'">
                        <label for="interpreter_request_check" class="slider"></label>
                        <input type="hidden" id="interpreter_request" name="interpreter_request" value="0">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group switch-group">
                    <label for="headscarf_request" data-i18n="need_headscarf">ต้องการหมวก/ผ้าเย็น?</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="headscarf_request_check" onchange="document.getElementById('headscarf_request').value = this.checked ? '1' : '0'">
                        <label for="headscarf_request_check" class="slider"></label>
                        <input type="hidden" id="headscarf_request" name="headscarf_request" value="0">
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
            <input type="date" id="meeting_date" name="meeting_date" class="meeting-field">
            <small class="field-note">* วันที่เริ่มต้นดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ แต่สามารถแก้ไขได้</small>
        </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="static-label"><span data-i18n="meeting_start">เริ่มประชุม</span> <span class="required">*</span></label>
                    <input type="time" id="meeting_start" name="meeting_start" class="meeting-field">
                </div>

                <div class="form-group">
                    <label class="static-label"><span data-i18n="meeting_end">สิ้นสุดประชุม</span> <span class="required">*</span></label>
                    <input type="time" id="meeting_end" name="meeting_end" class="meeting-field">
                </div>
            </div>

            <div class="form-group">
                <label class="static-label"><span data-i18n="select_room">เลือกห้องประชุม</span> <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select id="meeting_room_select" name="meeting_room_select" class="meeting-field">
                        <option value="" data-i18n="select_room_ph">เลือกห้องประชุม</option>
                        <option value="Meeting Room 1">Meeting Room 1</option>
                        <option value="Meeting Room 2">Meeting Room 2</option>
                        <option value="Meeting Room 3">Meeting Room 3</option>
                        <option value="Meeting Room 4">Meeting Room 4</option>
                    </select>
                </div>
            </div>
            
            <!-- ช่องเลือกอีเมลผู้รับ (Required) -->
            <div class="form-group">
                <label class="static-label" data-i18n="required_email">อีเมลผู้รับ <span class="required">*</span></label>
                <div class="email-selector">
                    <div class="email-search-row">
                        <div class="email-search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="requiredEmailSearch" class="email-search-input" placeholder="ค้นหาชื่อหรืออีเมล..." data-i18n-placeholder="search_ph">
                            <div id="requiredEmailSearchResults" class="search-results"></div>
                        </div>
                        <button type="button" class="btn-browse-email" onclick="openEmailModal('required')" title="เลือกจากรายชื่อทั้งหมด">
                            <i class="fas fa-address-book"></i> <span data-i18n="browse_all"></span>
                        </button>
                    </div>
                    <div class="selected-emails" id="selectedRequiredEmails"></div>
                </div>
                <small class="field-note">* กรุณาเลือกอีเมลผู้รับอย่างน้อย 1 ท่าน</small>
            </div>

            <!-- ช่องเลือกอีเมลผู้รับสำเนา (CC) -->
            <div class="form-group">
                <label class="static-label" data-i18n="cc_email">อีเมลผู้รับสำเนา</label>
                <div class="email-selector">
                    <div class="email-search-row">
                        <div class="email-search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="ccEmailSearch" class="email-search-input" placeholder="ค้นหาชื่อหรืออีเมล..." data-i18n-placeholder="search_ph">
                            <div id="ccEmailSearchResults" class="search-results"></div>
                        </div>
                        <button type="button" class="btn-browse-email" onclick="openEmailModal('cc')" title="เลือกจากรายชื่อทั้งหมด">
                            <i class="fas fa-address-book"></i> <span data-i18n="browse_all"></span>
                        </button>
                    </div>
                    <div class="selected-emails" id="selectedCCEmails"></div>
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
<!-- Email Picker Modal -->
<div id="emailPickerModal" class="email-modal-overlay" onclick="if(event.target===this)closeEmailModal()">
    <div class="email-modal">
        <div class="email-modal-header">
            <h3 id="emailModalTitle"><i class="fas fa-address-book"></i> <span>Select Email to</span></h3>
            <button type="button" class="email-modal-close" onclick="closeEmailModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="email-modal-search">
            <i class="fas fa-search"></i>
            <input type="text" id="modalSearchInput" placeholder="Search by name or email..." oninput="filterModalList()">
        </div>
        <div class="email-modal-actions-top">
            <button type="button" class="btn-select-all" onclick="toggleSelectAll()">
                <i class="fas fa-check-square"></i> Select All
            </button>
            <span class="selected-count" id="selectedCount">Selected 0 items</span>
        </div>
        <div class="email-modal-list" id="emailModalList">
            <!-- Populated by JS -->
        </div>
        <div class="email-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeEmailModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn-modal-confirm" onclick="confirmEmailSelection()">
                <i class="fas fa-check"></i> Confirm Selection (<span id="confirmCount">0</span>)
            </button>
        </div>
    </div>
</div>

    <script src="script.js"></script>
    <style>
/* ===== Email Picker Modal ===== */
.btn-browse-email {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    height: 40px;
    white-space: nowrap;
    background: var(--primary-color, #6c63ff);
    color: #fff;
    border: none;
    border-radius: 0 10px 10px 0;
    font-size: 0.82rem;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.btn-browse-email:hover { filter: brightness(1.12); }

.email-search-row {
    display: flex !important;
    flex-direction: row !important;
    align-items: stretch !important;
    overflow: visible;
    gap: 0;
    width: 100%;
}
.email-search-row .email-search-container {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    width: auto !important;
    border: 1px solid var(--border-color, #ddd) !important;
    border-right: none !important;
    border-radius: 10px 0 0 10px !important;
    position: relative;
    display: flex !important;
    align-items: center;
}
.email-search-row .btn-browse-email {
    flex: 0 0 auto !important;
    width: auto !important;
    display: inline-flex !important;
    border-radius: 0 10px 10px 0 !important;
    margin: 0 !important;
    align-self: stretch !important;
    padding: 0 16px !important;
}

.email-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}
.email-modal-overlay.is-open {
    display: flex;
    animation: fadeInOverlay .2s ease;
}
@keyframes fadeInOverlay { from { opacity:0 } to { opacity:1 } }

.email-modal {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #ddd);
    border-radius: 16px;
    width: min(680px, 95vw);
    max-height: 82vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    animation: slideUpModal .25s ease;
}
@keyframes slideUpModal { from { transform: translateY(30px); opacity:0 } to { transform: translateY(0); opacity:1 } }

.email-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color, #eee);
}
.email-modal-header h3 { margin: 0; font-size: 1rem; color: var(--primary-color, #6c63ff); }
.email-modal-close {
    background: none; border: none; cursor: pointer;
    color: var(--text-muted, #888); font-size: 1.1rem; padding: 4px 8px; border-radius: 6px;
    transition: background .15s;
}
.email-modal-close:hover { background: var(--hover-bg, rgba(0,0,0,.07)); }

.email-modal-search {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border-color, #eee);
}
.email-modal-search i { color: var(--text-muted, #aaa); }
.email-modal-search input {
    flex: 1; border: none; background: transparent;
    font-size: 0.9rem; outline: none;
    color: var(--text-color, #333);
    font-family: inherit;
}

.email-modal-actions-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 20px;
    background: var(--hover-bg, rgba(0,0,0,.03));
    border-bottom: 1px solid var(--border-color, #eee);
}
.btn-select-all {
    background: none; border: 1px solid var(--border-color, #ddd);
    border-radius: 6px; padding: 4px 10px; font-size: 0.8rem;
    cursor: pointer; color: var(--text-color, #555); font-family: inherit;
    transition: all .15s;
}
.btn-select-all:hover { border-color: var(--primary-color, #6c63ff); color: var(--primary-color, #6c63ff); }
.selected-count { font-size: 0.8rem; color: var(--text-muted, #888); }

.email-modal-list {
    flex: 1; overflow-y: auto; padding: 8px 12px;
}

.email-modal-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid var(--border-color, #f0f0f0);
}
.email-modal-item:last-child { border-bottom: none; }
.email-modal-item:hover { background: var(--hover-bg, rgba(108,99,255,.07)); }
.email-modal-item.checked { background: rgba(108,99,255,.1); }

.email-modal-item input[type="checkbox"] {
    width: 17px; height: 17px; cursor: pointer;
    accent-color: var(--primary-color, #6c63ff);
    flex-shrink: 0;
}

.email-modal-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 0.85rem; color: #fff;
    flex-shrink: 0;
}

.email-modal-info { flex: 1; min-width: 0; }
.email-modal-info .name { font-size: 0.88rem; font-weight: 500; color: var(--text-color, #333); }
.email-modal-info .email { font-size: 0.78rem; color: var(--text-muted, #888); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.email-modal-info .dept { font-size: 0.75rem; color: var(--accent-color, #a0a0b0); margin-top: 1px; }

.email-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    padding: 14px 20px;
    border-top: 1px solid var(--border-color, #eee);
}
.btn-modal-cancel {
    padding: 8px 18px; border-radius: 8px; border: 1px solid var(--border-color, #ddd);
    background: none; cursor: pointer; font-size: 0.85rem; font-family: inherit;
    color: var(--text-muted, #888); transition: all .15s;
}
.btn-modal-cancel:hover { border-color: #aaa; color: #555; }
.btn-modal-confirm {
    padding: 8px 20px; border-radius: 8px; border: none;
    background: var(--primary-color, #6c63ff); color: #fff;
    cursor: pointer; font-size: 0.85rem; font-family: inherit;
    transition: all .15s; opacity: 0.92;
}
.btn-modal-confirm:hover { opacity: 1; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(108,99,255,.35); }

.email-modal-empty { text-align: center; padding: 40px; color: var(--text-muted, #aaa); font-size: 0.9rem; }

/* ===== Department Tag Styles ===== */
.dept-name {
    font-weight: 700;
    font-size: 0.75rem;
    margin-right: 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.email-name {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-main, #333);
}

/* สีพื้นหลัง สีขอบ และสีตัวอักษรของแต่ละแผนก */
.tag-dept-default { background: rgba(100, 116, 139, 0.15) !important; border: 1px solid rgba(100, 116, 139, 0.4) !important; }
.tag-dept-default .dept-name { color: #64748b; }

.tag-dept-hr { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.4) !important; }
.tag-dept-hr .dept-name { color: #ef4444; }

.tag-dept-it { background: rgba(59, 130, 246, 0.15) !important; border: 1px solid rgba(59, 130, 246, 0.4) !important; }
.tag-dept-it .dept-name { color: #3b82f6; }

/* เพิ่มแผนก ACC และ QC ตามรูปภาพ */
.tag-dept-acc, .tag-dept-finance { background: rgba(139, 92, 246, 0.15) !important; border: 1px solid rgba(139, 92, 246, 0.4) !important; }
.tag-dept-acc .dept-name, .tag-dept-finance .dept-name { color: #8b5cf6; }

.tag-dept-qc, .tag-dept-quality { background: rgba(236, 72, 153, 0.15) !important; border: 1px solid rgba(236, 72, 153, 0.4) !important; }
.tag-dept-qc .dept-name, .tag-dept-quality .dept-name { color: #ec4899; }

.tag-dept-marketing { background: rgba(245, 158, 11, 0.15) !important; border: 1px solid rgba(245, 158, 11, 0.4) !important; }
.tag-dept-marketing .dept-name { color: #f59e0b; }

.tag-dept-sales { background: rgba(168, 85, 247, 0.15) !important; border: 1px solid rgba(168, 85, 247, 0.4) !important; }
.tag-dept-sales .dept-name { color: #a855f7; }

.tag-dept-operations { background: rgba(6, 182, 212, 0.15) !important; border: 1px solid rgba(6, 182, 212, 0.4) !important; }
.tag-dept-operations .dept-name { color: #06b6d4; }
</style>

    <script>
// ===== Email Picker Modal Logic =====
(function() {
    // รายชื่อจาก PHP (inject as JSON)
    window._emailRecipients = <?= json_encode($email_recipients, JSON_UNESCAPED_UNICODE) ?>;
})();

// สีสำหรับ avatar
const AVATAR_COLORS = [
    '#6c63ff','#3498db','#2ecc71','#e67e22','#e74c3c',
    '#9b59b6','#1abc9c','#f39c12','#16a085','#8e44ad'
];
function avatarColor(name) {
    let h = 0;
    for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h << 5) - h);
    return AVATAR_COLORS[Math.abs(h) % AVATAR_COLORS.length];
}
function initials(name) {
    if (!name) return '?';
    const parts = name.trim().split(/\s+/);
    return (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
}

let _modalTarget = 'required'; // 'required' | 'cc'
let _modalTempSelected = new Set();

function openEmailModal(target) {
    _modalTarget = target;
    // โหลดค่าที่เลือกอยู่แล้วเข้าไปใน set
    const type = target === 'required' ? 'required' : 'cc';
    const existing = getSelectedEmailsForType(type);
    _modalTempSelected = new Set(existing);

    const modal = document.getElementById('emailPickerModal');
    const title = document.getElementById('emailModalTitle').querySelector('span');
    title.textContent = target === 'required' ? 'เลือกผู้รับอีเมล' : 'เลือกอีเมลผู้รับสำเนา (CC)';
    document.getElementById('modalSearchInput').value = '';

    modal.classList.add('is-open');
    renderModalList('');
    document.getElementById('modalSearchInput').focus();
}

function closeEmailModal() {
    document.getElementById('emailPickerModal').classList.remove('is-open');
    _modalTempSelected = new Set();
}

function getSelectedEmailsForType(type) {
    // ดึง email ที่ถูกเลือกอยู่แล้วจาก hidden inputs
    const container = type === 'required'
        ? document.getElementById('selectedRequiredEmails')
        : document.getElementById('selectedCCEmails');
    if (!container) return [];
    return Array.from(container.querySelectorAll('input[type="hidden"]')).map(i => i.value);
}

function renderModalList(query) {
    const list = document.getElementById('emailModalList');
    const recipients = window._emailRecipients || [];

    if (!recipients.length) {
        list.innerHTML = '<div class="email-modal-empty"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.4;"></i>ไม่มีรายชื่ออีเมลในระบบ</div>';
        updateModalCounts();
        return;
    }

    const q = query.toLowerCase().trim();
    const items = recipients.filter(r =>
        !q || (r.name||'').toLowerCase().includes(q) || (r.email||'').toLowerCase().includes(q) ||
        (r.department && r.department.toLowerCase().includes(q))
    );

    if (!items.length) {
        list.innerHTML = '<div class="email-modal-empty"><i class="fas fa-search" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.4;"></i>ไม่พบรายชื่อที่ค้นหา</div>';
        updateModalCounts();
        return;
    }

    list.innerHTML = items.map(r => {
        const checked = _modalTempSelected.has(r.email);
        const color = avatarColor(r.name || r.email);
        const init = initials(r.name || r.email);
        const safeEmail = r.email.replace(/'/g, "\\'");
        return `<div class="email-modal-item${checked ? ' checked' : ''}" onclick="toggleModalItem('${safeEmail}', this)">
            <input type="checkbox" ${checked ? 'checked' : ''} onclick="event.stopPropagation(); toggleModalItem('${safeEmail}', this.closest('.email-modal-item'))">
            <div class="email-modal-avatar" style="background:${color}">${init}</div>
            <div class="email-modal-info">
                <div class="name">${r.name || ''}</div>
                <div class="email">${r.email}</div>
                ${r.department ? `<div class="dept">${r.department}</div>` : ''}
            </div>
        </div>`;
    }).join('');
    updateModalCounts();
}

function toggleModalItem(email, row) {
    const cb = row.querySelector('input[type="checkbox"]');
    if (_modalTempSelected.has(email)) {
        _modalTempSelected.delete(email);
        row.classList.remove('checked');
        if (cb) cb.checked = false;
    } else {
        _modalTempSelected.add(email);
        row.classList.add('checked');
        if (cb) cb.checked = true;
    }
    updateModalCounts();
}

function updateModalCounts() {
    const n = _modalTempSelected.size;
    document.getElementById('selectedCount').textContent = `เลือก ${n} รายการ`;
    document.getElementById('confirmCount').textContent = n;
}

function filterModalList() {
    renderModalList(document.getElementById('modalSearchInput').value);
}

let _allSelected = false;
function toggleSelectAll() {
    const q = document.getElementById('modalSearchInput').value.toLowerCase().trim();
    const recipients = window._emailRecipients || [];
    const visibleItems = recipients.filter(r =>
        !q || (r.name||'').toLowerCase().includes(q) || (r.email||'').toLowerCase().includes(q)
    );
    const allChecked = visibleItems.every(r => _modalTempSelected.has(r.email));
    visibleItems.forEach(r => {
        if (allChecked) _modalTempSelected.delete(r.email);
        else _modalTempSelected.add(r.email);
    });
    renderModalList(document.getElementById('modalSearchInput').value);
}

function confirmEmailSelection() {
    // ดึง container ของ target
    const isRequired = _modalTarget === 'required';
    const containerId = isRequired ? 'selectedRequiredEmails' : 'selectedCCEmails';
    const container = document.getElementById(containerId);
    if (!container) { closeEmailModal(); return; }

    // หา script.js addEmail / removeEmail function ถ้ามี
    const currentEmails = new Set(getSelectedEmailsForType(_modalTarget === 'required' ? 'required' : 'cc'));

    // ลบที่ไม่ได้ติ๊กแล้ว
    currentEmails.forEach(email => {
        if (!_modalTempSelected.has(email)) {
            // ลบออกจาก container
            const tag = container.querySelector(`[data-email="${CSS.escape(email)}"]`);
            if (tag) tag.remove();
            const hidden = container.querySelector(`input[value="${CSS.escape(email)}"]`);
            if (hidden) hidden.remove();
        }
    });

    // เพิ่มที่เลือกใหม่
    _modalTempSelected.forEach(email => {
        if (!currentEmails.has(email)) {
            const rec = window._emailRecipients.find(r => r.email === email);
            if (!rec) return;
            // สร้าง tag ใหม่
            const tag = document.createElement('div');
            
            // Create department tag class
            const deptClass = rec.department ? `tag-dept-${rec.department.toLowerCase().replace(/\s+/g, '-')}` : 'tag-dept-default';
            tag.className = `selected-email-tag ${deptClass}`;
            tag.setAttribute('data-email', email);
            
            tag.innerHTML = `
                <span class="dept-name">[${rec.department ? rec.department.toUpperCase() : 'N/A'}]</span>
                <span class="email-name">${rec.name}</span>
                <span class="remove-email" onclick="this.parentElement.remove(); removeHiddenEmail('${isRequired ? 'required_emails' : 'cc_emails'}', '${email}')">&times;</span>
                <input type="hidden" name="${isRequired ? 'required_emails' : 'cc_emails'}[]" value="${email}">
            `;
            container.appendChild(tag);
        }
    });

    closeEmailModal();
}

// Helper ลบ hidden input (เผื่อ script.js ใช้วิธีต่างกัน)
function removeHiddenEmail(name, email) {
    document.querySelectorAll(`input[name="${name}[]"]`).forEach(el => {
        if (el.value === email) el.remove();
    });
}

// ===== End Email Picker Modal =====

// ฟังก์ชันตั้งค่าวันที่เริ่มต้นเป็นวันนี้
function setDefaultDateTime() {
    const today = new Date();
    const endTime = new Date(today);
    endTime.setHours(endTime.getHours() + 1); // เพิ่ม 1 ชั่วโมง
    
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const hours = String(today.getHours()).padStart(2, '0');
    const minutes = String(today.getMinutes()).padStart(2, '0');
    const endHours = String(endTime.getHours()).padStart(2, '0');
    
    // ตั้งค่าเริ่มต้น
    const startDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('visit_start_datetime').value = startDateTime;
    
    const endDateTime = `${year}-${month}-${day}T${endHours}:${minutes}`;
    document.getElementById('visit_end_datetime').value = endDateTime;
    
    // อัปเดตวันที่จองห้องประชุม
    updateMeetingDate();
}
// ฟังก์ชันตรวจสอบวันที่ (เพิ่มใน inline script ด้วย)
function validateDates() {
    // ไม่ทำอะไรระหว่างพิมพ์ — validate เฉพาะตอน submit (ดู submit handler)
    updateMeetingDate();
}
        // ฟังก์ชันอัปเดตวันที่จองห้องประชุม
        function updateMeetingDate() {
            const visitStart = document.getElementById('visit_start_datetime').value;
            const meetingDate = document.getElementById('meeting_date');
            
            if (visitStart && meetingDate) {
                const datePart = visitStart.split('T')[0];
                meetingDate.value = datePart;
            }
        }

        // เรียกใช้เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            setDefaultDateTime();

            // validate วันที่เฉพาะตอน submit เท่านั้น ไม่ยุ่งกับ input ระหว่างพิมพ์
            document.getElementById('visitorForm').addEventListener('submit', function(e) {
                const startEl = document.getElementById('visit_start_datetime');
                const endEl   = document.getElementById('visit_end_datetime');
                const startMs = startEl.valueAsNumber;
                const endMs   = endEl.valueAsNumber;

                if (!isNaN(startMs) && !isNaN(endMs) && endMs < startMs) {
                    e.preventDefault();
                    let errEl = document.getElementById('date_error_msg');
                    if (!errEl) {
                        errEl = document.createElement('p');
                        errEl.id = 'date_error_msg';
                        errEl.style.cssText = 'color:#e74c3c;font-size:13px;margin:4px 0 0 0;';
                        endEl.parentNode.appendChild(errEl);
                    }
                    errEl.textContent = '⚠️ วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่ม';
                    endEl.focus();
                } else {
                    const errEl = document.getElementById('date_error_msg');
                    if (errEl) errEl.textContent = '';
                }
            });
        });

        // Inline fix for toggle
        function toggleMeetingFields() {
           const isChecked = document.getElementById('meeting_room_check').checked;
           const section = document.getElementById('meetingSection');
           // เลือกเฉพาะ field จริง ไม่รวม search input ที่ไม่ได้ส่ง form
           const realFields = section.querySelectorAll('#meeting_date, #meeting_start, #meeting_end, #meeting_room_select');
           
           if (isChecked) {
               section.style.display = 'block';
               section.style.opacity = '0';
               setTimeout(() => section.style.opacity = '1', 10);
               
               // กำหนด required เฉพาะ field จริง เท่านั้น (ห้ามใส่ search input)
               realFields.forEach(field => field.setAttribute('required', 'required'));
               
               // ตั้งค่าวันที่เริ่มต้นให้ meeting_date (ถ้ายังไม่มีค่า)
               const visitStart = document.getElementById('visit_start_datetime').value;
               const meetingDate = document.getElementById('meeting_date');
               if (visitStart && meetingDate && !meetingDate.value) {
                   const datePart = visitStart.split('T')[0];
                   meetingDate.value = datePart;
               }
           } else {
               section.style.display = 'none';
               realFields.forEach(field => field.removeAttribute('required'));
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
        
        // ตรวจสอบธีมที่บันทึกไว้ (ค่าเริ่มต้นเป็นโหมดสว่าง)
        const savedTheme = localStorage.getItem('vms_theme') || 'light';
        
        // ฟังก์ชันตั้งค่าธีม
        function setTheme(theme) {
            if (theme === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('vms_theme', 'light');
                // โหมดสว่าง ให้สวิตช์อยู่ซ้าย (ดวงอาทิตย์)
                if (themeToggle) themeToggle.checked = false; 
                if (themeIconLight) themeIconLight.classList.add('active');
                if (themeIconDark) themeIconDark.classList.remove('active');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('vms_theme', 'dark');
                // โหมดมืด ให้สวิตช์อยู่ขวา (พระจันทร์)
                if (themeToggle) themeToggle.checked = true;  
                if (themeIconLight) themeIconLight.classList.remove('active');
                if (themeIconDark) themeIconDark.classList.add('active');
            }
            
            // Force repaint ป้องกัน CSS ค้าง
            document.body.style.display = 'none';
            document.body.offsetHeight; // Trigger reflow
            document.body.style.display = '';
        }
        
        // ตั้งค่าธีมเริ่มต้น
        setTheme(savedTheme);
        
        // Event listener สำหรับ toggle theme
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                // ถ้าสวิตช์ถูกเปิด (เลื่อนขวา) ให้เป็น dark, ถ้าปิด (เลื่อนซ้าย) ให้เป็น light
                setTheme(this.checked ? 'dark' : 'light');
            });
        }
    })();
</script>
</body>
</html>