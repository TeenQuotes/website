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
  `country_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_name_unique` (`name`),
  UNIQUE KEY `countries_country_code_unique` (`country_code`)
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

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `quote_tag` (
  `quote_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  KEY `quote_tag_quote_id_index` (`quote_id`),
  KEY `quote_tag_tag_id_index` (`tag_id`),
  CONSTRAINT `quote_tag_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quote_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Seed countries table
INSERT INTO `countries` (`id`, `name`, `country_code`)
VALUES
	(1,'Afghanistan','AF'),
	(2,'Albania','AL'),
	(3,'Algeria','DZ'),
	(4,'American Samoa','AS'),
	(5,'Andorra','AD'),
	(6,'Angola','AO'),
	(7,'Anguilla','AI'),
	(8,'Antarctica','AQ'),
	(9,'Antigua and Barbuda','AG'),
	(10,'Argentina','AR'),
	(11,'Armenia','AM'),
	(12,'Aruba','AW'),
	(13,'Australia','AU'),
	(14,'Austria','AT'),
	(15,'Azerbaijan','AZ'),
	(16,'Bahamas','BS'),
	(17,'Bahrain','BH'),
	(18,'Bangladesh','BD'),
	(19,'Barbados','BB'),
	(20,'Belarus','BY'),
	(21,'Belgium','BE'),
	(22,'Belize','BZ'),
	(23,'Benin','BJ'),
	(24,'Bermuda','BM'),
	(25,'Bhutan','BT'),
	(26,'Bolivia','BO'),
	(27,'Bosnia and Herzegovina','BA'),
	(28,'Botswana','BW'),
	(29,'Bouvet Island','BV'),
	(30,'Brazil','BR'),
	(31,'British Indian Ocean Territory','IO'),
	(32,'Brunei Darussalam','BN'),
	(33,'Bulgaria','BG'),
	(34,'Burkina Faso','BF'),
	(35,'Burundi','BI'),
	(36,'Cambodia','KH'),
	(37,'Cameroon','CM'),
	(38,'Canada','CA'),
	(39,'Cape Verde','CV'),
	(40,'Cayman Islands','KY'),
	(41,'Central African Republic','CF'),
	(42,'Chad','TD'),
	(43,'Chile','CL'),
	(44,'China','CN'),
	(45,'Christmas Island','CX'),
	(46,'Cocos (Keeling) Islands','CC'),
	(47,'Colombia','CO'),
	(48,'Comoros','KM'),
	(49,'Congo','CG'),
	(50,'Congo The Democratic Republic of The Congo','CD'),
	(51,'Cook Islands','CK'),
	(52,'Costa Rica','CR'),
	(53,'Cote D''ivoire','CI'),
	(54,'Croatia','HR'),
	(55,'Cuba','CU'),
	(56,'Cyprus','CY'),
	(57,'Czech Republic','CZ'),
	(58,'Denmark','DK'),
	(59,'Djibouti','DJ'),
	(60,'Dominica','DM'),
	(61,'Dominican Republic','DO'),
	(62,'Ecuador','EC'),
	(63,'Egypt','EG'),
	(64,'El Salvador','SV'),
	(65,'Equatorial','GQ'),
	(66,'Eritrea','ER'),
	(67,'Estonia','EE'),
	(68,'Ethiopia','ET'),
	(69,'Falkland Islands (Malvinas)','FK'),
	(70,'Faroe','FO'),
	(71,'Fiji','FJ'),
	(72,'Finland','FI'),
	(73,'France','FR'),
	(74,'French Guiana','GF'),
	(75,'French Polynesia','PF'),
	(76,'French Southern Territories','TF'),
	(77,'Gabon','GA'),
	(78,'Gambia','GM'),
	(79,'Georgia','GE'),
	(80,'Germany','DE'),
	(81,'Ghana','GH'),
	(82,'Gibraltar','GI'),
	(83,'Greece','GR'),
	(84,'Greenland','GL'),
	(85,'Grenada','GD'),
	(86,'Guadeloupe','GP'),
	(87,'Guam','GU'),
	(88,'Guatemala','GT'),
	(89,'Guinea','GN'),
	(90,'Guinea-Bissau','GW'),
	(91,'Guyana','GY'),
	(92,'Haiti','HT'),
	(93,'Heard Island and McDonald Islands','HM'),
	(94,'Honduras','HN'),
	(95,'Hong Kong','HK'),
	(96,'Hungary','HU'),
	(97,'Iceland','IS'),
	(98,'India','IN'),
	(99,'Indonesia','ID'),
	(100,'Iran','IR'),
	(101,'Iraq','IQ'),
	(102,'Ireland','IE'),
	(103,'Israel','IL'),
	(104,'Italy','IT'),
	(105,'Jamaica','JM'),
	(106,'Japan','JP'),
	(107,'Jordan','JO'),
	(108,'Kazakhstan','KZ'),
	(109,'Kenya','KE'),
	(110,'Kiribati','KI'),
	(111,'Korea','KR'),
	(112,'Kuwait','KW'),
	(113,'Kyrgyzstan','KG'),
	(114,'Lao People''s Democratic Republic','LA'),
	(115,'Latvia','LV'),
	(116,'Lebanon','LB'),
	(117,'Lesotho','LS'),
	(118,'Liberia','LR'),
	(119,'Libyan Arab Jamahiriya','LY'),
	(120,'Liechtenstein','LI'),
	(121,'Lithuania','LT'),
	(122,'Luxembourg','LU'),
	(123,'Macao','MO'),
	(124,'Macedonia','MK'),
	(125,'Madagascar','MG'),
	(126,'Malawi','MW'),
	(127,'Malaysia','MY'),
	(128,'Maldives','MV'),
	(129,'Mali','ML'),
	(130,'Malta','MT'),
	(131,'Marshall Islands','MH'),
	(132,'Martinique','MQ'),
	(133,'Mauritania','MR'),
	(134,'Mauritius','MU'),
	(135,'Mayotte','YT'),
	(136,'Mexico','MX'),
	(137,'Micronesia','FM'),
	(138,'Moldova','MD'),
	(139,'Monaco','MC'),
	(140,'Mongolia','MN'),
	(141,'Montserrat','MS'),
	(142,'Morocco','MA'),
	(143,'Mozambique','MZ'),
	(144,'Myanmar','MM'),
	(145,'Namibia','NA'),
	(146,'Nauru','NR'),
	(147,'Nepal','NP'),
	(148,'Netherlands','NL'),
	(149,'Netherlands Antilles','BQ'),
	(150,'New Caledonia','NC'),
	(151,'New Zealand','NZ'),
	(152,'Nicaragua','NI'),
	(153,'Niger','NE'),
	(154,'Nigeria','NG'),
	(155,'Niue','NU'),
	(156,'Norfolk Island','NF'),
	(157,'Northern Mariana Islands','MP'),
	(158,'Norway','NO'),
	(159,'Oman','OM'),
	(160,'Pakistan','PK'),
	(161,'Palau','PW'),
	(162,'Palestinian Territory','PS'),
	(163,'Panama','PA'),
	(164,'Papua New Guinea','PG'),
	(165,'Paraguay','PY'),
	(166,'Peru','PE'),
	(167,'Philippines','PH'),
	(168,'Pitcairn','PN'),
	(169,'Poland','PL'),
	(170,'Portugal','PT'),
	(171,'Puerto Rico','PR'),
	(172,'Qatar','QA'),
	(173,'Reunion','RE'),
	(174,'Romania','RO'),
	(175,'Russian Federation','RU'),
	(176,'Rwanda','RW'),
	(177,'Saint Helena','SH'),
	(178,'Saint Kitts and Nevis','KN'),
	(179,'Saint Lucia','LC'),
	(180,'Saint Pierre and Miquelon','PM'),
	(181,'Saint Vincent and the Grenadines','VC'),
	(182,'Samoa','WS'),
	(183,'San Marino','SM'),
	(184,'Sao Tome and Principe','ST'),
	(185,'Saudi Arabia','SA'),
	(186,'Senegal','SN'),
	(187,'Serbia','RS'),
	(188,'Seychelles','SC'),
	(189,'Sierra Leone','SL'),
	(190,'Singapore','SG'),
	(191,'Slovakia','Sk'),
	(192,'Slovenia','SI'),
	(193,'Solomon Islands','SB'),
	(194,'Somalia','SO'),
	(195,'South Africa','ZA'),
	(196,'South Georgia and The South Sandwich Islands','GS'),
	(197,'Spain','ES'),
	(198,'Sri Lanka','LK'),
	(199,'Sudan','SD'),
	(200,'Suriname','SR'),
	(201,'Svalbard and Jan Mayen','SJ'),
	(202,'Swaziland','SZ'),
	(203,'Sweden','SE'),
	(204,'Switzerland','CH'),
	(205,'Syrian Arab Republic','SY'),
	(206,'Taiwan','TW'),
	(207,'Tajikistan','TJ'),
	(208,'Tanzania','TZ'),
	(209,'Thailand','TH'),
	(210,'Timor-leste','TL'),
	(211,'Togo','TG'),
	(212,'Tokelau','TK'),
	(213,'Tonga','TO'),
	(214,'Trinidad and Tobago','TT'),
	(215,'Tunisia','TN'),
	(216,'Turkey','TR'),
	(217,'Turkmenistan','TM'),
	(218,'Turks and Caicos Islands','TC'),
	(219,'Tuvalu','TV'),
	(220,'Uganda','UG'),
	(221,'Ukraine','UA'),
	(222,'United Arab Emirates','AE'),
	(223,'United Kingdom','GB'),
	(224,'United States','US'),
	(225,'United States Minor Outlying Islands','UM'),
	(226,'Uruguay','UY'),
	(227,'Uzbekistan','UZ'),
	(228,'Vanuatu','VU'),
	(229,'Venezuela','VE'),
	(230,'Vietnam','VN'),
	(231,'Virgin Islands, British Virgin Islands','VG'),
	(232,'Virgin Islands, U.S. Virgin Islands','VI'),
	(233,'Wallis and Futuna','WF'),
	(234,'Western Sahara','EH'),
	(235,'Yemen','YE'),
	(236,'Zambia','ZM'),
	(237,'Zimbabwe','ZW');