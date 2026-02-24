<?php
require_once 'config.php';

// ดึงปีที่มีข้อมูล
$years_result = $conn->query("SELECT DISTINCT YEAR(visit_start_datetime) as yr FROM visitors ORDER BY yr DESC");
$available_years = [];
while ($row = $years_result->fetch_assoc()) $available_years[] = $row['yr'];
if (empty($available_years)) $available_years = [date('Y')];

$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $available_years[0];

// สถิติรายเดือน
$monthly_sql = "SELECT 
    MONTH(visit_start_datetime) as month,
    COUNT(*) as total,
    SUM(CASE WHEN visitor_type='VIP' THEN 1 ELSE 0 END) as vip_count,
    SUM(CASE WHEN visitor_type='Normal' THEN 1 ELSE 0 END) as normal_count,
    SUM(CASE WHEN has_meeting_room=1 THEN 1 ELSE 0 END) as with_meeting,
    SUM(CASE WHEN welcome_board=1 THEN 1 ELSE 0 END) as welcome_count,
    SUM(CASE WHEN factory_tour=1 THEN 1 ELSE 0 END) as tour_count
    FROM visitors WHERE YEAR(visit_start_datetime) = ?
    GROUP BY MONTH(visit_start_datetime) ORDER BY month";
$stmt = $conn->prepare($monthly_sql);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$monthly_result = $stmt->get_result();
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) $monthly_data[$row['month']] = $row;

// ดึง visitors ตามเดือนที่เลือก
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$visitors_sql = "SELECT * FROM visitors WHERE YEAR(visit_start_datetime)=? AND MONTH(visit_start_datetime)=? ORDER BY visit_start_datetime DESC";
$vstmt = $conn->prepare($visitors_sql);
$vstmt->bind_param("ii", $selected_year, $selected_month);
$vstmt->execute();
$visitors_result = $vstmt->get_result();
$visitors_list = [];
while ($row = $visitors_result->fetch_assoc()) $visitors_list[] = $row;

// สถิติรวม
$total_stat = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN visitor_type='VIP' THEN 1 ELSE 0 END) as vip, SUM(CASE WHEN has_meeting_room=1 THEN 1 ELSE 0 END) as rooms FROM visitors WHERE YEAR(visit_start_datetime)=$selected_year")->fetch_assoc();

$month_names_th = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
$month_names_full = ['','มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - VMS</title>
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root {
    /* Dark Mode (ค่าเริ่มต้น) */
    --bg: #0b1120;
    --surface: rgba(255,255,255,0.04);
    --surface2: rgba(255,255,255,0.07);
    --border: rgba(255,255,255,0.08);
    --primary: #38bdf8;
    --accent: #a78bfa;
    --green: #34d399;
    --amber: #fbbf24;
    --red: #f87171;
    --text: #f1f5f9;
    --muted: #64748b;
    --sidebar-bg: rgba(15,23,42,0.9);
    --topbar-bg: rgba(15,23,42,0.6);
    --toggle-bg: #334155;
    --toggle-handle: #94a3b8;
}

/* Light Mode Theme */
[data-theme="light"] {
    --bg: #f8fafc;
    --surface: rgba(0,0,0,0.02);
    --surface2: rgba(0,0,0,0.05);
    --border: rgba(0,0,0,0.08);
    --primary: #0284c7;
    --accent: #7c3aed;
    --green: #059669;
    --amber: #d97706;
    --red: #dc2626;
    --text: #0f172a;
    --muted: #64748b;
    --sidebar-bg: rgba(255,255,255,0.9);
    --topbar-bg: rgba(255,255,255,0.8);
    --toggle-bg: #cbd5e1;
    --toggle-handle: #475569;
}

* { margin:0; padding:0; box-sizing:border-box; }
body { 
    font-family:'Prompt',sans-serif; 
    background:var(--bg); 
    color:var(--text); 
    min-height:100vh;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Layout */
.layout { display:flex; min-height:100vh; }
.sidebar { 
    width:240px; 
    background:var(--sidebar-bg); 
    border-right:1px solid var(--border); 
    padding:20px 0; 
    position:sticky; 
    top:0; 
    height:100vh; 
    backdrop-filter:blur(10px);
    transition: background-color 0.3s ease;
}
.sidebar-logo { padding:0 20px 24px; border-bottom:1px solid var(--border); }
.sidebar-logo h2 { font-size:1.1rem; font-weight:700; color:var(--primary); letter-spacing:2px; }
.sidebar-logo p { font-size:.75rem; color:var(--muted); margin-top:2px; }
.nav-section { padding:16px 12px 8px; font-size:.7rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1.5px; }
.nav-item { display:flex; align-items:center; gap:10px; padding:10px 20px; font-size:.9rem; color:var(--muted); text-decoration:none; transition:.2s; border-left:3px solid transparent; }
.nav-item:hover { color:var(--text); background:var(--surface); }
.nav-item.active { color:var(--primary); background:rgba(56,189,248,.08); border-left-color:var(--primary); }
.nav-item i { width:18px; text-align:center; }

.main { flex:1; overflow-x:hidden; }
.topbar { 
    background:var(--topbar-bg); 
    backdrop-filter:blur(10px); 
    border-bottom:1px solid var(--border); 
    padding:16px 32px; 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    position:sticky; 
    top:0; 
    z-index:10;
    transition: background-color 0.3s ease;
}
.topbar h1 { font-size:1.15rem; font-weight:600; color:var(--text); }
.year-select { background:var(--surface2); border:1px solid var(--border); color:var(--text); padding:7px 14px; border-radius:8px; font-family:inherit; font-size:.9rem; cursor:pointer; }

.content { padding:28px 32px; }

/* Stats */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:20px; position:relative; overflow:hidden; transition:.25s; }
.stat-card:hover { border-color:var(--primary); transform:translateY(-2px); }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.stat-card.blue::before { background:linear-gradient(90deg,var(--primary),#60a5fa); }
.stat-card.purple::before { background:linear-gradient(90deg,var(--accent),#c084fc); }
.stat-card.green::before { background:linear-gradient(90deg,var(--green),#6ee7b7); }
.stat-card.amber::before { background:linear-gradient(90deg,var(--amber),#fde68a); }
.stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; margin-bottom:12px; }
.stat-card.blue .stat-icon { background:rgba(56,189,248,.15); color:var(--primary); }
.stat-card.purple .stat-icon { background:rgba(167,139,250,.15); color:var(--accent); }
.stat-card.green .stat-icon { background:rgba(52,211,153,.15); color:var(--green); }
.stat-card.amber .stat-icon { background:rgba(251,191,36,.15); color:var(--amber); }
.stat-num { font-size:2rem; font-weight:700; line-height:1; }
.stat-label { font-size:.8rem; color:var(--muted); margin-top:4px; }

/* Charts row */
.charts-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:28px; }
.chart-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:22px; }
.card-title { font-size:.95rem; font-weight:600; color:var(--text); margin-bottom:18px; display:flex; align-items:center; gap:8px; }
.card-title i { color:var(--primary); }
.chart-wrap { position:relative; height:220px; }

/* Monthly grid */
.months-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:28px; }
.month-tile { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:14px; text-align:center; cursor:pointer; transition:.2s; }
.month-tile:hover { border-color:rgba(56,189,248,.4); background:rgba(56,189,248,.06); }
.month-tile.active { border-color:var(--primary); background:rgba(56,189,248,.1); }
.month-tile .m-name { font-size:.8rem; color:var(--muted); }
.month-tile .m-count { font-size:1.6rem; font-weight:700; color:var(--text); margin:4px 0; }
.month-tile .m-bar { height:3px; border-radius:2px; background:rgba(255,255,255,.1); margin-top:8px; overflow:hidden; }
.month-tile .m-bar-fill { height:100%; background:linear-gradient(90deg,var(--primary),var(--accent)); transition:.5s; }

/* Visitor table */
.table-section { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.table-header { padding:20px 24px 16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; }
.month-title { font-size:1rem; font-weight:600; }
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; font-size:.875rem; }
thead th { padding:12px 16px; text-align:left; font-size:.75rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.8px; border-bottom:1px solid var(--border); white-space:nowrap; }
tbody tr { border-bottom:1px solid var(--border); transition:.15s; }
tbody tr:hover { background:var(--surface2); }
tbody td { padding:13px 16px; color:var(--text); vertical-align:middle; }
.badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600; }
.badge-vip { background:rgba(251,191,36,.15); color:var(--amber); border:1px solid rgba(251,191,36,.3); }
.badge-normal { background:rgba(100,116,139,.15); color:var(--muted); border:1px solid rgba(100,116,139,.3); }
.chip-sm { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:.75rem; margin:2px; }
.chip-yes { background:rgba(52,211,153,.1); color:var(--green); }
.chip-no { background:rgba(100,116,139,.08); color:var(--muted); }
.no-data { text-align:center; padding:48px; color:var(--muted); }

/* Date badges (แก้ไขขนาดให้พอดี) */
.date-badge {
    display: flex; /* เปลี่ยนเป็น flex ธรรมดาจัดให้เต็ม block ภายใน <td> */
    align-items: center;
    gap: 7px;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: .78rem;
    white-space: nowrap;
    width: max-content; /* ✅ แก้ไข: ให้กว้างพอดีกับเนื้อหา */
    min-width: 130px; /* ✅ เพิ่ม min-width ป้องกันโดนบีบ */
    margin-bottom: 5px;
}
.date-badge:last-child { margin-bottom: 0; }
.date-badge i { font-size: .8rem; flex-shrink: 0; }
.date-badge-date { font-weight: 600; line-height: 1.3; }
.date-badge-time { font-size: .72rem; opacity: .8; line-height: 1.2; }
.date-start {
    background: rgba(52,211,153,.12);
    color: var(--green);
    border: 1px solid rgba(52,211,153,.25);
}
.date-end {
    background: rgba(248,113,113,.1);
    color: var(--red);
    border: 1px solid rgba(248,113,113,.25);
}

/* --- SWITCHER STYLES (Theme + Language) --- */
.control-group {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-right: 15px;
}

.theme-switch-container,
.lang-switch-container {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--surface2);
    padding: 6px 12px;
    border-radius: 30px;
    border: 1px solid var(--border);
    backdrop-filter: blur(5px);
}

.theme-icon {
    font-size: 0.85rem;
    color: var(--muted);
    transition: color 0.3s ease;
}

.theme-icon.active {
    color: var(--primary);
}

.switch-theme,
.switch-lang {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
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
    background-color: var(--toggle-bg);
    transition: .3s;
    border-radius: 34px;
    border: 1px solid var(--border);
}

.slider-theme:before,
.slider-lang:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 2px;
    bottom: 1px;
    background-color: var(--toggle-handle);
    transition: .3s;
    border-radius: 50%;
}

input:checked + .slider-theme,
input:checked + .slider-lang {
    background-color: var(--primary);
}

input:checked + .slider-theme:before,
input:checked + .slider-lang:before {
    transform: translateX(22px);
    background-color: #ffffff;
}

.lang-text {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--muted);
    transition: 0.3s;
}

.lang-text.active {
    color: var(--primary);
}

@media(max-width:900px){ 
    .stats-row{grid-template-columns:1fr 1fr;} 
    .months-grid{grid-template-columns:repeat(4,1fr);} 
    .charts-row{grid-template-columns:1fr;} 
    .sidebar{display:none;} 
}
</style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2>⬡ VMS</h2>
            <p>Visitor Management System</p>
        </div>
        <div class="nav-section" data-i18n="menu_main">Main</div>
        <a href="index.php" class="nav-item">
            <i class="fas fa-plus-circle"></i> <span data-i18n="menu_add">เพิ่มผู้มาติดต่อ</span>
        </a>
        <a href="dashboard.php" class="nav-item active">
            <i class="fas fa-chart-area"></i> <span data-i18n="menu_dashboard">Dashboard</span>
        </a>
        <a href="manual.php" class="nav-item">
            <i class="fas fa-book"></i> <span data-i18n="menu_manual">Manual</span>
        </a>
        <div class="nav-section">Admin</div>
        <a href="admin.php" class="nav-item">
            <i class="fas fa-cog"></i> <span data-i18n="menu_admin">จัดการข้อมูล</span>
        </a>
    </aside>

    <div class="main">
        <div class="topbar">
            <h1>
                <i class="fas fa-chart-area" style="color:var(--primary);margin-right:8px;"></i>
                <span data-i18n="page_title">Dashboard ภาพรวมผู้มาติดต่อ</span>
            </h1>
            
            <div style="display:flex; align-items:center;">
                <div class="control-group">
                    <div class="theme-switch-container">
                        <i class="fas fa-sun theme-icon" id="themeIconLight"></i>
                        <label class="switch-theme">
                            <input type="checkbox" id="themeToggle">
                            <span class="slider-theme"></span>
                        </label>
                        <i class="fas fa-moon theme-icon" id="themeIconDark"></i>
                    </div>

                    <div class="lang-switch-container">
                        <span class="lang-text" id="langLabel_TH">TH</span>
                        <label class="switch-lang">
                            <input type="checkbox" id="languageToggle">
                            <span class="slider-lang"></span>
                        </label>
                        <span class="lang-text" id="langLabel_EN">EN</span>
                    </div>
                </div>

                <form method="GET" style="display:flex;gap:10px;align-items:center;">
                    <input type="hidden" name="month" value="<?= $selected_month ?>">
                    <label style="color:var(--muted);font-size:.85rem;" data-i18n="year_label">ปี:</label>
                    <select name="year" class="year-select" onchange="this.form.submit()">
                        <?php foreach($available_years as $yr): ?>
                        <option value="<?= $yr ?>" <?= $yr==$selected_year?'selected':'' ?>><?= $yr?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="content">
            <div class="stats-row">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-num"><?= $total_stat['total'] ?? 0 ?></div>
                    <div class="stat-label">
                        <span data-i18n="stat_total">ผู้มาติดต่อทั้งหมด</span> 
                        (<span data-i18n="year_txt">ปี</span> <?= $selected_year+543 ?>)
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"><i class="fas fa-crown"></i></div>
                    <div class="stat-num"><?= $total_stat['vip'] ?? 0 ?></div>
                    <div class="stat-label" data-i18n="stat_vip">ผู้มาติดต่อ VIP</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-door-open"></i></div>
                    <div class="stat-num"><?= $total_stat['rooms'] ?? 0 ?></div>
                    <div class="stat-label" data-i18n="stat_room">มีการจองห้องประชุม</div>
                </div>
                <div class="stat-card amber">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-num"><?= count($monthly_data) ?></div>
                    <div class="stat-label" data-i18n="stat_active_months">เดือนที่มีผู้เข้าเยี่ยม</div>
                </div>
            </div>

            <div class="charts-row">
                <div class="chart-card">
                    <div class="card-title"><i class="fas fa-chart-bar"></i> <span data-i18n="chart_bar_title">จำนวนผู้มาติดต่อรายเดือน</span></div>
                    <div class="chart-wrap"><canvas id="barChart"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="card-title"><i class="fas fa-chart-pie"></i> <span data-i18n="chart_pie_title">สัดส่วนประเภทผู้มาติดต่อ</span></div>
                    <div class="chart-wrap"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <?php
            $max_count = max(array_merge([1], array_column($monthly_data, 'total')));
            ?>
            <div class="months-grid">
                <?php for($m=1; $m<=12; $m++): 
                    $d = $monthly_data[$m] ?? ['total'=>0,'vip_count'=>0];
                    $pct = $max_count > 0 ? ($d['total']/$max_count*100) : 0;
                    $is_active = $m == $selected_month;
                ?>
                <a href="?year=<?= $selected_year ?>&month=<?= $m ?>" style="text-decoration:none;">
                    <div class="month-tile <?= $is_active?'active':'' ?>">
                        <div class="m-name" data-i18n="month_<?= $m ?>"><?= $month_names_th[$m] ?></div>
                        <div class="m-count" style="color:<?= $d['total']>0?'var(--primary)':'var(--muted)' ?>"><?= $d['total'] ?></div>
                        <?php if($d['total']>0): ?><div style="font-size:.7rem;color:var(--accent);">VIP: <?= $d['vip_count'] ?></div><?php endif; ?>
                        <div class="m-bar"><div class="m-bar-fill" style="width:<?= $pct ?>%;"></div></div>
                    </div>
                </a>
                <?php endfor; ?>
            </div>

            <div class="table-section">
                <div class="table-header">
                    <div class="month-title">
                        <i class="fas fa-list" style="color:var(--primary);margin-right:8px;"></i>
                        <span data-i18n="list_title">รายชื่อผู้มาติดต่อ</span> — <span data-i18n="month_full_<?= $selected_month ?>"><?= $month_names_full[$selected_month] ?></span> <?= $selected_year+543 ?>
                    </div>
                    <span style="font-size:.85rem;color:var(--muted);"><?= count($visitors_list) ?> <span data-i18n="items">รายการ</span></span>
                </div>
                <?php if(empty($visitors_list)): ?>
                <div class="no-data">
                    <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:12px;display:block;"></i>
                    <span data-i18n="no_data">ไม่มีข้อมูลผู้มาติดต่อในเดือนนี้</span>
                </div>
                <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 2%;">#</th>
                                <th data-i18n="col_company" style="width: 18%;">บริษัท/หน่วยงาน</th>
                                <th data-i18n="col_name" style="width: 15%;">ชื่อผู้มาติดต่อ</th>
                                <th data-i18n="col_purpose">วัตถุประสงค์</th> 
                                <th data-i18n="col_date" style="width: 1%; white-space: nowrap;">วันที่เยี่ยม</th>
                                <th data-i18n="col_type" style="width: 5%;">ประเภท</th>
                                <th data-i18n="col_service" style="width: 15%;">บริการเพิ่มเติม</th>
                                <th data-i18n="col_room" style="width: 10%;">ห้องประชุม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($visitors_list as $i => $v): ?>
                            <tr>
                                <td style="color:var(--muted);"><?= $i+1 ?></td>
                                <td style="font-weight:500;"><?= htmlspecialchars($v['company_name']) ?></td>
                                <td><?= htmlspecialchars($v['visitor_name']) ?></td>
                                
                                <td style="max-width:400px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($v['purpose']) ?>">
                                    <?= htmlspecialchars($v['purpose']) ?>
                                </td>
                                
                                <td style="width: 1%; white-space: nowrap;">
                                    <div style="display:flex; flex-direction:column;">
                                        <div class="date-badge date-start">
                                            <i class="fas fa-calendar-plus"></i>
                                            <div>
                                                <div class="date-badge-date"><?= date('d/m/Y', strtotime($v['visit_start_datetime'])) ?></div>
                                                <div class="date-badge-time"><?= date('H:i', strtotime($v['visit_start_datetime'])) ?></div>
                                            </div>
                                        </div>
                                        <div class="date-badge date-end">
                                            <i class="fas fa-calendar-minus"></i>
                                            <div>
                                                <div class="date-badge-date"><?= date('d/m/Y', strtotime($v['visit_end_datetime'])) ?></div>
                                                <div class="date-badge-time"><?= date('H:i', strtotime($v['visit_end_datetime'])) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge <?= $v['visitor_type']=='VIP'?'badge-vip':'badge-normal' ?>"><?= $v['visitor_type']=='VIP'?'👑 ':'' ?><?= $v['visitor_type'] ?></span></td>
                                <td>
                                    <?php if($v['welcome_board']): ?><span class="chip-sm chip-yes"><span class="chip-icon">🪧</span> <span data-i18n="col_welcome">ป้ายต้อนรับ</span></span><?php endif; ?><br>
                                    <?php if($v['factory_tour']): ?><span class="chip-sm chip-yes"><span class="chip-icon">🏭</span> <span data-i18n="col_tour">เยี่ยมชมโรงงาน</span></span><?php endif; ?><br>
                                    <?php if($v['coffee_snack']): ?><span class="chip-sm chip-yes"><span class="chip-icon">☕</span> <span data-i18n="col_coffee">กาแฟ-น้ำดื่ม</span></span><?php endif; ?><br>
                                    <?php if($v['lunch']): ?><span class="chip-sm chip-yes"><span class="chip-icon">🍱</span> <span data-i18n="col_lunch">อาหารกลางวัน</span></span><?php endif; ?>
                                    <?php if(!$v['welcome_board'] && !$v['factory_tour'] && !$v['coffee_snack'] && !$v['lunch']): ?><span style="color:var(--muted);font-size:.8rem;">—</span><?php endif; ?>
                                </td>
                                <td>
                                    <?php if($v['has_meeting_room']): ?>
                                        <span class="chip-sm chip-yes" title="<?= htmlspecialchars($v['meeting_date'].' '.$v['meeting_start'].'-'.$v['meeting_end']) ?>">
                                            🏢 <?= htmlspecialchars($v['selected_meeting_room'] ?? '') ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:var(--muted);font-size:.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// --- TRANSLATION DATA ---
const translations = {
    th: {
        menu_main: "Main",
        menu_add: "เพิ่มผู้มาติดต่อ",
        menu_dashboard: "Dashboard",
        menu_admin: "จัดการข้อมูล",
        page_title: "Dashboard ภาพรวมผู้มาติดต่อ",
        year_label: "ปี:",
        year_txt: "ปี",
        stat_total: "ผู้มาติดต่อทั้งหมด",
        stat_vip: "ผู้มาติดต่อ VIP",
        stat_room: "มีการจองห้องประชุม",
        stat_active_months: "เดือนที่มีผู้เข้าเยี่ยม",
        chart_bar_title: "จำนวนผู้มาติดต่อรายเดือน",
        chart_pie_title: "สัดส่วนประเภทผู้มาติดต่อ",
        list_title: "รายชื่อผู้มาติดต่อ",
        items: "รายการ",
        no_data: "ไม่มีข้อมูลผู้มาติดต่อในเดือนนี้",
        col_company: "บริษัท/หน่วยงาน",
        col_name: "ชื่อผู้มาติดต่อ",
        col_purpose: "วัตถุประสงค์",
        col_start: "วันที่เริ่ม",
        col_end: "วันที่สิ้นสุด",
        col_date: "วันที่เยี่ยม",
        col_type: "ประเภท",
        col_service: "บริการเพิ่มเติม",
        col_room: "ห้องประชุม",
        col_welcome: "ป้ายต้อนรับ",
        col_tour: "เยี่ยมชมโรงงาน",
        col_coffee: "กาแฟ-น้ำดื่ม",
        col_lunch: "อาหารกลางวัน",
        month_1: "ม.ค.", month_2: "ก.พ.", month_3: "มี.ค.", month_4: "เม.ย.", month_5: "พ.ค.", month_6: "มิ.ย.",
        month_7: "ก.ค.", month_8: "ส.ค.", month_9: "ก.ย.", month_10: "ต.ค.", month_11: "พ.ย.", month_12: "ธ.ค.",
        month_full_1: "มกราคม", month_full_2: "กุมภาพันธ์", month_full_3: "มีนาคม", month_full_4: "เมษายน",
        month_full_5: "พฤษภาคม", month_full_6: "มิถุนายน", month_full_7: "กรกฎาคม", month_full_8: "สิงหาคม",
        month_full_9: "กันยายน", month_full_10: "ตุลาคม", month_full_11: "พฤศจิกายน", month_full_12: "ธันวาคม"
    },
    en: {
        menu_main: "Main",
        menu_add: "Add Visitor",
        menu_dashboard: "Dashboard",
        menu_admin: "Admin Panel",
        page_title: "Visitor Overview Dashboard",
        year_label: "Year:",
        year_txt: "Year",
        stat_total: "Total Visitors",
        stat_vip: "VIP Visitors",
        stat_room: "Room Bookings",
        stat_active_months: "Active Months",
        chart_bar_title: "Monthly Visitors",
        chart_pie_title: "Visitor Type Ratio",
        list_title: "Visitor List",
        items: "items",
        no_data: "No visitor data for this month",
        col_company: "Company/Dept",
        col_name: "Visitor Name",
        col_purpose: "Purpose",
        col_start: "Start Date",
        col_end: "End Date",
        col_date: "Visit Dates",
        col_type: "Type",
        col_service: "Extra Services",
        col_room: "Meeting Room",
        col_welcome: "Welcome Board",
        col_tour: "Factory Tour",
        col_coffee: "Coffee & Drinks",
        col_lunch: "Lunch",
        month_1: "Jan", month_2: "Feb", month_3: "Mar", month_4: "Apr", month_5: "May", month_6: "Jun",
        month_7: "Jul", month_8: "Aug", month_9: "Sep", month_10: "Oct", month_11: "Nov", month_12: "Dec",
        month_full_1: "January", month_full_2: "February", month_full_3: "March", month_full_4: "April",
        month_full_5: "May", month_full_6: "June", month_full_7: "July", month_full_8: "August",
        month_full_9: "September", month_full_10: "October", month_full_11: "November", month_full_12: "December"
    }
};

const monthLabelsTH = [<?php echo implode(',', array_map(fn($m) => "'$m'", $month_names_th)); ?>].slice(1);
const monthLabelsEN = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

// --- FUNCTION TO SWITCH THEME ---
function setTheme(theme) {
    if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        localStorage.setItem('vms_theme', 'light');
        document.getElementById('themeToggle').checked = true;
        document.getElementById('themeIconLight').classList.add('active');
        document.getElementById('themeIconDark').classList.remove('active');
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('vms_theme', 'dark');
        document.getElementById('themeToggle').checked = false;
        document.getElementById('themeIconLight').classList.remove('active');
        document.getElementById('themeIconDark').classList.add('active');
    }
    
    // Force repaint for charts
    if (barChartInstance) {
        barChartInstance.update();
    }
}

// --- FUNCTION TO SWITCH LANGUAGE ---
function setLanguage(lang) {
    localStorage.setItem('vms_lang', lang);

    // Update Toggle UI
    const labelTH = document.getElementById('langLabel_TH');
    const labelEN = document.getElementById('langLabel_EN');
    const toggle = document.getElementById('languageToggle');
    
    if (lang === 'en') {
        labelTH.classList.remove('active');
        labelEN.classList.add('active');
        toggle.checked = true;
    } else {
        labelTH.classList.add('active');
        labelEN.classList.remove('active');
        toggle.checked = false;
    }

    // Update Text Content
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
            el.innerText = translations[lang][key];
        }
    });

    // Update Chart Labels
    if (barChartInstance) {
        barChartInstance.data.labels = (lang === 'en') ? monthLabelsEN : monthLabelsTH;
        barChartInstance.update();
    }
}

// --- INITIALIZE ---
const currentLang = localStorage.getItem('vms_lang') || 'en';
const currentTheme = localStorage.getItem('vms_theme') || 'light';

document.addEventListener('DOMContentLoaded', () => {
    setLanguage(currentLang);
    setTheme(currentTheme);
    
    // Theme Toggle Event
    document.getElementById('themeToggle').addEventListener('change', function() {
        setTheme(this.checked ? 'light' : 'dark');
    });
    
    // Language Toggle Event
    document.getElementById('languageToggle').addEventListener('change', function() {
        setLanguage(this.checked ? 'en' : 'th');
    });
});

// --- CHARTS CONFIG ---
Chart.defaults.color = '#64748b';
Chart.defaults.font.family = 'Prompt';

const monthCounts = [<?php echo implode(',', array_map(fn($m) => $monthly_data[$m]['total'] ?? 0, range(1,12))); ?>];
const vipCounts   = [<?php echo implode(',', array_map(fn($m) => $monthly_data[$m]['vip_count'] ?? 0, range(1,12))); ?>];
const normalCounts= [<?php echo implode(',', array_map(fn($m) => $monthly_data[$m]['normal_count'] ?? 0, range(1,12))); ?>];

// Bar chart
let barChartInstance = new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: monthLabelsTH,
        datasets: [
            { 
                label: 'Normal', 
                data: normalCounts, 
                backgroundColor: 'rgba(56,189,248,.6)', 
                borderRadius: 6,
                borderColor: 'rgba(56,189,248,1)',
                borderWidth: 1
            },
            { 
                label: 'VIP',    
                data: vipCounts,    
                backgroundColor: 'rgba(251,191,36,.7)', 
                borderRadius: 6,
                borderColor: 'rgba(251,191,36,1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true, 
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                labels: { 
                    color: 'var(--muted)', 
                    font:{size:11} 
                } 
            } 
        },
        scales: {
            x: { 
                stacked: true, 
                grid:{ color:'rgba(255,255,255,.04)' }, 
                ticks:{ color:'var(--muted)' } 
            },
            y: { 
                stacked: true, 
                grid:{ color:'rgba(255,255,255,.04)' }, 
                ticks:{ color:'var(--muted)', stepSize:1 } 
            }
        }
    }
});

// Pie chart (✅ แก้ไขเอาเส้นขอบออก)
const totalVip    = vipCounts.reduce((a,b)=>a+b,0);
const totalNormal = normalCounts.reduce((a,b)=>a+b,0);
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Normal', 'VIP'],
        datasets: [{
            data: [totalNormal, totalVip],
            backgroundColor: ['rgba(56,189,248,.7)', 'rgba(251,191,36,.7)'],
            // เอา borderColor และ borderWidth ออกเพื่อให้ไม่มีเส้นขอบ
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true, 
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { 
                    color: 'var(--muted)', 
                    padding: 16, 
                    font:{size:12} 
                } 
            }
        }
    }
});
</script>
</body>
</html>