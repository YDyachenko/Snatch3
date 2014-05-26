-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2014 at 11:46 AM
-- Server version: 5.5.37-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bank3`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `number` varchar(100) NOT NULL,
  `balance` double NOT NULL,
  `currency` enum('rub','usd') NOT NULL DEFAULT 'rub',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `number` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `number`, `balance`, `currency`) VALUES
(1, 1, '90107430600227300001', 591, 'rub'),
(2, 2, '90107430600227300002', 578, 'rub'),
(3, 3, '90107430600227300003', 590, 'rub'),
(4, 4, '90107430600227300004', 574, 'rub'),
(5, 5, '90107430600227300005', 574, 'rub'),
(6, 6, '90107430600227300006', 573, 'rub'),
(7, 7, '90107430600227300007', 614, 'rub'),
(8, 8, '90107430600227300008', 561, 'rub'),
(9, 9, '90107430600227300009', 609, 'rub'),
(10, 10, '90107430600227300010', 566, 'rub'),
(11, 11, '90107430600227300011', 581, 'rub'),
(12, 12, '90107430600227300012', 640, 'rub'),
(13, 13, '90107430600227300013', 610, 'rub'),
(14, 14, '90107430600227300014', 638, 'rub'),
(15, 15, '90107430600227300015', 604, 'rub'),
(16, 16, '90107430600227300016', 576, 'rub'),
(17, 17, '90107430600227300017', 594, 'rub'),
(18, 18, '90107430600227300018', 590, 'rub'),
(19, 19, '90107430600227300019', 276, 'rub'),
(20, 20, '90107430600227300020', 204, 'rub'),
(21, 21, '90107430600227300021', 236, 'rub'),
(22, 22, '90107430600227300022', 282, 'rub'),
(23, 23, '90107430600227300023', 285, 'rub'),
(24, 24, '90107430600227300024', 519, 'rub'),
(25, 25, '90107430600227300025', 477, 'rub'),
(26, 26, '90107430600227300026', 486, 'rub'),
(27, 27, '90107430600227300027', 549, 'rub'),
(28, 28, '90107430600227300028', 479, 'rub'),
(29, 29, '90107430600227300029', 541, 'rub'),
(30, 30, '90107430600227300030', 493, 'rub'),
(31, 31, '90107430600227300031', 0, 'rub'),
(32, 31, '80107430600227300031', 0, 'usd');

-- --------------------------------------------------------

--
-- Table structure for table `rel_users_services`
--

CREATE TABLE IF NOT EXISTS `rel_users_services` (
  `user_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rel_users_services`
--

INSERT INTO `rel_users_services` (`user_id`, `service_id`) VALUES
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 1),
(31, 3);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`) VALUES
(1, 'Mobile bank'),
(2, 'SMS-tokens'),
(3, 'Transaction authentication numbers');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(40) NOT NULL,
  `data` blob NOT NULL,
  `access_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `data`, `access_time`) VALUES
('4354q7bsamdur0vusjqdo0o2r3', 0x757365727c613a313a7b733a323a226964223b693a33313b7d, '2014-05-15 10:13:29'),
('a42p1da0chjseep9643gjde8d7', 0x636170746368617c613a313a7b733a343a22636f6465223b733a353a223638313731223b7d757365727c613a313a7b733a323a226964223b693a33323b7d, '2014-05-15 10:39:36'),
('e0fm3l23assujeqr8pk4cculk0', 0x757365727c613a313a7b733a323a226964223b693a33313b7d, '2014-05-15 10:18:43'),
('h9vv0ejhdltphknbifr97vr4r0', 0x757365727c613a313a7b733a323a226964223b693a33313b7d, '2014-05-15 10:14:01'),
('pnq692reqpmpl323iuaqfo2ok2', 0x636170746368617c613a313a7b733a343a22636f6465223b733a353a223435353031223b7d, '2014-05-16 07:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `tan`
--

CREATE TABLE IF NOT EXISTS `tan` (
  `card_id` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`card_id`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tan`
--

INSERT INTO `tan` (`card_id`, `id`, `code`, `used`) VALUES
(24, 1, '36405', 0),
(24, 2, '23204', 0),
(24, 3, '76063', 0),
(24, 4, '50970', 0),
(24, 5, '70904', 0),
(24, 6, '78978', 0),
(24, 7, '41491', 0),
(24, 8, '01522', 0),
(24, 9, '00916', 0),
(24, 10, '26614', 0),
(24, 11, '68569', 0),
(24, 12, '93891', 0),
(24, 13, '74223', 0),
(24, 14, '42594', 0),
(24, 15, '79460', 0),
(24, 16, '19770', 0),
(24, 17, '14970', 0),
(24, 18, '97476', 0),
(24, 19, '65087', 0),
(24, 20, '32082', 0),
(25, 1, '04000', 0),
(25, 2, '86818', 0),
(25, 3, '44635', 0),
(25, 4, '79242', 0),
(25, 5, '29394', 0),
(25, 6, '88689', 0),
(25, 7, '78389', 0),
(25, 8, '46527', 0),
(25, 9, '46210', 0),
(25, 10, '88903', 0),
(25, 11, '13252', 0),
(25, 12, '64132', 0),
(25, 13, '01140', 0),
(25, 14, '18670', 0),
(25, 15, '42773', 0),
(25, 16, '75166', 0),
(25, 17, '58074', 0),
(25, 18, '34858', 0),
(25, 19, '16030', 0),
(25, 20, '04972', 0),
(26, 1, '31858', 0),
(26, 2, '47426', 0),
(26, 3, '01254', 0),
(26, 4, '12362', 0),
(26, 5, '36958', 0),
(26, 6, '47117', 0),
(26, 7, '35921', 0),
(26, 8, '76818', 0),
(26, 9, '42067', 0),
(26, 10, '57983', 0),
(26, 11, '12017', 0),
(26, 12, '96408', 0),
(26, 13, '24316', 0),
(26, 14, '58330', 0),
(26, 15, '28324', 0),
(26, 16, '07206', 0),
(26, 17, '62964', 0),
(26, 18, '65016', 0),
(26, 19, '84015', 0),
(26, 20, '77400', 0),
(27, 1, '39583', 0),
(27, 2, '66512', 0),
(27, 3, '97570', 0),
(27, 4, '49993', 0),
(27, 5, '20429', 0),
(27, 6, '60190', 0),
(27, 7, '93051', 0),
(27, 8, '41793', 0),
(27, 9, '09156', 0),
(27, 10, '10619', 0),
(27, 11, '04046', 0),
(27, 12, '91600', 0),
(27, 13, '60475', 0),
(27, 14, '51730', 0),
(27, 15, '04910', 0),
(27, 16, '63034', 0),
(27, 17, '93898', 0),
(27, 18, '49919', 0),
(27, 19, '08945', 0),
(27, 20, '50624', 0),
(28, 1, '25026', 0),
(28, 2, '66232', 0),
(28, 3, '97034', 0),
(28, 4, '99512', 0),
(28, 5, '46197', 0),
(28, 6, '86226', 0),
(28, 7, '34236', 0),
(28, 8, '90313', 0),
(28, 9, '51164', 0),
(28, 10, '56417', 0),
(28, 11, '65485', 0),
(28, 12, '17149', 0),
(28, 13, '87301', 0),
(28, 14, '00142', 0),
(28, 15, '50466', 0),
(28, 16, '92233', 0),
(28, 17, '00948', 0),
(28, 18, '46650', 0),
(28, 19, '54895', 0),
(28, 20, '90514', 0),
(29, 1, '57618', 0),
(29, 2, '03786', 0),
(29, 3, '07768', 0),
(29, 4, '90898', 0),
(29, 5, '23917', 0),
(29, 6, '52892', 0),
(29, 7, '55926', 0),
(29, 8, '82961', 0),
(29, 9, '66833', 0),
(29, 10, '72352', 0),
(29, 11, '17519', 0),
(29, 12, '37116', 0),
(29, 13, '37139', 0),
(29, 14, '72178', 0),
(29, 15, '23517', 0),
(29, 16, '88013', 0),
(29, 17, '23184', 0),
(29, 18, '12123', 0),
(29, 19, '86090', 0),
(29, 20, '06214', 0),
(30, 1, '15774', 0),
(30, 2, '62107', 0),
(30, 3, '52191', 0),
(30, 4, '66524', 0),
(30, 5, '04266', 0),
(30, 6, '22412', 0),
(30, 7, '73840', 0),
(30, 8, '21242', 0),
(30, 9, '09429', 0),
(30, 10, '59511', 0),
(30, 11, '02538', 0),
(30, 12, '15066', 0),
(30, 13, '34019', 0),
(30, 14, '14039', 0),
(30, 15, '24876', 0),
(30, 16, '82544', 0),
(30, 17, '74628', 0),
(30, 18, '44350', 0),
(30, 19, '08500', 0),
(30, 20, '41545', 0),
(31, 1, '00961', 0),
(31, 2, '23543', 0),
(31, 3, '87463', 0),
(31, 4, '40619', 0),
(31, 5, '04203', 0),
(31, 6, '84429', 0),
(31, 7, '02009', 0),
(31, 8, '13267', 0),
(31, 9, '65412', 0),
(31, 10, '85246', 0),
(31, 11, '14145', 0),
(31, 12, '43095', 0),
(31, 13, '09700', 0),
(31, 14, '71307', 0),
(31, 15, '06357', 0),
(31, 16, '53287', 0),
(31, 17, '90214', 0),
(31, 18, '86885', 0),
(31, 19, '38518', 0),
(31, 20, '58089', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `sum` double NOT NULL,
  `otp_code` char(5) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions_history`
--

CREATE TABLE IF NOT EXISTS `transactions_history` (
  `id` int(10) unsigned NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `sum` double NOT NULL,
  `date` datetime NOT NULL,
  `description` text NOT NULL,
  `shown` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_templates`
--

CREATE TABLE IF NOT EXISTS `transaction_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `to` varchar(100) NOT NULL,
  `sum` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `transaction_templates`
--

INSERT INTO `transaction_templates` (`id`, `user_id`, `name`, `from`, `to`, `sum`) VALUES
(1, 1, 'Bot 1 > 2', 1, '90107430600227300002', 25),
(2, 2, 'Bot 2 > 3', 2, '90107430600227300003', 25),
(3, 3, 'Bot 3 > 4', 3, '90107430600227300004', 25),
(4, 4, 'Bot 4 > 5', 4, '90107430600227300005', 25),
(5, 5, 'Bot 5 > 6', 5, '90107430600227300006', 25),
(6, 6, 'Bot 6 > 1', 6, '90107430600227300001', 25),
(7, 7, 'Bot 7 > 8', 7, '90107430600227300008', 25),
(8, 8, 'Bot 8 > 9', 8, '90107430600227300009', 25),
(9, 9, 'Bot 9 > 10', 9, '90107430600227300010', 25),
(10, 10, 'Bot 10 > 11', 10, '90107430600227300011', 25),
(11, 11, 'Bot 11 > 12', 11, '90107430600227300012', 25),
(12, 12, 'Bot 12 > 7', 12, '90107430600227300007', 25),
(13, 13, 'Bot 13 > 14', 13, '90107430600227300014', 25),
(14, 14, 'Bot 14 > 15', 14, '90107430600227300015', 25),
(15, 15, 'Bot 15 > 16', 15, '90107430600227300016', 25),
(16, 16, 'Bot 16 > 17', 16, '90107430600227300017', 25),
(17, 17, 'Bot 17 > 18', 17, '90107430600227300018', 25),
(18, 18, 'Bot 18 > 13', 18, '90107430600227300013', 25);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `force_change_password` tinyint(1) NOT NULL,
  `otp_method` enum('tan','mtan','none') NOT NULL DEFAULT 'none',
  `card_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `force_change_password`, `otp_method`, `card_id`, `email`, `first_name`, `last_name`, `phone`) VALUES
(1, '100001', '916ca2cb530b6a15e3b2d5df5c44d79c', 0, 'none', NULL, 'user1@ibank.phd', 'Lawrence', 'Evans', ''),
(2, '100002', 'e7e4d75e9340549b466ea6fc82c2b968', 0, 'none', NULL, 'user2@ibank.phd', 'Cedric', 'Torres', ''),
(3, '100003', '919e7edba59c695463bb74bd06a842c8', 0, 'none', NULL, 'user3@ibank.phd', 'Lillie', 'Summers', ''),
(4, '100004', 'bba0145ec880b5afbad5335779dae898', 0, 'none', NULL, 'user4@ibank.phd', 'Randy', 'Carter', ''),
(5, '100005', '912334ca989cd42884428a0f349297ec', 0, 'none', NULL, 'user5@ibank.phd', 'Kathryn', 'Sanchez', ''),
(6, '100006', 'dc73ef2307a01ce416e623b6b506a264', 0, 'none', NULL, 'user6@ibank.phd', 'Cynthia', 'Rogers', ''),
(7, '100007', '8a28350b47d8966e5559f327219240b8', 0, 'none', NULL, 'user7@ibank.phd', 'Lee', 'Lucas', ''),
(8, '100008', 'eee29ade414a4329bd06dac909a13072', 0, 'none', NULL, 'user8@ibank.phd', 'Ernesto', 'Baldwin', ''),
(9, '100009', 'd8212c7cfdc45810cbaabb9611215bb1', 0, 'none', NULL, 'user9@ibank.phd', 'Alicia', 'Mclaughlin', ''),
(10, '100010', '68343d869d75180a47eb9705fe0abe77', 0, 'none', NULL, 'user10@ibank.phd', 'Marian', 'Warren', ''),
(11, '100011', '761b0f30ce41d389427259a65f8c2c7d', 0, 'none', NULL, 'user11@ibank.phd', 'Ryan', 'Phillips', ''),
(12, '100012', 'b6ef771851d55837eb6944814fee5e6e', 0, 'none', NULL, 'user12@ibank.phd', 'Louise', 'Thomas', ''),
(13, '100013', '3794adcfd0ff373cb65e5bdccac3f166', 0, 'none', NULL, 'user13@ibank.phd', 'Elaine', 'Simpson', ''),
(14, '100014', '2d963f2e22440160191f6675d6caf60a', 0, 'none', NULL, 'user14@ibank.phd', 'Gregory', 'Hernandez', ''),
(15, '100015', '6b028bdec7f5a4bd308dad4a4c7a7ff8', 0, 'none', NULL, 'user15@ibank.phd', 'Lula', 'Buchanan', ''),
(16, '100016', 'dd37c874b6ecf2bc5724c5389aed5372', 0, 'none', NULL, 'user16@ibank.phd', 'Dixie', 'Rice', ''),
(17, '100017', 'ecfbd3a3fdb6f9a40e1ebc523807c67a', 0, 'none', NULL, 'user17@ibank.phd', 'Vernon', 'Medina', ''),
(18, '100018', 'ff3046f484fc5d25717ba4ce02ce1335', 0, 'none', NULL, 'user18@ibank.phd', 'Annie', 'Osborne', ''),
(19, '100019', '5f4dcc3b5aa765d61d8327deb882cf99', 0, 'none', NULL, 'user19@ibank.phd', 'David', 'Bennett', ''),
(20, '100020', '7b24afc8bc80e548d66c4e7ff72171c5', 0, 'none', NULL, 'user20@ibank.phd', 'Glen', 'Reed', ''),
(21, '100021', '25d55ad283aa400af464c76d713c07ad', 0, 'none', NULL, 'user21@ibank.phd', 'Ruth', 'Scott', ''),
(22, '100022', '3bf1114a986ba87ed28fc1b5884fc2f8', 0, 'none', NULL, 'user22@ibank.phd', 'Irma', 'Sanders', ''),
(23, '100023', '276f8db0b86edaa7fc805516c852c889', 0, 'none', NULL, 'user23@ibank.phd', 'Ronald', 'Simmons', ''),
(24, '100024', '36f17c3939ac3e7b2fc9396fa8e953ea', 0, 'tan', 24, 'user24@ibank.phd', 'Angela', 'Washington', ''),
(25, '100025', '0d107d09f5bbe40cade3de5c71e9e9b7', 0, 'tan', 25, 'user25@ibank.phd', 'Glen', 'Reed', ''),
(26, '100026', '8621ffdbc5698829397d97767ac13db3', 0, 'tan', 26, 'user26@ibank.phd', 'Lula', 'Buchanan', ''),
(27, '100027', '110d46fcd978c24f306cd7fa23464d73', 0, 'tan', 27, 'user27@ibank.phd', 'Gerardo', 'Wright', ''),
(28, '100028', '8621ffdbc5698829397d97767ac13db3', 0, 'tan', 28, 'user28@ibank.phd', 'Sammy', 'Banks', ''),
(29, '100029', '3899dcbab79f92af727c2190bbd8abc5', 0, 'tan', 29, 'user29@ibank.phd', 'Jane', 'Ross', ''),
(30, '100030', '8621ffdbc5698829397d97767ac13db3', 0, 'tan', 30, 'user30@ibank.phd', 'Felicia', 'Estrada', ''),
(31, 'test', 'd8578edf8458ce06fbc5bb76a58c5ca4', 0, 'tan', 31, 'user31@ibank.phd', 'Test', 'Test', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
