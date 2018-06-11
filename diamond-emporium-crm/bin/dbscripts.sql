ALTER TABLE `de_userdetail` ADD `title` VARCHAR(20) NOT NULL AFTER `id`, ADD `gender` VARCHAR(25) NOT NULL AFTER `title`;

-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2018 at 04:51 AM
-- Server version: 5.7.11
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `openseedcrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `de_countries`
--

CREATE TABLE `de_countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `de_countries`
--

INSERT INTO `de_countries` (`country_id`, `country_name`) VALUES
(1, 'Australia'),
(2, 'China'),
(3, 'Germany'),
(4, 'Hong Kong'),
(5, 'New Zealand'),
(6, 'Ireland'),
(7, 'Singapore'),
(8, 'France'),
(9, 'United Kingdom'),
(10, 'United States'),
(11, 'Afghanistan'),
(12, 'Akrotiri'),
(13, 'Albania'),
(14, 'Algeria'),
(15, 'American Samoa'),
(16, 'Andorra'),
(17, 'Angola'),
(18, 'Anguilla'),
(19, 'Antarctica'),
(20, 'Antigua and Barbuda'),
(21, 'Argentina'),
(22, 'Armenia'),
(23, 'Aruba'),
(24, 'Ashmore and Cartier Islands'),
(25, 'Australia'),
(26, 'Austria'),
(27, 'Azerbaijan'),
(28, 'Bahamas, The'),
(29, 'Bahrain'),
(30, 'Bangladesh '),
(31, 'Barbados'),
(32, 'Bassas da India'),
(33, 'Belarus'),
(34, 'Belgium'),
(35, 'Belize'),
(36, 'Benin'),
(37, 'Bermuda'),
(38, 'Bhutan'),
(39, 'Bolivia'),
(40, 'Bosnia and Herzegovina'),
(41, 'Botswana'),
(42, 'Bouvet Island '),
(43, 'Brazil'),
(44, 'British Indian Ocean Territory'),
(45, 'British Virgin Islands'),
(46, 'Brunei'),
(47, 'Bulgaria'),
(48, 'Burkina Faso'),
(49, 'Burma'),
(50, 'Burundi'),
(51, 'Cambodia'),
(52, 'Cameroon'),
(53, 'Canada'),
(54, 'Cape Verde'),
(55, 'Cayman Islands'),
(56, 'Central African Republic'),
(57, 'Chad'),
(58, 'Chile'),
(59, 'China'),
(60, 'Christmas Island'),
(61, 'Clipperton Island'),
(62, 'Cocos (Keeling) Islands'),
(63, 'Colombia'),
(64, 'Comoros'),
(65, 'Congo, Democratic Republic of the'),
(66, 'Congo, Republic of the'),
(67, 'Cook Islands'),
(68, 'Coral Sea Islands'),
(69, 'Costa Rica'),
(70, 'Cote d\'Ivoire'),
(71, 'Croatia'),
(72, 'Cuba'),
(73, 'Cyprus'),
(74, 'Czech Republic'),
(75, 'Denmark'),
(76, 'Dhekelia'),
(77, 'Djibouti'),
(78, 'Dominica'),
(79, 'Dominican Republic'),
(80, 'Ecuador'),
(81, 'Egypt'),
(82, 'El Salvador'),
(83, 'Equatorial Guinea'),
(84, 'Eritrea'),
(85, 'Estonia'),
(86, 'Ethiopia'),
(87, 'Europa Island'),
(88, 'Falkland Islands (Islas Malvinas)'),
(89, 'Faroe Islands'),
(90, 'Fiji'),
(91, 'Finland'),
(92, 'France'),
(93, 'French Guiana'),
(94, 'French Polynesia'),
(95, 'French Southern and Antarctic Lands'),
(96, 'Gabon'),
(97, 'Gambia, The'),
(98, 'Gaza Strip'),
(99, 'Georgia'),
(100, 'Germany'),
(101, 'Ghana'),
(102, 'Gibraltar'),
(103, 'Glorioso Islands'),
(104, 'Greece '),
(105, 'Greenland'),
(106, 'Guadeloupe'),
(107, 'Guam'),
(108, 'Guatemala'),
(109, 'Guernsey'),
(110, 'Guinea'),
(111, 'Guinea-Bissau'),
(112, 'Guyana'),
(113, 'Haiti'),
(114, 'Heard Island and McDonald Islands'),
(115, 'Holy See (Vatican City)'),
(116, 'Honduras'),
(117, 'Hong Kong'),
(118, 'Hungary'),
(119, 'Iceland'),
(120, 'India'),
(121, 'Indonesia '),
(122, 'Iran'),
(123, 'Iraq'),
(124, 'Ireland'),
(125, 'Isle of Man'),
(126, 'Israel'),
(127, 'Italy'),
(128, 'Jamaica'),
(129, 'Jan Mayen'),
(130, 'Japan'),
(131, 'Jersey'),
(132, 'Jordan'),
(133, 'Juan de Nova Island'),
(134, 'Kazakhstan'),
(135, 'Kenya'),
(136, 'Kiribati'),
(137, 'Korea, North'),
(138, 'Korea, South'),
(139, 'Kuwait'),
(140, 'Kyrgyzstan'),
(141, 'Laos'),
(142, 'Latvia'),
(143, 'Lebanon'),
(144, 'Lesotho'),
(145, 'Liberia'),
(146, 'Libya'),
(147, 'Liechtenstein'),
(148, 'Lithuania'),
(149, 'Luxembourg'),
(150, 'Macau'),
(151, 'Macedonia '),
(152, 'Madagascar'),
(153, 'Malawi'),
(154, 'Malaysia'),
(155, 'Maldives'),
(156, 'Mali'),
(157, 'Malta'),
(158, 'Marshall Islands'),
(159, 'Martinique'),
(160, 'Mauritania'),
(161, 'Mauritius'),
(162, 'Mayotte'),
(163, 'Mexico'),
(164, 'Micronesia, Federated States of'),
(165, 'Moldova'),
(166, 'Monaco'),
(167, 'Mongolia'),
(168, 'Montserrat'),
(169, 'Morocco'),
(170, 'Mozambique'),
(171, 'Namibia'),
(172, 'Nauru'),
(173, 'Navassa Island'),
(174, 'Nepal'),
(175, 'Netherlands'),
(176, 'Netherlands Antilles'),
(177, 'New Caledonia'),
(178, 'New Zealand'),
(179, 'Nicaragua'),
(180, 'Niger'),
(181, 'Nigeria'),
(182, 'Niue'),
(183, 'Norfolk Island'),
(184, 'Northern Mariana Islands'),
(185, 'Norway'),
(186, 'Oman'),
(187, 'Pakistan'),
(188, 'Palau'),
(189, 'Panama'),
(190, 'Papua New Guinea'),
(191, 'Paracel Islands'),
(192, 'Paraguay'),
(193, 'Peru'),
(194, 'Philippines'),
(195, 'Pitcairn Islands'),
(196, 'Poland'),
(197, 'Portugal'),
(198, 'Puerto Rico'),
(199, 'Qatar'),
(200, 'Reunion'),
(201, 'Romania'),
(202, 'Russia'),
(203, 'Rwanda'),
(204, 'Saint Helena'),
(205, 'Saint Kitts and Nevis'),
(206, 'Saint Lucia'),
(207, 'Saint Pierre and Miquelon'),
(208, 'Saint Vincent and the Grenadines'),
(209, 'Samoa'),
(210, 'San Marino'),
(211, 'Sao Tome and Principe'),
(212, 'Saudi Arabia'),
(213, 'Senegal'),
(214, 'Serbia and Montenegro'),
(215, 'Seychelles'),
(216, 'Sierra Leone'),
(217, 'Singapore'),
(218, 'Slovakia'),
(219, 'Slovenia'),
(220, 'Solomon Islands'),
(221, 'Somalia'),
(222, 'South Africa'),
(223, 'South Georgia and the South Sandwich Islands'),
(224, 'Spratly Islands'),
(225, 'Sri Lanka'),
(226, 'Sudan'),
(227, 'Suriname'),
(228, 'Svalbard'),
(229, 'Swaziland'),
(230, 'Sweden'),
(231, 'Switzerland'),
(232, 'Syria'),
(233, 'Taiwan'),
(234, 'Tajikistan'),
(235, 'Tanzania'),
(236, 'Thailand'),
(237, 'Timor-Leste'),
(238, 'Togo'),
(239, 'Tokelau'),
(240, 'Tonga'),
(241, 'Trinidad and Tobago'),
(242, 'Tromelin Island'),
(243, 'Tunisia'),
(244, 'Turkey'),
(245, 'Turkmenistan'),
(246, 'Turks and Caicos Islands'),
(247, 'Tuvalu'),
(248, 'Uganda'),
(249, 'Ukraine'),
(250, 'United Arab Emirates'),
(251, 'United Kingdom'),
(252, 'United States'),
(253, 'Uruguay'),
(254, 'Uzbekistan'),
(255, 'Vanuatu'),
(256, 'Venezuela'),
(257, 'Vietnam'),
(258, 'Virgin Islands'),
(259, 'Wake Island'),
(260, 'Wallis and Futuna'),
(261, 'West Bank'),
(262, 'Western Sahara'),
(263, 'Yemen'),
(264, 'Zambia'),
(265, 'Zimbabwe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `de_countries`
--
ALTER TABLE `de_countries`
  ADD PRIMARY KEY (`country_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `de_countries`
--
ALTER TABLE `de_countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE `de_userdetail` ADD `country` VARCHAR(80) NOT NULL AFTER `email`;

ALTER TABLE `de_userdetail` ADD `communication_method` VARCHAR(40) NOT NULL AFTER `State`;

delete from de_countries where country_id in (25,59,100,117,178,124,92,217,251,252);

ALTER TABLE `de_userdetail` ADD `user_booking_date` TINYINT(1) NOT NULL AFTER `booking_duration`




