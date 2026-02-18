<?php
/**
 * admin.php — VMS Admin Panel with Login Authentication
 * Credentials are defined in config.php:
 *   define('ADMIN_USER', 'admin');
 *   define('ADMIN_PASS', 'your_hashed_password');  // ใช้ password_hash()
 */

session_start();
require_once 'config.php';


// Session timeout 2 ชั่วโมง
define('SESSION_TIMEOUT', 7200);

// ============================================================
// Handle Logout
// ============================================================
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ============================================================
// Handle Login
// ============================================================
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $input_user = trim($_POST['username'] ?? '');
    $input_pass = $_POST['password'] ?? '';

    if ($input_user === ADMIN_USER && password_verify($input_pass, ADMIN_PASS)) {
        session_regenerate_id(true); // ป้องกัน session fixation
        $_SESSION['vms_admin']    = true;
        $_SESSION['admin_user']   = $input_user;
        $_SESSION['admin_login_time'] = time();
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        // Delay เล็กน้อยเพื่อป้องกัน brute force
        sleep(1);
    }
}

// ============================================================
// ตรวจสอบ Session — ถ้ายังไม่ login ให้แสดงหน้า Login
// ============================================================
$is_logged_in = (
    isset($_SESSION['vms_admin']) &&
    $_SESSION['vms_admin'] === true &&
    isset($_SESSION['admin_login_time']) &&
    (time() - $_SESSION['admin_login_time']) < SESSION_TIMEOUT
);

// อัปเดต last active time ถ้า login อยู่
if ($is_logged_in) {
    $_SESSION['admin_login_time'] = time();
}

// ============================================================
// หน้า Login (แสดงเมื่อยังไม่ได้ login)
// ============================================================
if (!$is_logged_in):
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — VMS</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --bg: #0b1120;
    --surface: rgba(255,255,255,0.05);
    --border: rgba(255,255,255,0.09);
    --primary: #38bdf8;
    --accent: #a78bfa;
    --red: #f87171;
    --text: #f1f5f9;
    --muted: #64748b;
}
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'Prompt', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Ambient glow background */
.glow {
    position: fixed;
    border-radius: 50%;
    filter: blur(120px);
    pointer-events: none;
    z-index: 0;
    opacity: 0.35;
}
.glow-1 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, #38bdf8, transparent 70%);
    top: -15%; left: -10%;
    animation: drift 12s ease-in-out infinite alternate;
}
.glow-2 {
    width: 450px; height: 450px;
    background: radial-gradient(circle, #a78bfa, transparent 70%);
    bottom: -15%; right: -10%;
    animation: drift 14s ease-in-out infinite alternate-reverse;
}
@keyframes drift { from { transform: translate(0,0); } to { transform: translate(40px, 30px); } }

/* Grid pattern overlay */
body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(56,189,248,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(56,189,248,.03) 1px, transparent 1px);
    background-size: 40px 40px;
    z-index: 0;
    pointer-events: none;
}

/* Login card */
.login-wrap {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

.login-card {
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 40px 36px;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    animation: slideUp .4s ease;
}
@keyframes slideUp {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
}

/* Logo / Header */
.login-logo {
    text-align: center;
    margin-bottom: 32px;
}
.login-logo .icon-wrap {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, rgba(56,189,248,.2), rgba(167,139,250,.2));
    border: 1px solid rgba(56,189,248,.3);
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 14px;
    box-shadow: 0 0 30px rgba(56,189,248,.15);
}
.login-logo h1 {
    font-size: 1.3rem;
    font-weight: 700;
    letter-spacing: 2px;
    color: var(--text);
}
.login-logo h1 span { color: var(--primary); }
.login-logo p {
    color: var(--muted);
    font-size: .82rem;
    margin-top: 4px;
}

/* Form */
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    font-size: .8rem;
    color: var(--muted);
    font-weight: 500;
    margin-bottom: 7px;
    letter-spacing: .3px;
}
.input-wrap {
    position: relative;
}
.input-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: .9rem;
    pointer-events: none;
}
.input-wrap input {
    width: 100%;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 13px 14px 13px 40px;
    color: var(--text);
    font-family: 'Prompt', sans-serif;
    font-size: .93rem;
    transition: border-color .25s, box-shadow .25s;
}
.input-wrap input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(56,189,248,.12);
}
.input-wrap input::placeholder { color: #475569; }

/* Toggle password visibility */
.toggle-pw {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    padding: 4px;
    transition: color .2s;
    font-size: .9rem;
}
.toggle-pw:hover { color: var(--text); }

/* Error alert */
.alert-error {
    background: rgba(248,113,113,.1);
    border: 1px solid rgba(248,113,113,.3);
    color: var(--red);
    border-radius: 10px;
    padding: 11px 14px;
    font-size: .87rem;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 9px;
    animation: shake .4s ease;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60%  { transform: translateX(-5px); }
    40%,80%  { transform: translateX(5px); }
}

/* Submit button */
.btn-login {
    width: 100%;
    background: linear-gradient(135deg, var(--primary), #3b82f6);
    border: none;
    border-radius: 12px;
    padding: 14px;
    color: #0b1120;
    font-family: 'Prompt', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    margin-top: 8px;
    transition: all .25s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    letter-spacing: .3px;
}
.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(56,189,248,.35);
}
.btn-login:active { transform: translateY(0); }

/* Footer note */
.login-footer {
    text-align: center;
    margin-top: 24px;
    color: #334155;
    font-size: .75rem;
}
.login-footer a {
    color: var(--muted);
    text-decoration: none;
}
.login-footer a:hover { color: var(--primary); }
</style>
</head>
<body>
<div class="glow glow-1"></div>
<div class="glow glow-2"></div>

<div class="login-wrap">
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <div class="icon-wrap">🔐</div>
            <h1>VMS <span>Admin</span></h1>
            <p>กรุณาเข้าสู่ระบบเพื่อจัดการข้อมูล</p>
        </div>

        <!-- Error message -->
        <?php if ($login_error): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($login_error) ?>
        </div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input
                        type="text"
                        name="username"
                        placeholder="กรอกชื่อผู้ใช้"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label>รหัสผ่าน</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        placeholder="กรอกรหัสผ่าน"
                        required
                    >
                    <button type="button" class="toggle-pw" onclick="togglePassword()" id="toggleBtn">
                        <i class="fas fa-eye" id="pwIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login_submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </button>
        </form>
    </div>

    <div class="login-footer">
        <a href="index.php"><i class="fas fa-arrow-left"></i> กลับหน้าหลัก</a>
    </div>
</div>

<script>
function togglePassword() {
    const input  = document.getElementById('passwordInput');
    const icon   = document.getElementById('pwIcon');
    const isText = input.type === 'text';
    input.type   = isText ? 'password' : 'text';
    icon.className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>
<?php
// หยุดแสดงผลส่วนที่เหลือ (ไม่ให้เห็น admin content)
exit;
endif;
// ============================================================
// ถ้า login แล้ว → โหลด admin content ต่อไป
// ============================================================

$msg      = '';
$msg_type = '';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$tab    = $_GET['tab'] ?? 'emails';

// ---- Email Recipients CRUD ----
if ($action === 'add_email') {
    $email = trim($_POST['email'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $dept  = trim($_POST['department'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $name) {
        $s = $conn->prepare("INSERT INTO email_recipients (email,name,department,is_active) VALUES (?,?,?,1)");
        $s->bind_param("sss", $email, $name, $dept);
        if ($s->execute()) { $msg='เพิ่มอีเมลเรียบร้อยแล้ว'; $msg_type='success'; }
        else { $msg='อีเมลนี้มีอยู่แล้ว หรือเกิดข้อผิดพลาด: '.$conn->error; $msg_type='error'; }
    } else { $msg='กรุณากรอกอีเมลและชื่อให้ถูกต้อง'; $msg_type='error'; }
}
if ($action === 'edit_email') {
    $id    = (int)$_POST['id'];
    $email = trim($_POST['email'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $dept  = trim($_POST['department'] ?? '');
    $active= (int)($_POST['is_active'] ?? 1);
    $s = $conn->prepare("UPDATE email_recipients SET email=?,name=?,department=?,is_active=? WHERE id=?");
    $s->bind_param("sssii", $email, $name, $dept, $active, $id);
    $s->execute(); $msg='แก้ไขข้อมูลเรียบร้อยแล้ว'; $msg_type='success';
}
if ($action === 'delete_email') {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM email_recipients WHERE id=$id");
    $msg='ลบข้อมูลเรียบร้อยแล้ว'; $msg_type='success';
}
if ($action === 'toggle_email') {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE email_recipients SET is_active = NOT is_active WHERE id=$id");
    $msg='เปลี่ยนสถานะเรียบร้อยแล้ว'; $msg_type='success';
}

// ---- Meeting Rooms CRUD ----
if ($action === 'add_room') {
    $name  = trim($_POST['room_name'] ?? '');
    $email = trim($_POST['room_email'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $s = $conn->prepare("INSERT INTO meeting_room_emails (room_name,room_email,description,is_active) VALUES (?,?,?,1)");
        $s->bind_param("sss", $name, $email, $desc);
        if ($s->execute()) { $msg='เพิ่มห้องประชุมเรียบร้อยแล้ว'; $msg_type='success'; }
        else { $msg='เกิดข้อผิดพลาด: '.$conn->error; $msg_type='error'; }
    } else { $msg='กรุณากรอกชื่อห้องและอีเมลให้ถูกต้อง'; $msg_type='error'; }
}
if ($action === 'edit_room') {
    $id    = (int)$_POST['id'];
    $name  = trim($_POST['room_name'] ?? '');
    $email = trim($_POST['room_email'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $active= (int)($_POST['is_active'] ?? 1);
    $s = $conn->prepare("UPDATE meeting_room_emails SET room_name=?,room_email=?,description=?,is_active=? WHERE id=?");
    $s->bind_param("sssii", $name, $email, $desc, $active, $id);
    $s->execute(); $msg='แก้ไขห้องประชุมเรียบร้อยแล้ว'; $msg_type='success';
}
if ($action === 'delete_room') {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM meeting_room_emails WHERE id=$id");
    $msg='ลบห้องประชุมเรียบร้อยแล้ว'; $msg_type='success';
}

// ---- Fetch data ----
$search      = trim($_GET['q'] ?? '');
$dept_filter = trim($_GET['dept'] ?? '');

$email_sql = "SELECT * FROM email_recipients WHERE 1=1";
if ($search)      $email_sql .= " AND (name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR email LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
if ($dept_filter) $email_sql .= " AND department='".mysqli_real_escape_string($conn,$dept_filter)."'";
$email_sql .= " ORDER BY department, name";
$emails = $conn->query($email_sql)->fetch_all(MYSQLI_ASSOC);

$rooms = $conn->query("SELECT * FROM meeting_room_emails ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$depts = $conn->query("SELECT DISTINCT department FROM email_recipients ORDER BY department")->fetch_all(MYSQLI_ASSOC);

$admin_username = $_SESSION['admin_user'] ?? 'admin';
$login_time_fmt = date('d/m/Y H:i', $_SESSION['admin_login_time'] ?? time());
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — VMS</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --bg:#0b1120; --surface:rgba(255,255,255,.04); --surface2:rgba(255,255,255,.07);
    --border:rgba(255,255,255,.08); --primary:#38bdf8; --accent:#a78bfa;
    --green:#34d399; --amber:#fbbf24; --red:#f87171;
    --text:#f1f5f9; --muted:#64748b;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Prompt',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
.layout{display:flex;min-height:100vh;}

/* Sidebar */
.sidebar{width:240px;background:rgba(15,23,42,.9);border-right:1px solid var(--border);padding:20px 0;position:sticky;top:0;height:100vh;display:flex;flex-direction:column;}
.sidebar-logo{padding:0 20px 24px;border-bottom:1px solid var(--border);}
.sidebar-logo h2{font-size:1.1rem;font-weight:700;color:var(--primary);letter-spacing:2px;}
.sidebar-logo p{font-size:.75rem;color:var(--muted);margin-top:2px;}
.nav-section{padding:16px 12px 8px;font-size:.7rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:.9rem;color:var(--muted);text-decoration:none;transition:.2s;border-left:3px solid transparent;}
.nav-item:hover{color:var(--text);background:var(--surface);}
.nav-item.active{color:var(--primary);background:rgba(56,189,248,.08);border-left-color:var(--primary);}
.nav-item i{width:18px;text-align:center;}

/* User info at sidebar bottom */
.sidebar-user{margin-top:auto;padding:16px 20px;border-top:1px solid var(--border);}
.user-info{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.user-avatar{width:34px;height:34px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:#0b1120;flex-shrink:0;}
.user-name{font-size:.85rem;font-weight:600;color:var(--text);}
.user-since{font-size:.72rem;color:var(--muted);}
.btn-logout{display:flex;align-items:center;gap:8px;width:100%;padding:9px 12px;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);border-radius:10px;color:var(--red);font-family:'Prompt',sans-serif;font-size:.83rem;font-weight:600;cursor:pointer;transition:.2s;text-decoration:none;}
.btn-logout:hover{background:rgba(248,113,113,.15);border-color:rgba(248,113,113,.4);}

/* Main */
.main{flex:1;overflow-x:hidden;}
.topbar{background:rgba(15,23,42,.6);backdrop-filter:blur(10px);border-bottom:1px solid var(--border);padding:16px 32px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:10;}
.topbar h1{font-size:1.15rem;font-weight:600;}
.topbar-right{display:flex;align-items:center;gap:12px;}
.session-badge{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);color:var(--green);border-radius:20px;padding:5px 12px;font-size:.78rem;display:flex;align-items:center;gap:6px;}
.session-badge::before{content:'';width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
.content{padding:28px 32px;}

/* Tabs */
.tabs{display:flex;gap:4px;margin-bottom:24px;background:var(--surface);padding:4px;border-radius:12px;width:fit-content;}
.tab-btn{padding:9px 22px;border-radius:9px;border:none;background:transparent;color:var(--muted);font-family:'Prompt',sans-serif;font-size:.9rem;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:7px;}
.tab-btn.active{background:var(--primary);color:#0b1120;font-weight:600;}
.tab-btn:hover:not(.active){background:var(--surface2);color:var(--text);}

/* Alert */
.alert-msg{padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.9rem;display:flex;align-items:center;gap:10px;}
.alert-msg.success{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.3);color:var(--green);}
.alert-msg.error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);color:var(--red);}

/* Toolbar */
.toolbar{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center;}
.search-box{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:9px 14px;color:var(--text);font-family:'Prompt',sans-serif;font-size:.9rem;width:280px;}
.search-box::placeholder{color:var(--muted);}
.search-box:focus{outline:none;border-color:var(--primary);}
.filter-select{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:9px 14px;color:var(--text);font-family:'Prompt',sans-serif;font-size:.9rem;cursor:pointer;}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:10px;border:none;font-family:'Prompt',sans-serif;font-size:.88rem;font-weight:600;cursor:pointer;transition:.2s;}
.btn-primary{background:var(--primary);color:#0b1120;}
.btn-primary:hover{opacity:.9;transform:translateY(-1px);}
.btn-sm{padding:5px 12px;font-size:.8rem;}
.btn-edit{background:rgba(167,139,250,.15);color:var(--accent);border:1px solid rgba(167,139,250,.3);}
.btn-edit:hover{background:rgba(167,139,250,.25);}
.btn-delete{background:rgba(248,113,113,.1);color:var(--red);border:1px solid rgba(248,113,113,.25);}
.btn-delete:hover{background:rgba(248,113,113,.2);}
.btn-toggle{background:rgba(56,189,248,.1);color:var(--primary);border:1px solid rgba(56,189,248,.25);padding:5px 10px;font-size:.78rem;}

/* Table */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;}
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:.875rem;}
thead th{padding:12px 16px;text-align:left;font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;border-bottom:1px solid var(--border);white-space:nowrap;}
tbody tr{border-bottom:1px solid rgba(255,255,255,.04);transition:.15s;}
tbody tr:hover{background:var(--surface2);}
tbody td{padding:12px 16px;vertical-align:middle;}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:600;}
.badge-active{background:rgba(52,211,153,.12);color:var(--green);border:1px solid rgba(52,211,153,.25);}
.badge-inactive{background:rgba(100,116,139,.1);color:var(--muted);border:1px solid rgba(100,116,139,.2);}
.dept-chip{display:inline-block;background:rgba(167,139,250,.1);color:var(--accent);border-radius:6px;padding:2px 8px;font-size:.78rem;}

/* Stats mini */
.stats-mini{display:flex;gap:12px;margin-bottom:20px;}
.stat-mini{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 18px;flex:1;}
.stat-mini .num{font-size:1.5rem;font-weight:700;color:var(--primary);}
.stat-mini .lbl{font-size:.78rem;color:var(--muted);margin-top:2px;}

/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;}
.modal-overlay.show{display:flex;}
.modal{background:#0f172a;border:1px solid var(--border);border-radius:16px;padding:30px;width:100%;max-width:480px;animation:modalIn .25s ease;}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal h3{font-size:1.05rem;font-weight:600;margin-bottom:22px;color:var(--text);}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:.82rem;color:var(--muted);margin-bottom:6px;font-weight:500;}
.form-group input,.form-group select,.form-group textarea{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:9px;padding:10px 14px;color:var(--text);font-family:'Prompt',sans-serif;font-size:.9rem;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--primary);}
.modal-actions{display:flex;gap:10px;margin-top:22px;}
.btn-cancel{background:transparent;border:1px solid var(--border);color:var(--muted);flex:1;}
.btn-cancel:hover{border-color:var(--text);color:var(--text);}
.btn-save{background:var(--primary);color:#0b1120;flex:2;}

/* Confirm modal */
.confirm-modal{max-width:360px;}
.confirm-modal p{color:var(--muted);font-size:.9rem;margin-bottom:22px;line-height:1.6;}
.btn-confirm-delete{background:var(--red);color:#fff;flex:2;}

@media(max-width:768px){.sidebar{display:none;}.content{padding:16px;}.toolbar{flex-direction:column;align-items:stretch;}.stats-mini{flex-wrap:wrap;}}
</style>
</head>
<body>
<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2>⬡ VMS</h2>
            <p>Visitor Management System</p>
        </div>
        <div class="nav-section">Main</div>
        <a href="index.php" class="nav-item"><i class="fas fa-plus-circle"></i> เพิ่มผู้มาติดต่อ</a>
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-area"></i> Dashboard</a>
        <div class="nav-section">Admin</div>
        <a href="admin.php" class="nav-item active"><i class="fas fa-cog"></i> จัดการข้อมูล</a>

        <!-- User info + Logout -->
        <div class="sidebar-user">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($admin_username, 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($admin_username) ?></div>
                    <div class="user-since">เข้าใช้ <?= $login_time_fmt ?></div>
                </div>
            </div>
            <a href="admin.php?logout=1" class="btn-logout"
               onclick="return confirm('ต้องการออกจากระบบหรือไม่?')">
                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
            </a>
        </div>
    </aside>

    <!-- Main content -->
    <div class="main">
        <div class="topbar">
            <h1>
                <i class="fas fa-shield-halved" style="color:var(--primary);margin-right:8px;"></i>
                จัดการข้อมูลระบบ
            </h1>
            <div class="topbar-right">
                <span class="session-badge">
                    <i class="fas fa-user-shield"></i>
                    <?= htmlspecialchars($admin_username) ?>
                </span>
            </div>
        </div>

        <div class="content">
            <?php if($msg): ?>
            <div class="alert-msg <?= $msg_type ?>">
                <i class="fas fa-<?= $msg_type=='success'?'check-circle':'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn <?= $tab=='emails'?'active':'' ?>" onclick="switchTab('emails', event)">
                    <i class="fas fa-envelope"></i> ผู้รับอีเมล
                    <span style="background:rgba(0,0,0,.2);padding:1px 7px;border-radius:10px;font-size:.75rem;"><?= count($emails) ?></span>
                </button>
                <button class="tab-btn <?= $tab=='rooms'?'active':'' ?>" onclick="switchTab('rooms', event)">
                    <i class="fas fa-door-open"></i> ห้องประชุม
                    <span style="background:rgba(0,0,0,.2);padding:1px 7px;border-radius:10px;font-size:.75rem;"><?= count($rooms) ?></span>
                </button>
            </div>

            <!-- ===== TAB: EMAIL RECIPIENTS ===== -->
            <div id="tab-emails" class="tab-content" <?= $tab!='emails'?'style="display:none"':'' ?>>
                <?php
                $active_count   = count(array_filter($emails, fn($e) => $e['is_active']));
                $inactive_count = count($emails) - $active_count;
                ?>
                <div class="stats-mini">
                    <div class="stat-mini"><div class="num"><?= count($emails) ?></div><div class="lbl">รายการทั้งหมด</div></div>
                    <div class="stat-mini"><div class="num" style="color:var(--green);"><?= $active_count ?></div><div class="lbl">ใช้งานอยู่</div></div>
                    <div class="stat-mini"><div class="num" style="color:var(--muted);"><?= $inactive_count ?></div><div class="lbl">ปิดใช้งาน</div></div>
                    <div class="stat-mini"><div class="num" style="color:var(--accent);"><?= count($depts) ?></div><div class="lbl">แผนก</div></div>
                </div>

                <div class="toolbar">
                    <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;">
                        <input type="hidden" name="tab" value="emails">
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="search-box" placeholder="🔍 ค้นหาชื่อหรืออีเมล...">
                        <select name="dept" class="filter-select" onchange="this.form.submit()">
                            <option value="">ทุกแผนก</option>
                            <?php foreach($depts as $d): ?>
                            <option value="<?= $d['department'] ?>" <?= $dept_filter==$d['department']?'selected':'' ?>><?= $d['department'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> ค้นหา</button>
                        <?php if($search||$dept_filter): ?>
                        <a href="admin.php?tab=emails" class="btn" style="background:var(--surface2);color:var(--muted);border:1px solid var(--border);">✕ ล้าง</a>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-primary" onclick="openAddEmail()">
                        <i class="fas fa-plus"></i> เพิ่มอีเมล
                    </button>
                </div>

                <div class="table-card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ชื่อ</th>
                                    <th>อีเมล</th>
                                    <th>แผนก</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($emails)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                                <i class="fas fa-inbox" style="display:block;font-size:1.8rem;margin-bottom:10px;"></i>ไม่พบข้อมูล
                            </td></tr>
                            <?php else: foreach($emails as $i => $e): ?>
                            <tr>
                                <td style="color:var(--muted);width:40px;"><?= $i+1 ?></td>
                                <td style="font-weight:500;"><?= htmlspecialchars($e['name']) ?></td>
                                <td style="font-family:monospace;font-size:.82rem;color:var(--primary);"><?= htmlspecialchars($e['email']) ?></td>
                                <td><span class="dept-chip"><?= htmlspecialchars($e['department']) ?></span></td>
                                <td>
                                    <span class="badge <?= $e['is_active']?'badge-active':'badge-inactive' ?>">
                                        <?= $e['is_active']?'● ใช้งาน':'○ ปิด' ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center;">
                                        <button class="btn btn-sm btn-edit" onclick='openEditEmail(<?= json_encode($e) ?>)'>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-delete" onclick='confirmDelete("email", <?= $e['id'] ?>, "<?= htmlspecialchars(addslashes($e['name'])) ?>")'>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_email">
                                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                            <input type="hidden" name="tab" value="emails">
                                            <button type="submit" class="btn btn-sm btn-toggle">
                                                <?= $e['is_active']?'ปิด':'เปิด' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== TAB: MEETING ROOMS ===== -->
            <div id="tab-rooms" class="tab-content" <?= $tab!='rooms'?'style="display:none"':'' ?>>
                <div class="toolbar">
                    <div style="flex:1;"></div>
                    <button class="btn btn-primary" onclick="openAddRoom()">
                        <i class="fas fa-plus"></i> เพิ่มห้องประชุม
                    </button>
                </div>
                <div class="table-card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ชื่อห้อง</th>
                                    <th>อีเมลห้อง</th>
                                    <th>คำอธิบาย</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($rooms)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                                <i class="fas fa-door-closed" style="display:block;font-size:1.8rem;margin-bottom:10px;"></i>ไม่มีห้องประชุม
                            </td></tr>
                            <?php else: foreach($rooms as $i => $r): ?>
                            <tr>
                                <td style="color:var(--muted);width:40px;"><?= $i+1 ?></td>
                                <td style="font-weight:600;"><?= htmlspecialchars($r['room_name']) ?></td>
                                <td style="font-family:monospace;font-size:.82rem;color:var(--primary);"><?= htmlspecialchars($r['room_email']) ?></td>
                                <td style="color:var(--muted);"><?= htmlspecialchars($r['description'] ?? '—') ?></td>
                                <td>
                                    <span class="badge <?= $r['is_active']?'badge-active':'badge-inactive' ?>">
                                        <?= $r['is_active']?'● ใช้งาน':'○ ปิด' ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center;">
                                        <button class="btn btn-sm btn-edit" onclick='openEditRoom(<?= json_encode($r) ?>)'>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-delete" onclick='confirmDelete("room", <?= $r['id'] ?>, "<?= htmlspecialchars(addslashes($r['room_name'])) ?>")'>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- .content -->
    </div><!-- .main -->
</div><!-- .layout -->

<!-- ===== MODALS ===== -->

<!-- Add Email -->
<div class="modal-overlay" id="modalAddEmail">
    <div class="modal">
        <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px;"></i>เพิ่มผู้รับอีเมล</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_email">
            <input type="hidden" name="tab" value="emails">
            <div class="form-group">
                <label>ชื่อ <span style="color:var(--red)">*</span></label>
                <input type="text" name="name" required placeholder="ชื่อ-นามสกุล หรือชื่อเล่น">
            </div>
            <div class="form-group">
                <label>อีเมล <span style="color:var(--red)">*</span></label>
                <input type="email" name="email" required placeholder="example@marugo-rubber.co.th">
            </div>
            <div class="form-group">
                <label>แผนก</label>
                <input type="text" name="department" placeholder="เช่น IT, GA, ACC">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('modalAddEmail')">ยกเลิก</button>
                <button type="submit" class="btn btn-primary btn-save"><i class="fas fa-save"></i> บันทึก</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Email -->
<div class="modal-overlay" id="modalEditEmail">
    <div class="modal">
        <h3><i class="fas fa-user-edit" style="color:var(--accent);margin-right:8px;"></i>แก้ไขผู้รับอีเมล</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_email">
            <input type="hidden" name="tab" value="emails">
            <input type="hidden" name="id" id="edit_email_id">
            <div class="form-group">
                <label>ชื่อ <span style="color:var(--red)">*</span></label>
                <input type="text" name="name" id="edit_email_name" required>
            </div>
            <div class="form-group">
                <label>อีเมล <span style="color:var(--red)">*</span></label>
                <input type="email" name="email" id="edit_email_email" required>
            </div>
            <div class="form-group">
                <label>แผนก</label>
                <input type="text" name="department" id="edit_email_dept">
            </div>
            <div class="form-group">
                <label>สถานะ</label>
                <select name="is_active" id="edit_email_active">
                    <option value="1">ใช้งาน</option>
                    <option value="0">ปิดใช้งาน</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('modalEditEmail')">ยกเลิก</button>
                <button type="submit" class="btn btn-primary btn-save"><i class="fas fa-save"></i> บันทึก</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Room -->
<div class="modal-overlay" id="modalAddRoom">
    <div class="modal">
        <h3><i class="fas fa-door-open" style="color:var(--green);margin-right:8px;"></i>เพิ่มห้องประชุม</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_room">
            <input type="hidden" name="tab" value="rooms">
            <div class="form-group">
                <label>ชื่อห้อง <span style="color:var(--red)">*</span></label>
                <input type="text" name="room_name" required placeholder="เช่น Meeting Room 5">
            </div>
            <div class="form-group">
                <label>อีเมลห้อง <span style="color:var(--red)">*</span></label>
                <input type="email" name="room_email" required placeholder="MeetingRoom5@marugo-rubber.co.th">
            </div>
            <div class="form-group">
                <label>คำอธิบาย</label>
                <textarea name="description" rows="2" placeholder="รายละเอียดเพิ่มเติมของห้อง"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('modalAddRoom')">ยกเลิก</button>
                <button type="submit" class="btn btn-primary btn-save"><i class="fas fa-save"></i> บันทึก</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room -->
<div class="modal-overlay" id="modalEditRoom">
    <div class="modal">
        <h3><i class="fas fa-edit" style="color:var(--accent);margin-right:8px;"></i>แก้ไขห้องประชุม</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_room">
            <input type="hidden" name="tab" value="rooms">
            <input type="hidden" name="id" id="edit_room_id">
            <div class="form-group">
                <label>ชื่อห้อง <span style="color:var(--red)">*</span></label>
                <input type="text" name="room_name" id="edit_room_name" required>
            </div>
            <div class="form-group">
                <label>อีเมลห้อง <span style="color:var(--red)">*</span></label>
                <input type="email" name="room_email" id="edit_room_email" required>
            </div>
            <div class="form-group">
                <label>คำอธิบาย</label>
                <textarea name="description" id="edit_room_desc" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>สถานะ</label>
                <select name="is_active" id="edit_room_active">
                    <option value="1">ใช้งาน</option>
                    <option value="0">ปิดใช้งาน</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('modalEditRoom')">ยกเลิก</button>
                <button type="submit" class="btn btn-primary btn-save"><i class="fas fa-save"></i> บันทึก</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal-overlay" id="modalConfirmDelete">
    <div class="modal confirm-modal">
        <h3><i class="fas fa-triangle-exclamation" style="color:var(--red);margin-right:8px;"></i>ยืนยันการลบ</h3>
        <p id="confirmDeleteMsg">คุณต้องการลบรายการนี้หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
        <form method="POST" id="deleteForm">
            <input type="hidden" name="action" id="deleteAction">
            <input type="hidden" name="id"     id="deleteId">
            <input type="hidden" name="tab"    id="deleteTab">
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('modalConfirmDelete')">ยกเลิก</button>
                <button type="submit" class="btn btn-confirm-delete"><i class="fas fa-trash"></i> ลบ</button>
            </div>
        </form>
    </div>
</div>

<script>
// ---- Tab switching ----
function switchTab(t, e) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + t).style.display = '';
    if (e && e.currentTarget) e.currentTarget.classList.add('active');
    history.replaceState(null, '', '?tab=' + t);
}

// ---- Modal helpers ----
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('show');
    });
});

// Close modal on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
    }
});

// ---- Email CRUD ----
function openAddEmail()  { openModal('modalAddEmail'); }
function openEditEmail(d) {
    document.getElementById('edit_email_id').value     = d.id;
    document.getElementById('edit_email_name').value   = d.name;
    document.getElementById('edit_email_email').value  = d.email;
    document.getElementById('edit_email_dept').value   = d.department || '';
    document.getElementById('edit_email_active').value = d.is_active;
    openModal('modalEditEmail');
}

// ---- Room CRUD ----
function openAddRoom()  { openModal('modalAddRoom'); }
function openEditRoom(d) {
    document.getElementById('edit_room_id').value     = d.id;
    document.getElementById('edit_room_name').value   = d.room_name;
    document.getElementById('edit_room_email').value  = d.room_email;
    document.getElementById('edit_room_desc').value   = d.description || '';
    document.getElementById('edit_room_active').value = d.is_active;
    openModal('modalEditRoom');
}

// ---- Confirm delete ----
function confirmDelete(type, id, label) {
    const action = type === 'email' ? 'delete_email' : 'delete_room';
    const tab    = type === 'email' ? 'emails' : 'rooms';
    document.getElementById('deleteAction').value = action;
    document.getElementById('deleteId').value     = id;
    document.getElementById('deleteTab').value    = tab;
    document.getElementById('confirmDeleteMsg').textContent =
        `คุณต้องการลบ "${label}" หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้`;
    openModal('modalConfirmDelete');
}
</script>
</body>
</html>