-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2025 at 10:24 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nouf`
--

-- --------------------------------------------------------

--
-- Table structure for table `cancellation_notification`
--

CREATE TABLE `cancellation_notification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `Banner_Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `Title` varchar(255) NOT NULL,
  `Description` text,
  `Type` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Max_Participants` int(11) DEFAULT NULL,
  `Registration_Deadline` date NOT NULL,
  `Banner_Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`Title`, `Description`, `Type`, `Date`, `Location`, `Max_Participants`, `Registration_Deadline`, `Banner_Image`) VALUES
('AI Hackathon', 'AI event for building innovative solutions.', 'Hackathon', '2025-05-01', 'Riyadh', 4, '2025-04-25', 'ai_event.jpg'),
('Cybersecurity Bootcamp', 'Hands-on bootcamp focused on cybersecurity challenges.', 'Workshop', '2025-04-01', 'Jeddah', 4, '2025-03-25', 'images/cyber.jpg'),
('Nora Hackathon', 'Join us for an AI Hackathon where innovators collaborate to solve challenges with AI.', 'Hackathon', '2025-04-01', 'Riyadh', 3, '2025-03-25', 'images/AI.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `joinrequest`
--

CREATE TABLE `joinrequest` (
  `Email` varchar(255) NOT NULL,
  `Team_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `email`, `event_title`, `team_name`, `status`) VALUES
(19, 'ali123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(20, 'asma123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(21, 'asma123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(31, 'asma123@gmail.com', 'Nora Hackathon', 'TT', 'Accepted'),
(32, 'sara@gamil.com', 'Nora Hackathon', 'TT', 'Accepted'),
(33, 'ali123@gmail.com', 'Nora Hackathon', 'TT', 'Accepted'),
(34, 'ali123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(35, 'fisal123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(36, 'sara@gamil.com', 'Nora Hackathon', 'FH', 'Accepted'),
(37, 'asma123@gmail.com', 'Nora Hackathon', 'FH', 'Accepted'),
(38, 'ali123@gmail.com', 'Nora Hackathon', 'FH', 'Accepted'),
(40, 'fisal123@gmail.com', 'Nora Hackathon', 'FH', 'Accepted'),
(41, 'fisal123@gmail.com', 'Nora Hackathon', 'WE CAN', 'Rejected'),
(42, 'asma123@gmail.com', 'Nora Hackathon', 'WE CAN', 'Accepted'),
(43, 'sara@gamil.com', 'Nora Hackathon', 'WE CAN', 'Accepted'),
(45, 'ali123@gmail.com', 'Nora Hackathon', 'wee', 'Accepted'),
(46, 'asma123@gmail.com', 'Nora Hackathon', 'wee', 'Accepted'),
(47, 'fisal123@gmail.com', 'Nora Hackathon', 'wee', 'Accepted'),
(48, 'sara@gamil.com', 'Nora Hackathon', 'GOO', 'Accepted'),
(49, 'asma123@gmail.com', 'Nora Hackathon', 'GOO', 'Accepted'),
(50, 'fisal123@gmail.com', 'Nora Hackathon', 'GOO', 'Accepted'),
(51, 'ali123@gmail.com', 'Nora Hackathon', 'TRA', 'Accepted'),
(52, 'sara@gamil.com', 'Nora Hackathon', 'TRA', 'Rejected'),
(53, 'asma123@gmail.com', 'Nora Hackathon', 'TRA', 'Accepted'),
(54, 'fisal123@gmail.com', 'Nora Hackathon', 'TRA', 'Rejected'),
(55, 'fisal123@gmail.com', 'Nora Hackathon', 'WQ', 'Rejected'),
(56, 'sara@gamil.com', 'Nora Hackathon', 'SS', 'Rejected'),
(57, 'asma123@gmail.com', 'Nora Hackathon', 'EE', 'Rejected'),
(58, 'asma123@gmail.com', 'Nora Hackathon', 'U', 'Rejected'),
(59, 'asma123@gmail.com', 'Nora Hackathon', 'PIK', 'Rejected'),
(60, 'asma123@gmail.com', 'Nora Hackathon', 'KSU', 'Rejected'),
(61, 'asma123@gmail.com', 'Nora Hackathon', 'UAT', 'Rejected'),
(63, 'sara@gamil.com', 'Nora Hackathon', 'opse', 'Rejected'),
(64, 'asma123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(65, 'fisal123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(66, 'sara@gamil.com', 'Nora Hackathon', 'X', 'Rejected'),
(69, 'asma123@gmail.com', 'Nora Hackathon', 'X', 'Accepted'),
(70, 'sara@gamil.com', 'Nora Hackathon', 'X', 'Accepted'),
(72, 'asma123@gmail.com', 'AI Hackathon', 'FU', 'Accepted'),
(73, 'ali123@gmail.com', 'Cybersecurity Bootcamp', 'la', 'Accepted'),
(74, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted'),
(76, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted'),
(77, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted'),
(78, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted'),
(80, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted'),
(81, 'sara@gamil.com', 'Cybersecurity Bootcamp', 'test', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `organizer`
--

CREATE TABLE `organizer` (
  `Email` varchar(255) NOT NULL,
  `Created_Event` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organizer`
--

INSERT INTO `organizer` (`Email`, `Created_Event`) VALUES
('organizer@example.com', 'Cybersecurity Bootcamp');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `Team_Name` varchar(255) NOT NULL,
  `Idea` text,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`Team_Name`, `Idea`, `Name`, `Email`) VALUES
('FU', 'h', 'ali', 'ali123@gmail.com'),
('la', 'dd', 'asma ali', 'asma123@gmail.com'),
('test', 'ddd', 'ali', 'ali123@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `shapeparticipant`
--

CREATE TABLE `shapeparticipant` (
  `Email` varchar(255) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shapeparticipant`
--

INSERT INTO `shapeparticipant` (`Email`, `Title`, `Status`) VALUES
('ali123@gmail.com', 'Cybersecurity Bootcamp', 'Pending'),
('asma123@gmail.com', 'Cybersecurity Bootcamp', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `Team_Name` varchar(255) NOT NULL,
  `Team_Members` int(11) DEFAULT NULL,
  `Team_Idea` text,
  `Max_Members` int(11) DEFAULT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Pending',
  `Title` varchar(255) DEFAULT NULL,
  `Leader_Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`Team_Name`, `Team_Members`, `Team_Idea`, `Max_Members`, `Status`, `Title`, `Leader_Email`) VALUES
('FU', 2, 'h', 4, 'Pending', 'AI Hackathon', 'ali123@gmail.com'),
('la', 2, 'dd', 4, 'Pending', 'Cybersecurity Bootcamp', 'asma123@gmail.com'),
('test', 2, 'ddd', 4, 'Pending', 'Cybersecurity Bootcamp', 'ali123@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `teaminvitation`
--

CREATE TABLE `teaminvitation` (
  `Email` varchar(255) NOT NULL,
  `Team_Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `team_member`
--

CREATE TABLE `team_member` (
  `Member_Email` varchar(255) NOT NULL,
  `Team_Name` varchar(255) NOT NULL,
  `Status` enum('Pending','Accepted') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `team_member`
--

INSERT INTO `team_member` (`Member_Email`, `Team_Name`, `Status`) VALUES
('asma123@gmail.com', 'FU', 'Pending'),
('asma123@gmail.com', 'la', 'Pending'),
('ali123@gmail.com', 'test', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Email` varchar(255) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Experiences` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Email`, `Name`, `Password`, `Role`, `Experiences`) VALUES
('ali123@gmail.com', 'ali', '$2y$10$evGTJuu3oHNCdfjBFsXHmu5J99g.nO1HHN7bOYwzvgGdpIymWyInq', 'participant', NULL),
('asma123@gmail.com', 'asma ali', '$2y$10$m/EdbOQWec3t6i4aHrkyoO8tgUAjiuX54KW9hfuum7NWZm.ApZYvu', 'participant', NULL),
('fisal123@gmail.com', 'Fisal sead', '$2y$10$wfu8IokhdB6dR3kPMMYWSu9vjlv9ZJyNrp6Gy5/K7zPookKt1V30i', 'participant', NULL),
('noraf3697@gmail.com', 'Nora Fisal', '$2y$10$LSdkI37rEUukjSap1GdmI.rdJlOrhajlxScsIhNHNc9nz073Gwhc2', 'participant', NULL),
('organizer@example.com', 'Organizer User', 'securepass', 'Organizer', NULL),
('sara@gamil.com', 'sara Fisal', '$2y$10$xd.NYSUr0PcVB6/3H1CaO.FE1PuG4/Zserf4PAntDnH7UIacPEx2W', 'participant', 'UX'),
('saraffisal@gmail.com', 'sara fisal', '$2y$10$XrFCcejZzhp7dMxEXB1c0Oyowx7imf14gQcWb/1yIvh/fAfb95rLa', 'participant', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cancellation_notification`
--
ALTER TABLE `cancellation_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`Title`);

--
-- Indexes for table `joinrequest`
--
ALTER TABLE `joinrequest`
  ADD PRIMARY KEY (`Email`,`Team_Name`),
  ADD KEY `Team_Name` (`Team_Name`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizer`
--
ALTER TABLE `organizer`
  ADD PRIMARY KEY (`Email`,`Created_Event`),
  ADD KEY `Created_Event` (`Created_Event`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`Team_Name`,`Email`),
  ADD KEY `Email` (`Email`);

--
-- Indexes for table `shapeparticipant`
--
ALTER TABLE `shapeparticipant`
  ADD PRIMARY KEY (`Email`,`Title`),
  ADD KEY `Title` (`Title`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`Team_Name`),
  ADD KEY `Title` (`Title`),
  ADD KEY `Leader_Email` (`Leader_Email`);

--
-- Indexes for table `teaminvitation`
--
ALTER TABLE `teaminvitation`
  ADD PRIMARY KEY (`Email`,`Team_Name`),
  ADD KEY `Team_Name` (`Team_Name`);

--
-- Indexes for table `team_member`
--
ALTER TABLE `team_member`
  ADD PRIMARY KEY (`Team_Name`,`Member_Email`),
  ADD KEY `Member_Email` (`Member_Email`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cancellation_notification`
--
ALTER TABLE `cancellation_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `joinrequest`
--
ALTER TABLE `joinrequest`
  ADD CONSTRAINT `joinrequest_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE,
  ADD CONSTRAINT `joinrequest_ibfk_2` FOREIGN KEY (`Team_Name`) REFERENCES `team` (`Team_Name`) ON DELETE CASCADE;

--
-- Constraints for table `organizer`
--
ALTER TABLE `organizer`
  ADD CONSTRAINT `organizer_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE,
  ADD CONSTRAINT `organizer_ibfk_2` FOREIGN KEY (`Created_Event`) REFERENCES `event` (`Title`) ON DELETE CASCADE;

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`Team_Name`) REFERENCES `team` (`Team_Name`) ON DELETE CASCADE,
  ADD CONSTRAINT `registration_ibfk_2` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE;

--
-- Constraints for table `shapeparticipant`
--
ALTER TABLE `shapeparticipant`
  ADD CONSTRAINT `shapeparticipant_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE,
  ADD CONSTRAINT `shapeparticipant_ibfk_2` FOREIGN KEY (`Title`) REFERENCES `event` (`Title`) ON DELETE CASCADE;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`Title`) REFERENCES `event` (`Title`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_ibfk_2` FOREIGN KEY (`Leader_Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE;

--
-- Constraints for table `teaminvitation`
--
ALTER TABLE `teaminvitation`
  ADD CONSTRAINT `teaminvitation_ibfk_1` FOREIGN KEY (`Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaminvitation_ibfk_2` FOREIGN KEY (`Team_Name`) REFERENCES `team` (`Team_Name`) ON DELETE CASCADE;

--
-- Constraints for table `team_member`
--
ALTER TABLE `team_member`
  ADD CONSTRAINT `fk_team_member_team` FOREIGN KEY (`Team_Name`) REFERENCES `team` (`Team_Name`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_member_ibfk_1` FOREIGN KEY (`Member_Email`) REFERENCES `user` (`Email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
