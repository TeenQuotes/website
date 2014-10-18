/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `quote_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `comments_quote_id_index` (`quote_id`),
  KEY `comments_user_id_index` (`user_id`),
  CONSTRAINT `comments_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table countries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table favorite_quotes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `favorite_quotes`;

CREATE TABLE `favorite_quotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `favorite_quotes_quote_id_index` (`quote_id`),
  KEY `favorite_quotes_user_id_index` (`user_id`),
  CONSTRAINT `favorite_quotes_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorite_quotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table newsletters
# ------------------------------------------------------------

DROP TABLE IF EXISTS `newsletters`;

CREATE TABLE `newsletters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `type` enum('weekly','daily') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'weekly',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `newsletters_user_id_index` (`user_id`),
  CONSTRAINT `newsletters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


# Dump of table password_reminders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_reminders`;

CREATE TABLE `password_reminders` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_reminders_email_index` (`email`),
  KEY `password_reminders_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table profile_visitors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profile_visitors`;

CREATE TABLE `profile_visitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `visitor_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `profile_visitors_user_id_index` (`user_id`),
  KEY `profile_visitors_visitor_id_index` (`visitor_id`),
  CONSTRAINT `profile_visitors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profile_visitors_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table quotes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `quotes`;

CREATE TABLE `quotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `quotes_user_id_index` (`user_id`),
  FULLTEXT KEY `search` (`content`),
  CONSTRAINT `quotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_user_id_index` (`user_id`),
  CONSTRAINT `settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table stories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stories`;

CREATE TABLE `stories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `represent_txt` text COLLATE utf8_unicode_ci NOT NULL,
  `frequence_txt` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `stories_user_id_index` (`user_id`),
  CONSTRAINT `stories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `security_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about_me` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hide_profile` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notification_comment_quote` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `last_visit` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Seed countries table
INSERT INTO `countries` (`id`, `name`)
VALUES
	(1,'Afghanistan'),
	(2,'Albania'),
	(3,'Algeria'),
	(4,'American'),
	(5,'Andorra'),
	(6,'Angola'),
	(7,'Anguilla'),
	(8,'Antarctica'),
	(9,'Antigua and Barbuda'),
	(10,'Argentina'),
	(11,'Armenia'),
	(12,'Aruba'),
	(13,'Australia'),
	(14,'Austria'),
	(15,'Azerbaijan'),
	(16,'Bahamas'),
	(17,'Bahrain'),
	(18,'Bangladesh'),
	(19,'Barbados'),
	(20,'Belarus'),
	(21,'Belgium'),
	(22,'Belize'),
	(23,'Benin'),
	(24,'Bermuda'),
	(25,'Bhutan'),
	(26,'Bolivia'),
	(27,'Bosnia and Herzegovina'),
	(28,'Botswana'),
	(29,'Bouvet'),
	(30,'Brazil'),
	(31,'British Indian Ocean Territory'),
	(32,'Brunei Darussalam'),
	(33,'Bulgaria'),
	(34,'Burkina Faso'),
	(35,'Burundi'),
	(36,'Cambodia'),
	(37,'Cameroon'),
	(38,'Canada'),
	(39,'Cape Verde'),
	(40,'Cayman Islands'),
	(41,'Central African Republic'),
	(42,'Chad'),
	(43,'Chile'),
	(44,'China'),
	(45,'Christmas Island'),
	(46,'Cocos (Keeling) Islands'),
	(47,'Colombia'),
	(48,'Comoros'),
	(49,'Congo'),
	(50,'Congo The Democratic Republic of The Congo'),
	(51,'Cook Islands'),
	(52,'Costa Rica'),
	(53,'Cote D''ivoire'),
	(54,'Croatia'),
	(55,'Cuba'),
	(56,'Cyprus'),
	(57,'Czech Republic'),
	(58,'Denmark'),
	(59,'Djibouti'),
	(60,'Dominica'),
	(61,'Dominican Republic'),
	(62,'Ecuador'),
	(63,'Egypt'),
	(64,'El Salvador'),
	(65,'Equatorial'),
	(66,'Eritrea'),
	(67,'Estonia'),
	(68,'Ethiopia'),
	(69,'Falkland Islands (Malvinas)'),
	(70,'Faroe'),
	(71,'Fiji'),
	(72,'Finland'),
	(73,'France'),
	(74,'French Guiana'),
	(75,'French Polynesia'),
	(76,'French Southern Territories'),
	(77,'Gabon'),
	(78,'Gambia'),
	(79,'Georgia'),
	(80,'Germany'),
	(81,'Ghana'),
	(82,'Gibraltar'),
	(83,'Greece'),
	(84,'Greenland'),
	(85,'Grenada'),
	(86,'Guadeloupe'),
	(87,'Guam'),
	(88,'Guatemala'),
	(89,'Guinea'),
	(90,'Guinea -bissau'),
	(91,'Guyana'),
	(92,'Haiti'),
	(93,'Heard Island and Mcdonald Islands'),
	(94,'Honduras'),
	(95,'Hong'),
	(96,'Hungary'),
	(97,'Iceland'),
	(98,'India'),
	(99,'Indonesia'),
	(100,'Iran'),
	(101,'Iraq'),
	(102,'Ireland'),
	(103,'Israel'),
	(104,'Italy'),
	(105,'Jamaica'),
	(106,'Japan'),
	(107,'Jordan'),
	(108,'Kazakhstan'),
	(109,'Kenya'),
	(110,'Kiribati'),
	(111,'Korea'),
	(112,'Kuwait'),
	(113,'Kyrgyzstan'),
	(114,'Lao People''s Democratic Republic'),
	(115,'Latvia'),
	(116,'Lebanon'),
	(117,'Lesotho'),
	(118,'Liberia'),
	(119,'Libyan Arab Jamahiriya'),
	(120,'Liechtenstein'),
	(121,'Lithuania'),
	(122,'Luxembourg'),
	(123,'Macao'),
	(124,'Macedonia'),
	(125,'Madagascar'),
	(126,'Malawi'),
	(127,'Malaysia'),
	(128,'Maldives'),
	(129,'Mali'),
	(130,'Malta'),
	(131,'Marshall'),
	(132,'Martinique'),
	(133,'Mauritania'),
	(134,'Mauritius'),
	(135,'Mayotte'),
	(136,'Mexico'),
	(137,'Micronesia'),
	(138,'Moldova'),
	(139,'Monaco'),
	(140,'Mongolia'),
	(141,'Montserrat'),
	(142,'Morocco'),
	(143,'Mozambique'),
	(144,'Myanmar'),
	(145,'Namibia'),
	(146,'Nauru'),
	(147,'Nepal'),
	(148,'Netherlands'),
	(149,'Netherlands Antilles'),
	(150,'New Caledonia'),
	(151,'New Zealand'),
	(152,'Nicaragua'),
	(153,'Niger'),
	(154,'Nigeria'),
	(155,'Niue'),
	(156,'Norfolk'),
	(157,'Northern'),
	(158,'Norway'),
	(159,'Oman'),
	(160,'Pakistan'),
	(161,'Palau'),
	(162,'Palestinian Territory'),
	(163,'Panama'),
	(164,'Papua New Guinea'),
	(165,'Paraguay'),
	(166,'Peru'),
	(167,'Philippines'),
	(168,'Pitcairn'),
	(169,'Poland'),
	(170,'Portugal'),
	(171,'Puerto Rico'),
	(172,'Qatar'),
	(173,'Reunion'),
	(174,'Romania'),
	(175,'Russian Federation'),
	(176,'Rwanda'),
	(177,'Saint Helena Saint Helena'),
	(178,'Saint Kitts and Nevis'),
	(179,'Saint Lucia Saint Lucia'),
	(180,'Saint Pierre and Miquelon'),
	(181,'Saint Vincent and The Grenadines'),
	(182,'Samoa'),
	(183,'San Marino San Marino'),
	(184,'Sao Tome and Principe'),
	(185,'Saudi'),
	(186,'Senegal'),
	(187,'Serbia'),
	(188,'Seychelles'),
	(189,'Sierra Leone'),
	(190,'Singapore'),
	(191,'Slovakia'),
	(192,'Slovenia'),
	(193,'Solomon Islands'),
	(194,'Somalia'),
	(195,'South Africa'),
	(196,'South Georgia and The South Sandwich Islands'),
	(197,'Spain'),
	(198,'Sri'),
	(199,'Sudan'),
	(200,'Suriname'),
	(201,'Svalbard and Jan Mayen'),
	(202,'Swaziland'),
	(203,'Sweden'),
	(204,'Switzerland'),
	(205,'Syrian Arab Republic'),
	(206,'Taiwa'),
	(207,'Tajikistan'),
	(208,'Tanzania'),
	(209,'Thailand'),
	(210,'Timor-leste'),
	(211,'Togo'),
	(212,'Tokelau'),
	(213,'Tonga'),
	(214,'Trinidad and Tobago'),
	(215,'Tunisia'),
	(216,'Turkey'),
	(217,'Turkmenistan'),
	(218,'Turks and Caicos Islands'),
	(219,'Tuvalu'),
	(220,'Uganda'),
	(221,'Ukraine'),
	(222,'United Arab Emirates'),
	(223,'United Kingdom'),
	(224,'United States'),
	(225,'United States Minor Outlying Islands'),
	(226,'Uruguay'),
	(227,'Uzbekistan'),
	(228,'Vanuatu'),
	(229,'Venezuela'),
	(230,'Viet'),
	(231,'Virgin Islands, British Virgin Islands'),
	(232,'Virgin Islands, U.S. Virgin Islands'),
	(233,'Wallis and Futuna'),
	(234,'Western'),
	(235,'Yemen'),
	(236,'Zambia'),
	(237,'Zimbabwe');