// ตัวแปร global
let selectedEmailList = [];
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
        need_meeting: "ต้องการจองห้องประชุม?",
        section_meeting: "รายละเอียดการจอง",
        meeting_date: "วันที่จองห้องประชุม",
        meeting_start: "เริ่มประชุม",
        meeting_end: "สิ้นสุดประชุม",
        select_room: "เลือกห้องประชุม",
        select_room_ph: "เลือกห้องประชุม",
        cc_email: "อีเมลผู้รับสำเนา",
        search_ph: "ค้นหาชื่อหรืออีเมล...",
        btn_submit: "ยืนยันข้อมูล",
        btn_reset: "ล้างค่า",
        field_note: "* วันที่เริ่มต้นดึงจากวันที่เริ่มเข้าเยี่ยมอัตโนมัติ แต่สามารถแก้ไขได้",
        // --- เพิ่มส่วน Alert ---
        alert_date_invalid: "วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่ม",
        alert_meeting_time_req: "กรุณากรอกเวลาประชุม",
        alert_meeting_time_invalid: "เวลาเริ่มประชุมต้องน้อยกว่าเวลาสิ้นสุดประชุม",
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
        need_meeting: "Book Meeting Room?",
        section_meeting: "Booking Details",
        meeting_date: "Meeting Date",
        meeting_start: "Start Time",
        meeting_end: "End Time",
        select_room: "Select Room",
        select_room_ph: "Select Room",
        cc_email: "CC Email Recipients",
        search_ph: "Type to search name or email...",
        btn_submit: "Submit Data",
        btn_reset: "Reset Form",
        field_note: "* Default date from visit start, but you can edit",
        // --- Added Alerts ---
        alert_date_invalid: "End date must be after or equal to start date",
        alert_meeting_time_req: "Please enter meeting time",
        alert_meeting_time_invalid: "Meeting start time must be before end time",
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
        meetingFields.forEach(field => field.required = true);
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

// ฟังก์ชันค้นหาอีเมลผ่าน API
async function searchEmails(searchTerm) {
    if (searchTerm.length < 2) {
        document.getElementById('emailSearchResults').style.display = 'none';
        return;
    }
    
    try {
        const response = await fetch(`get_emails.php?term=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();
        
        if (data.error) {
            console.error('API Error:', data.error);
            displaySearchResults([], data.error);
            return;
        }
        
        if (Array.isArray(data)) {
            displaySearchResults(data);
        } else {
            displaySearchResults([], 'รูปแบบข้อมูลไม่ถูกต้อง');
        }
    } catch (error) {
        console.error('Search error:', error);
        displaySearchResults([], 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    }
}

// แสดงผลการค้นหา
function displaySearchResults(results, errorMessage = null) {
    const searchResults = document.getElementById('emailSearchResults');
    
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
            
            const isSelected = selectedEmailList.some(item => item.email === email);
            if (!isSelected) {
                html += `
                    <div class="search-result-item" onclick="selectEmail('${email}', '${name}', '${department}')">
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

// เลือกอีเมล
window.selectEmail = function(email, name, department) {
    selectedEmailList.push({ email, name, department });
    
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'email_recipients[]';
    hiddenInput.value = email;
    hiddenInput.id = `email_${email.replace(/[@.]/g, '_')}`;
    document.getElementById('visitorForm').appendChild(hiddenInput);
    
    const selectedEmails = document.getElementById('selectedEmails');
    const selectedTag = document.createElement('div');
    selectedTag.className = 'selected-email-tag';
    selectedTag.id = `tag_${email.replace(/[@.]/g, '_')}`;
    selectedTag.innerHTML = `
        <span class="selected-email-info">
            <span class="selected-name">${name}</span>
            <span class="selected-email">${email}</span>
        </span>
        <span class="remove-email" onclick="removeEmail('${email}')">&times;</span>
    `;
    selectedEmails.appendChild(selectedTag);
    
    document.getElementById('emailSearch').value = '';
    document.getElementById('emailSearchResults').style.display = 'none';
};

// ลบอีเมลที่เลือก
window.removeEmail = function(email) {
    selectedEmailList = selectedEmailList.filter(item => item.email !== email);
    
    const hiddenId = `email_${email.replace(/[@.]/g, '_')}`;
    const hiddenInput = document.getElementById(hiddenId);
    if (hiddenInput) hiddenInput.remove();
    
    const tagId = `tag_${email.replace(/[@.]/g, '_')}`;
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

    if (startDate.value && endDate.value && startDate.value > endDate.value) {
        alert(t.alert_date_invalid);
        endDate.value = startDate.value;
    }
    updateMeetingDate();
}

// ตั้งค่าวันที่เริ่มต้น
function setDefaultDates() {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    const startDate = document.getElementById('visit_start_datetime');
    const endDate = document.getElementById('visit_end_datetime');
    
    if (startDate) startDate.value = formatDate(now);
    if (endDate) endDate.value = formatDate(tomorrow);
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
    document.getElementById('meeting_room').value = '0';
    document.getElementById('meeting_room_check').checked = false;
    
    selectedEmailList = [];
    document.getElementById('selectedEmails').innerHTML = '';
    document.querySelectorAll('input[name="email_recipients[]"]').forEach(input => input.remove());
    
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
    
    // Email search
    const emailSearch = document.getElementById('emailSearch');
    const searchResults = document.getElementById('emailSearchResults');
    
    if (emailSearch) {
        emailSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();
            
            if (searchTerm.length >= 2) {
                searchTimeout = setTimeout(() => searchEmails(searchTerm), 300);
            } else {
                searchResults.style.display = 'none';
            }
        });
        
        emailSearch.addEventListener('focus', function() {
            if (this.value.length >= 2) searchEmails(this.value);
        });
        
        document.addEventListener('click', function(e) {
            if (!emailSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
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
    
    const meetingRoomCheck = document.getElementById('meeting_room_check');
    if (meetingRoomCheck) {
        meetingRoomCheck.addEventListener('change', function() {
            document.getElementById('meeting_room').value = this.checked ? '1' : '0';
            toggleMeetingFields();
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
            
            // ตรวจสอบวันที่
            if (startDate.value && endDate.value && startDate.value > endDate.value) {
                alert(t.alert_date_invalid); // ใชัข้อความตามภาษา
                return;
            }
            
            // ตรวจสอบการจองห้องประชุม
            if (meetingRoomCheck && meetingRoomCheck.checked) {
                const meetingStart = document.getElementById('meeting_start').value;
                const meetingEnd = document.getElementById('meeting_end').value;
                
                if (!meetingStart || !meetingEnd) {
                    alert(t.alert_meeting_time_req); // ใชัข้อความตามภาษา
                    return;
                }
                
                if (meetingStart >= meetingEnd) {
                    alert(t.alert_meeting_time_invalid); // ใชัข้อความตามภาษา
                    return;
                }
            }
            
            // แสดง Loading
            isSubmitting = true;
            const submitBtn = document.querySelector('.btn-submit');
            submitBtn.innerHTML = `<span class="spinner"></span> ${t.btn_saving}`; // ใชัข้อความตามภาษา
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
                    showAlert('success', data.message); // message จาก backend อาจจะต้องปรับถ้าอยากให้เป็นภาษาอังกฤษ 100% แต่ตรงนี้ alert ของระบบจะแสดงตามที่ส่งกลับมา
                    
                    // รอ 1.5 วินาทีแล้ว refresh หน้า
                    setTimeout(() => {
                        window.location.reload(); 
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                    restoreSubmitButton();
                }
            } catch (error) {
                showAlert('error', t.error_prefix + error.message); // ใชัข้อความตามภาษา
                restoreSubmitButton();
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
