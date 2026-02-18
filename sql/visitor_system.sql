-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 18, 2026 at 09:32 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `visitor_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int NOT NULL,
  `visitor_id` int NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `sent_status` enum('success','failed','pending') DEFAULT 'pending',
  `error_message` text,
  `sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients`
--

CREATE TABLE `email_recipients` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_recipients`
--

INSERT INTO `email_recipients` (`id`, `email`, `name`, `department`, `is_active`) VALUES
(1, 's_supaphorn@marugo-rubber.co.th', 'Aom', 'ACC', 1),
(2, 'p_warayupas@marugo-rubber.co.th', 'Ni', 'ACC', 1),
(3, 'j_savitree@marugo-rubber.co.th', 'Bee', 'ACC', 1),
(4, 'm_wanlapa@marugo-rubber.co.th', 'Or', 'GA', 1),
(5, 'c_duanghatai@marugo-rubber.co.th', 'Lek', 'GA', 1),
(6, 'w_krissada@marugo-rubber.co.th', 'Nook', 'IT', 1),
(7, 'p_jeerapan@marugo-rubber.co.th', 'Bird', 'IT', 1),
(8, 'p_pornpipat@marugo-rubber.co.th', 'Mai', 'EN/ME', 1),
(9, 'p_thawatchai@marugo-rubber.co.th', 'Mon', 'EN/ME', 1),
(10, 'y-ito@marugo-rubber.co.th', 'Ito', 'MD', 1),
(11, 'y-imai@marugo-rubber.co.th', 'Imai', 'FM', 1),
(12, 't-hiramatsu@marugo-rubber.co.th', 'Hiramatsu', 'GM', 1),
(13, 'k-deguchi@marugo-rubber.co.th', 'Degushi', 'GM', 1),
(14, 'h-kayano@marugo-rubber.co.th', 'Kayano', 'GM', 1),
(15, 's_sarinya@marugo-rubber.co.th', 'Aon', 'Outsourcing', 1),
(16, 'outsourcing_mrt@marugo-rubber.co.th', 'Fon', 'Outsourcing', 1),
(17, 'planning_mrt@marugo-rubber.co.th', 'Nong', 'PC', 1),
(18, 'planning2_mrt@marugo-rubber.co.th', 'Fon', 'PC', 1),
(19, 'planning3_mrt@marugo-rubber.co.th', 'Fang', 'PC', 1),
(20, 'planning4_mrt@marugo-rubber.co.th', 'Nan', 'PC', 1),
(21, 'n_banchong@marugo-rubber.co.th', 'Tao', 'PD', 1),
(22, 'shipping@marugo-rubber.co.th', 'Aom', 'PD', 1),
(23, 'l_supasit@marugo-rubber.co.th', 'Tam', 'PD', 1),
(24, 'p_supat@marugo-rubber.co.th', 'Amp', 'PD', 1),
(25, 's_suthichai@marugo-rubber.co.th', 'Ko', 'PD', 1),
(26, 'p_ronnayut@marugo-rubber.co.th', 'Tee', 'PD', 1),
(27, 's_boonpipop@marugo-rubber.co.th', 'Je', 'PD', 1),
(28, 'c_soiphet@marugo-rubber.co.th', 'Soi', 'PD', 1),
(29, 'k_sutach@marugo-rubber.co.th', 'Tach', 'PD', 1),
(30, 's_theerapong@marugo-rubber.co.th', 'Max', 'PD', 1),
(31, 'j_wittaya@marugo-rubber.co.th', 'Aon', 'PD', 1),
(32, 't_supaporn@marugo-rubber.co.th', 'Joy', 'PD', 1),
(33, 'p_thunyaporn@marugo-rubber.co.th', 'Fhonnoi', 'PD', 1),
(34, 's_pornpimon@marugo-rubber.co.th', 'Saay', 'PD', 1),
(35, 'p_amonrat@marugo-rubber.co.th', 'Jib', 'PD', 1),
(36, 'r_kanokporn@marugo-rubber.co.th', 'Nes', 'PD', 1),
(37, 'p_torsak@marugo-rubber.co.th', 'Tor', 'PD', 1),
(38, 'adhesive_mrt@marugo-rubber.co.th', 'Adhesive', 'PD', 1),
(39, 'r_pitchayot@marugo-rubber.co.th', 'Um', 'PP', 1),
(40, 'k_supaporn@marugo-rubber.co.th', 'Nik', 'PP', 1),
(41, 'a_siriporn@marugo-rubber.co.th', 'Nue', 'PU', 1),
(42, 'j_nattida@marugo-rubber.co.th', 'Jom', 'PU', 1),
(43, 'y_chairat@marugo-rubber.co.th', 'Tong', 'QA/QMR', 1),
(44, 's_isawanchat@marugo-rubber.co.th', 'Care', 'QA', 1),
(45, 'b_napassanun@marugo-rubber.co.th', 'Da', 'QA/DCC', 1),
(46, 'p_pariwat@marugo-rubber.co.th', 'Toey', 'QA', 1),
(47, 'j_warawut@marugo-rubber.co.th', 'Tiw', 'QA', 1),
(48, 'b_pattanapong@marugo-rubber.co.th', 'Art', 'QC', 1),
(49, 'r_anchalee@marugo-rubber.co.th', 'An', 'QC', 1),
(50, 'p_sunisa@marugo-rubber.co.th', 'Ann', 'QC', 1),
(51, 'k_nattaphol@marugo-rubber.co.th', 'Ko', 'QC', 1),
(52, 'p_warawoot@marugo-rubber.co.th', 'Beer', 'QC', 1),
(53, 'c_weerapat@marugo-rubber.co.th', 'Arm', 'QC', 1),
(54, 'qc_mrt@maruggo-rubber.co.th', 'QC', 'QC', 1),
(55, 'qc_inspection@maruggo-rubber.co.th', 'Inspection', 'QC', 1),
(56, 'k_laaurat@marugo-rubber.co.th', 'Som', 'SA', 1),
(57, 'r_issara@marugo-rubber.co.th', 'Fong', 'SA', 1),
(58, 'k_kanchana@marugo-rubber.co.th', 'Bie', 'SA', 1),
(59, 'n_kwandaw@marugo-rubber.co.th', 'Oouyaye', 'SA', 1),
(60, 't_chutima@marugo-rubber.co.th', 'View', 'SF/ENVI', 1),
(61, 'd_kasemsak@marugo-rubber.co.th', 'Den', 'SF/ENVI', 1),
(62, 's_atithep@marugo-rubber.co.th', 'Thep', 'TS', 1),
(63, 'p_pattra@marugo-rubber.co.th', 'Tu', 'TS', 1),
(64, 'k_suriya@marugo-rubber.co.th', 'Ya', 'WH', 1),
(65, 's_poramet@marugo-rubber.co.th', 'James', 'WH', 1);

-- --------------------------------------------------------

--
-- Table structure for table `meeting_room_emails`
--

CREATE TABLE `meeting_room_emails` (
  `id` int NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `room_email` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `meeting_room_emails`
--

INSERT INTO `meeting_room_emails` (`id`, `room_name`, `room_email`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Meeting Room 1', 'MeetingRoom@marugo-rubber.co.th', 1, 'ห้องประชุมใหญ่ ชั้น 2', '2026-02-18 02:51:47', '2026-02-18 02:51:47'),
(2, 'Meeting Room 2', 'MeetingRoom2@marugo-rubber.co.th', 1, 'ห้องประชุมใหญ่ ชั้น 2', '2026-02-18 02:51:47', '2026-02-18 02:51:47'),
(3, 'Meeting Room 3', 'MeetingRoom3@marugo-rubber.co.th', 1, 'ห้องประชุมเล็ก ชั้น 2', '2026-02-18 02:51:47', '2026-02-18 02:51:47'),
(4, 'Meeting Room 4', 'MeetingRoom4@marugo-rubber.co.th', 1, 'ห้องประชุม VIP ชั้น 2', '2026-02-18 02:51:47', '2026-02-18 02:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `visitor_name` varchar(255) NOT NULL,
  `purpose` text,
  `visit_start_datetime` datetime NOT NULL,
  `visit_end_datetime` datetime NOT NULL,
  `visit_period` varchar(50) DEFAULT NULL,
  `visitor_type` enum('VIP','Normal') DEFAULT 'Normal',
  `welcome_board` tinyint(1) DEFAULT '0',
  `factory_tour` tinyint(1) DEFAULT '0',
  `has_meeting_room` tinyint(1) DEFAULT '0',
  `meeting_date` date DEFAULT NULL,
  `meeting_start` time DEFAULT NULL,
  `meeting_end` time DEFAULT NULL,
  `selected_meeting_room` varchar(50) DEFAULT NULL,
  `email_recipients` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_id` (`visitor_id`),
  ADD KEY `idx_sent_status` (`sent_status`);

--
-- Indexes for table `email_recipients`
--
ALTER TABLE `email_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `meeting_room_emails`
--
ALTER TABLE `meeting_room_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_name` (`room_name`),
  ADD UNIQUE KEY `unique_room_email` (`room_email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `email_recipients`
--
ALTER TABLE `email_recipients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `meeting_room_emails`
--
ALTER TABLE `meeting_room_emails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
