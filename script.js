// ตัวแปร global
let selectedRequiredEmailList = [];
let selectedCCEmailList = [];
let searchTimeout;
let isSubmitting = false; // ป้องกันการ submit ซ้ำ

// Dictionary ภาษา (เพิ่มส่วน Alert เข้าไป)
const translations = {
    th: {
        subtitle: "ระบบจัดการผู้มาติดต่อและจองห้องประชุม",
        section_visitor: "ข้อมูลผู้มาติดต่อ",
        company: "บริษัท/หน่วยงาน",
        visitor_name: "ชื่อผู้มาติดต่อ",
        purpose: "วัตถุประสงค์",
        start_time: "เวลาเริ่มเข้าเยี่ยม",
        end_time: "เวลาสิ้นสุดเข้าเยี่ยม",
        period: "ช่วงเวลา",
        select_period: "เลือกช่วงเวลา",
        morning: "เช้า (08:00-12:00)",
        afternoon: "บ่าย (13:00-17:00)",
        full_day: "เต็มวัน (08:00-17:00)",
        visitor_type: "ประเภทผู้มาติดต่อ",
        need_welcome: "ต้องการ Welcome Board?",
        need_tour: "ต้องการเยี่ยมชมโรงงาน?",
        need_coffee: "ต้องการกาแฟ-น้ำดื่ม?",
        need_lunch: "ต้องการอาหารกลางวัน?",
        need_meeting: "ต้องการจองห้องประชุม?",
        need_microphone: "ต้องการไมค์?",
        need_headscarf: "ต้องการหมวก/ผ้าเย็น?",
        need_projector: "ต้องการโปรเจคเตอร์?",
        need_interpreter: "ต้องการล่าม?",
        section_meeting: "รายละเอียดการจอง",
        meeting_date: "วันที่จองห้องประชุม",
        meeting_start: "เริ่มประชุม",
        meeting_end: "สิ้นสุดประชุม",
        select_room: "เลือกห้องประชุม",
        select_room_ph: "เลือกห้องประชุม",
        required_email: "อีเมลผู้รับ",
        cc_email: "อีเมลผู้รับสำเนา",
        search_ph: "ค้นหาชื่อหรืออีเมล...",
        btn_submit: "ยืนยันข้อมูล",
        btn_reset: "ล้างค่า",
        field_note: "* วันที่เริ่มต้นดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ แต่สามารถแก้ไขได้",
        // --- เพิ่มส่วน Alert ---
        alert_date_invalid: "วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่ม",
        alert_meeting_time_req: "กรุณากรอกเวลาประชุม",
        alert_meeting_time_invalid: "เวลาเริ่มประชุมต้องน้อยกว่าเวลาสิ้นสุดประชุม",
        alert_required_email: "กรุณาเลือกอีเมลผู้รับอย่างน้อย 1 ท่าน",
        btn_saving: "กำลังบันทึก...",
        error_prefix: "เกิดข้อผิดพลาด: ",
        confirm_success: "บันทึกข้อมูลเรียบร้อยแล้ว"
    },
    en: {
        subtitle: "Visitor Management & Meeting Room Booking",
        section_visitor: "Visitor Information",
        company: "Company/Department",
        visitor_name: "Visitor Name",
        purpose: "Purpose of Visit",
        start_time: "Start Date & Time",
        end_time: "End Date & Time",
        period: "Visit Period",
        select_period: "Select Period",
        morning: "Morning (08:00-12:00)",
        afternoon: "Afternoon (13:00-17:00)",
        full_day: "Full Day (08:00-17:00)",
        visitor_type: "Visitor Type",
        need_welcome: "Require Welcome Board?",
        need_tour: "Require Factory Tour?",
        need_coffee: "Need Coffee & Drinks?",
        need_lunch: "Need Lunch?",
        need_meeting: "Book Meeting Room?",
        need_microphone: "Need Microphone?",
        need_headscarf: "Need Cap/Refreshing towel?",
        need_projector: "Need Projector?",
        need_interpreter: "Need Interpreter?",
        section_meeting: "Booking Details",
        meeting_date: "Meeting Date",
        meeting_start: "Start Time",
        meeting_end: "End Time",
        select_room: "Select Room",
        select_room_ph: "Select Room",
        required_email: "Required Email Recipients",
        cc_email: "CC Email Recipients",
        search_ph: "Type to search name or email...",
        btn_submit: "Submit Data",
        btn_reset: "Reset Form",
        field_note: "* Default date from visit start, but you can edit",
        // --- Added Alerts ---
        alert_date_invalid: "End date must be after or equal to start date",
        alert_meeting_time_req: "Please enter meeting time",
        alert_meeting_time_invalid: "Meeting start time must be before end time",
        alert_required_email: "Please select at least 1 required email recipient",
        btn_saving: "Saving...",
        error_prefix: "Error: ",
        confirm_success: "Data saved successfully"
    }
};

// ฟังก์ชันตั้งค่าภาษา
function setLanguage(lang) {
    // บันทึกลง LocalStorage
    localStorage.setItem('vms_lang', lang);

    // เปลี่ยนสี Label TH/EN
    const labelTH = document.getElementById('langLabel_TH');
    const labelEN = document.getElementById('langLabel_EN');
    
    if (labelTH && labelEN) {
        if (lang === 'th') {
            labelTH.classList.add('active');
            labelEN.classList.remove('active');
        } else {
            labelTH.classList.remove('active');
            labelEN.classList.add('active');
        }
    }

    // วนลูปเปลี่ยนข้อความทั้งหมดที่มี data-i18n
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
            el.innerText = translations[lang][key];
        }
    });

    // เปลี่ยน Placeholder (สำหรับช่องค้นหา)
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (translations[lang] && translations[lang][key]) {
            el.placeholder = translations[lang][key];
        }
    });

    // เปลี่ยน field note
    const fieldNote = document.querySelector('.field-note');
    if (fieldNote && translations[lang] && translations[lang]['field_note']) {
        fieldNote.innerText = translations[lang]['field_note'];
    }
}

// แสดง/ซ่อนฟิลด์การจองห้องประชุม
function toggleMeetingFields() {
    const meetingRoomCheck = document.getElementById('meeting_room_check');
    const meetingSection = document.getElementById('meetingSection');
    const meetingFields = document.querySelectorAll('.meeting-field');
    
    if (meetingRoomCheck && meetingRoomCheck.checked) {
        meetingSection.style.display = 'block';
        meetingSection.style.opacity = '0';
        setTimeout(() => meetingSection.style.opacity = '1', 10);
        // set required เฉพาะ field จริง ไม่รวม search input ที่ไม่ได้ส่ง form
        meetingFields.forEach(field => {
            if (field.id !== 'requiredEmailSearch' && field.id !== 'ccEmailSearch') {
                field.required = true;
            }
        });
        updateMeetingDate();
    } else {
        meetingSection.style.display = 'none';
        meetingFields.forEach(field => field.required = false);
    }
}

// อัปเดตวันที่จองห้องประชุม
function updateMeetingDate() {
    const visitStart = document.getElementById('visit_start_datetime').value;
    const meetingDate = document.getElementById('meeting_date');

    if (visitStart && meetingDate) {
        const datePart = visitStart.split('T')[0];
        meetingDate.value = datePart;
    }
}

// ฟังก์ชันค้นหาอีเมลผ่าน API (สำหรับ Required Email)
async function searchRequiredEmails(searchTerm) {
    if (searchTerm.length < 2) {
        document.getElementById('requiredEmailSearchResults').style.display = 'none';
        return;
    }
    
    try {
        const response = await fetch(`get_emails.php?term=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();
        
        if (data.error) {
            console.error('API Error:', data.error);
            displayRequiredSearchResults([], data.error);
            return;
        }
        
        if (Array.isArray(data)) {
            displayRequiredSearchResults(data);
        } else {
            displayRequiredSearchResults([], 'รูปแบบข้อมูลไม่ถูกต้อง');
        }
    } catch (error) {
        console.error('Search error:', error);
        displayRequiredSearchResults([], 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    }
}

// แสดงผลการค้นหา (Required Email)
function displayRequiredSearchResults(results, errorMessage = null) {
    const searchResults = document.getElementById('requiredEmailSearchResults');
    
    if (errorMessage) {
        searchResults.innerHTML = `<div class="search-result-item error">${errorMessage}</div>`;
    } else if (!results || results.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item no-results">ไม่พบผลการค้นหา</div>';
    } else {
        let html = '';
        results.forEach(result => {
            const email = result.email || result.value;
            const name = result.name || '';
            const department = result.department || '';
            
            const isSelected = selectedRequiredEmailList.some(item => item.email === email);
            if (!isSelected) {
                html += `
                    <div class="search-result-item" onclick="selectRequiredEmail('${email}', '${name}', '${department}')">
                        <span class="result-name">${name}</span>
                        <span class="result-email">${email}</span>
                        <small class="result-dept">${department}</small>
                    </div>
                `;
            }
        });
        
        if (html === '') {
            html = '<div class="search-result-item no-results">เลือกรายการอื่นแล้ว</div>';
        }
        
        searchResults.innerHTML = html;
    }
    searchResults.style.display = 'block';
}

// เลือกอีเมล (Required)
window.selectRequiredEmail = function(email, name, department) {
    selectedRequiredEmailList.push({ email, name, department });
    
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'required_recipients[]';
    hiddenInput.value = email;
    hiddenInput.id = `req_email_${email.replace(/[@.]/g, '_')}`;
    document.getElementById('visitorForm').appendChild(hiddenInput);
    
    const selectedEmails = document.getElementById('selectedRequiredEmails');
    const selectedTag = document.createElement('div');
    selectedTag.className = 'selected-email-tag required-tag';
    selectedTag.id = `req_tag_${email.replace(/[@.]/g, '_')}`;
    selectedTag.innerHTML = `
        <span class="selected-email-info">
            <span class="selected-name">${name}</span>
            <span class="selected-email">${email}</span>
        </span>
        <span class="remove-email" onclick="removeRequiredEmail('${email}')">&times;</span>
    `;
    selectedEmails.appendChild(selectedTag);
    
    document.getElementById('requiredEmailSearch').value = '';
    document.getElementById('requiredEmailSearchResults').style.display = 'none';
};

// ลบอีเมลที่เลือก (Required)
window.removeRequiredEmail = function(email) {
    selectedRequiredEmailList = selectedRequiredEmailList.filter(item => item.email !== email);
    
    const hiddenId = `req_email_${email.replace(/[@.]/g, '_')}`;
    const hiddenInput = document.getElementById(hiddenId);
    if (hiddenInput) hiddenInput.remove();
    
    const tagId = `req_tag_${email.replace(/[@.]/g, '_')}`;
    const tag = document.getElementById(tagId);
    if (tag) tag.remove();
};

// ฟังก์ชันค้นหาอีเมลผ่าน API (สำหรับ CC Email)
async function searchCCEmails(searchTerm) {
    if (searchTerm.length < 2) {
        document.getElementById('ccEmailSearchResults').style.display = 'none';
        return;
    }
    
    try {
        const response = await fetch(`get_emails.php?term=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();
        
        if (data.error) {
            console.error('API Error:', data.error);
            displayCCSearchResults([], data.error);
            return;
        }
        
        if (Array.isArray(data)) {
            displayCCSearchResults(data);
        } else {
            displayCCSearchResults([], 'รูปแบบข้อมูลไม่ถูกต้อง');
        }
    } catch (error) {
        console.error('Search error:', error);
        displayCCSearchResults([], 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    }
}

// แสดงผลการค้นหา (CC Email)
function displayCCSearchResults(results, errorMessage = null) {
    const searchResults = document.getElementById('ccEmailSearchResults');
    
    if (errorMessage) {
        searchResults.innerHTML = `<div class="search-result-item error">${errorMessage}</div>`;
    } else if (!results || results.length === 0) {
        searchResults.innerHTML = '<div class="search-result-item no-results">ไม่พบผลการค้นหา</div>';
    } else {
        let html = '';
        results.forEach(result => {
            const email = result.email || result.value;
            const name = result.name || '';
            const department = result.department || '';
            
            const isSelected = selectedCCEmailList.some(item => item.email === email);
            if (!isSelected) {
                html += `
                    <div class="search-result-item" onclick="selectCCEmail('${email}', '${name}', '${department}')">
                        <span class="result-name">${name}</span>
                        <span class="result-email">${email}</span>
                        <small class="result-dept">${department}</small>
                    </div>
                `;
            }
        });
        
        if (html === '') {
            html = '<div class="search-result-item no-results">เลือกรายการอื่นแล้ว</div>';
        }
        
        searchResults.innerHTML = html;
    }
    searchResults.style.display = 'block';
}

// เลือกอีเมล (CC)
window.selectCCEmail = function(email, name, department) {
    selectedCCEmailList.push({ email, name, department });
    
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'cc_recipients[]';
    hiddenInput.value = email;
    hiddenInput.id = `cc_email_${email.replace(/[@.]/g, '_')}`;
    document.getElementById('visitorForm').appendChild(hiddenInput);
    
    const selectedEmails = document.getElementById('selectedCCEmails');
    const selectedTag = document.createElement('div');
    selectedTag.className = 'selected-email-tag cc-tag';
    selectedTag.id = `cc_tag_${email.replace(/[@.]/g, '_')}`;
    selectedTag.innerHTML = `
        <span class="selected-email-info">
            <span class="selected-name">${name}</span>
            <span class="selected-email">${email}</span>
        </span>
        <span class="remove-email" onclick="removeCCEmail('${email}')">&times;</span>
    `;
    selectedEmails.appendChild(selectedTag);
    
    document.getElementById('ccEmailSearch').value = '';
    document.getElementById('ccEmailSearchResults').style.display = 'none';
};

// ลบอีเมลที่เลือก (CC)
window.removeCCEmail = function(email) {
    selectedCCEmailList = selectedCCEmailList.filter(item => item.email !== email);
    
    const hiddenId = `cc_email_${email.replace(/[@.]/g, '_')}`;
    const hiddenInput = document.getElementById(hiddenId);
    if (hiddenInput) hiddenInput.remove();
    
    const tagId = `cc_tag_${email.replace(/[@.]/g, '_')}`;
    const tag = document.getElementById(tagId);
    if (tag) tag.remove();
};

// ตรวจสอบวันที่
function validateDates() {
    const startDate = document.getElementById('visit_start_datetime');
    const endDate = document.getElementById('visit_end_datetime');
    
    // ดึงภาษาปัจจุบันมาใช้สำหรับ Alert
    const currentLang = localStorage.getItem('vms_lang') || 'th';
    const t = translations[currentLang];

    if (startDate.value && endDate.value) {
        // เปรียบเทียบ string โดยตรง เพราะ datetime-local format คือ YYYY-MM-DDTHH:MM
        // ซึ่งสามารถ sort ตามตัวอักษรได้ถูกต้องเสมอ ไม่มีปัญหา timezone
        if (startDate.value > endDate.value) {
            alert(t.alert_date_invalid);
            return;
        }
    }
    updateMeetingDate();
}

// ตั้งค่าวันที่เริ่มต้น
function setDefaultDates() {
    const now = new Date();
    const endTime = new Date(now);
    endTime.setHours(endTime.getHours() + 1); // เพิ่ม 1 ชั่วโมง
    
    function formatDateTime(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    const startDate = document.getElementById('visit_start_datetime');
    const endDate = document.getElementById('visit_end_datetime');
    
    if (startDate) startDate.value = formatDateTime(now);
    if (endDate) endDate.value = formatDateTime(endTime);
    updateMeetingDate();
}

// แสดง Alert
function showAlert(type, message) {
    const oldAlert = document.querySelector('.alert');
    if (oldAlert) oldAlert.remove();
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    
    const form = document.getElementById('visitorForm');
    form.insertBefore(alert, form.firstChild);
    
    // Auto hide after 3 seconds
    setTimeout(() => alert.remove(), 3000);
}

// รีเซ็ตฟอร์ม
function resetForm() {
    const form = document.getElementById('visitorForm');
    form.reset();
    
    document.getElementById('welcome_board').value = '0';
    document.getElementById('welcome_board_check').checked = false;
    document.getElementById('factory_tour').value = '0';
    document.getElementById('factory_tour_check').checked = false;
    document.getElementById('coffee_snack').value = '0';
    document.getElementById('coffee_snack_check').checked = false;
    document.getElementById('lunch').value = '0';
    document.getElementById('lunch_check').checked = false;
    document.getElementById('meeting_room').value = '0';
    document.getElementById('meeting_room_check').checked = false;
    document.getElementById('microphone_request').value = '1';
    document.getElementById('microphone_request_check').checked = true;
    document.getElementById('interpreter_request').value = '1';
    document.getElementById('interpreter_request_check').checked = true;
    document.getElementById('headscarf_request').value = '1';
    document.getElementById('headscarf_request_check').checked = true;
    
    selectedRequiredEmailList = [];
    selectedCCEmailList = [];
    document.getElementById('selectedRequiredEmails').innerHTML = '';
    document.getElementById('selectedCCEmails').innerHTML = '';
    document.querySelectorAll('input[name="required_recipients[]"]').forEach(input => input.remove());
    document.querySelectorAll('input[name="cc_recipients[]"]').forEach(input => input.remove());
    
    toggleMeetingFields();
    setDefaultDates();
}

// ฟังก์ชันกู้คืนปุ่ม submit
function restoreSubmitButton() {
    const submitBtn = document.querySelector('.btn-submit');
    if (submitBtn) {
        const currentLang = localStorage.getItem('vms_lang') || 'th';
        const btnText = translations[currentLang]['btn_submit'] || 'ยืนยันข้อมูล';
        submitBtn.innerHTML = `<span class="btn-content"><i class="fas fa-paper-plane"></i> <span data-i18n="btn_submit">${btnText}</span></span><div class="btn-glow"></div>`;
        submitBtn.disabled = false;
        isSubmitting = false;
    }
}

// Event Listeners หลัก
document.addEventListener('DOMContentLoaded', function() {
    // ตั้งค่าวันที่เริ่มต้น
    setDefaultDates();

    // ✅ Auto-check all toggles by default (user can turn off if not needed)
    const autoChecks = [
        { checkId: 'welcome_board_check',       hiddenId: 'welcome_board' },
        { checkId: 'factory_tour_check',        hiddenId: 'factory_tour' },
        { checkId: 'coffee_snack_check',        hiddenId: 'coffee_snack' },
        { checkId: 'lunch_check',               hiddenId: 'lunch' },
        { checkId: 'microphone_request_check',  hiddenId: 'microphone_request' },
        { checkId: 'interpreter_request_check', hiddenId: 'interpreter_request' },
        { checkId: 'headscarf_request_check',   hiddenId: 'headscarf_request' },
    ];
    autoChecks.forEach(({ checkId, hiddenId }) => {
        const chk = document.getElementById(checkId);
        const hid = document.getElementById(hiddenId);
        if (chk && hid) { chk.checked = true; hid.value = '1'; }
    });
    
    // ตั้งค่าภาษาเริ่มต้น
    const languageToggle = document.getElementById('languageToggle');
    const currentLang = localStorage.getItem('vms_lang') || 'th';
    
    // ตั้งค่าสถานะปุ่ม Toggle
    if (languageToggle) {
        if (currentLang === 'en') {
            languageToggle.checked = true;
        }
        
        // Event Listener เมื่อกดปุ่มเปลี่ยนภาษา
        languageToggle.addEventListener('change', function() {
            const lang = this.checked ? 'en' : 'th';
            setLanguage(lang);
        });
    }
    
    // เรียกใช้ setLanguage ครั้งแรก
    setLanguage(currentLang);
    
    // Required Email search
    const requiredEmailSearch = document.getElementById('requiredEmailSearch');
    const requiredSearchResults = document.getElementById('requiredEmailSearchResults');
    
    if (requiredEmailSearch) {
        requiredEmailSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();
            
            if (searchTerm.length >= 2) {
                searchTimeout = setTimeout(() => searchRequiredEmails(searchTerm), 300);
            } else {
                requiredSearchResults.style.display = 'none';
            }
        });
        
        requiredEmailSearch.addEventListener('focus', function() {
            if (this.value.length >= 2) searchRequiredEmails(this.value);
        });
        
        document.addEventListener('click', function(e) {
            if (!requiredEmailSearch.contains(e.target) && !requiredSearchResults.contains(e.target)) {
                requiredSearchResults.style.display = 'none';
            }
        });
    }
    
    // CC Email search
    const ccEmailSearch = document.getElementById('ccEmailSearch');
    const ccSearchResults = document.getElementById('ccEmailSearchResults');
    
    if (ccEmailSearch) {
        ccEmailSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();
            
            if (searchTerm.length >= 2) {
                searchTimeout = setTimeout(() => searchCCEmails(searchTerm), 300);
            } else {
                ccSearchResults.style.display = 'none';
            }
        });
        
        ccEmailSearch.addEventListener('focus', function() {
            if (this.value.length >= 2) searchCCEmails(this.value);
        });
        
        document.addEventListener('click', function(e) {
            if (!ccEmailSearch.contains(e.target) && !ccSearchResults.contains(e.target)) {
                ccSearchResults.style.display = 'none';
            }
        });
    }
    
    // Date validation
    const startDate = document.getElementById('visit_start_datetime');
    const endDate = document.getElementById('visit_end_datetime');
    
    if (startDate) startDate.addEventListener('change', validateDates);
    if (endDate) endDate.addEventListener('change', validateDates);
    
    // Toggle switches
    const welcomeBoardCheck = document.getElementById('welcome_board_check');
    if (welcomeBoardCheck) {
        welcomeBoardCheck.addEventListener('change', function() {
            document.getElementById('welcome_board').value = this.checked ? '1' : '0';
        });
    }
    
    const factoryTourCheck = document.getElementById('factory_tour_check');
    if (factoryTourCheck) {
        factoryTourCheck.addEventListener('change', function() {
            document.getElementById('factory_tour').value = this.checked ? '1' : '0';
        });
    }
    
    const coffeeSnackCheck = document.getElementById('coffee_snack_check');
    if (coffeeSnackCheck) {
        coffeeSnackCheck.addEventListener('change', function() {
            document.getElementById('coffee_snack').value = this.checked ? '1' : '0';
        });
    }
    
    const lunchCheck = document.getElementById('lunch_check');
    if (lunchCheck) {
        lunchCheck.addEventListener('change', function() {
            document.getElementById('lunch').value = this.checked ? '1' : '0';
        });
    }
    
    const meetingRoomCheck = document.getElementById('meeting_room_check');
    if (meetingRoomCheck) {
        meetingRoomCheck.addEventListener('change', function() {
            document.getElementById('meeting_room').value = this.checked ? '1' : '0';
            toggleMeetingFields();
        });
    }

    const microphoneCheck = document.getElementById('microphone_request_check');
    if (microphoneCheck) {
        microphoneCheck.addEventListener('change', function() {
            document.getElementById('microphone_request').value = this.checked ? '1' : '0';
        });
    }

    const interpreterCheck = document.getElementById('interpreter_request_check');
    if (interpreterCheck) {
        interpreterCheck.addEventListener('change', function() {
            document.getElementById('interpreter_request').value = this.checked ? '1' : '0';
        });
    }
    
    // Form submission - ใช้ครั้งเดียวเท่านั้น
    const form = document.getElementById('visitorForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // ดึงภาษาปัจจุบันมาใช้งาน
            const currentLang = localStorage.getItem('vms_lang') || 'th';
            const t = translations[currentLang];
            
            // ป้องกันการ submit ซ้ำ
            if (isSubmitting) {
                console.log('Already submitting, please wait...');
                return;
            }
            
            // ตรวจสอบวันที่ - แก้ไขให้ใช้ timestamp ในการเปรียบเทียบ
            const startDate = document.getElementById('visit_start_datetime');
            const endDate = document.getElementById('visit_end_datetime');
            
            if (startDate.value && endDate.value) {
                // เปรียบเทียบ string โดยตรง เพราะ datetime-local format คือ YYYY-MM-DDTHH:MM
                if (startDate.value > endDate.value) {
                    alert(t.alert_date_invalid);
                    return;
                }
            }
            
            // ตรวจสอบการจองห้องประชุม
            if (meetingRoomCheck && meetingRoomCheck.checked) {
                const meetingStart = document.getElementById('meeting_start').value;
                const meetingEnd = document.getElementById('meeting_end').value;
                
                if (!meetingStart || !meetingEnd) {
                    alert(t.alert_meeting_time_req);
                    return;
                }
                
                if (meetingStart >= meetingEnd) {
                    alert(t.alert_meeting_time_invalid);
                    return;
                }
                
                // ตรวจสอบว่ามีการเลือกอีเมลผู้รับอย่างน้อย 1 ท่าน
                if (selectedRequiredEmailList.length === 0) {
                    alert(t.alert_required_email);
                    return;
                }
            }
            
            // แสดง Loading
            isSubmitting = true;
            const submitBtn = document.querySelector('.btn-submit');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = `<span class="spinner"></span> ${t.btn_saving}`;
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(this);
                
                // เพิ่มภาษาเลือกใน form data
                formData.append('language', currentLang);
                
                // เพิ่ม timestamp เพื่อป้องกัน cache
                formData.append('_timestamp', Date.now());
                
                const response = await fetch('save_visitor.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    
                    // รอ 1.5 วินาทีแล้ว refresh หน้า
                    setTimeout(() => {
                        window.location.reload(); 
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                    isSubmitting = false;
                }
            } catch (error) {
                showAlert('error', t.error_prefix + error.message);
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
                isSubmitting = false;
            }
        });
    }
});

// ฟังก์ชันทดสอบ
function testEmailSearch() {
    fetch('get_emails.php?term=a')
        .then(response => response.json())
        .then(data => {
            console.log('Search test result:', data);
        })
        .catch(error => {
            console.error('Test error:', error);
        });
}