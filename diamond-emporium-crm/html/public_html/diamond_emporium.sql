-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2014 at 03:22 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `diamond_emporium`
--

-- --------------------------------------------------------

--
-- Table structure for table `de_leads`
--

CREATE TABLE IF NOT EXISTS `de_leads` (
  `lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `product` int(11) NOT NULL,
  `budget` varchar(20) NOT NULL,
  `lead_source` int(11) NOT NULL COMMENT 'Phone, web, partner referrel, purchased list, other',
  `lead_type` int(11) NOT NULL COMMENT 'appintment, Enquiry',
  `state` int(11) NOT NULL,
  `lead_owner` text NOT NULL,
  `looking_for` text NOT NULL,
  `reference_product` int(11) NOT NULL,
  `referred_by_customer` int(11) NOT NULL,
  `preferred_contact` varchar(50) NOT NULL COMMENT 'Phone, Email',
  `priority` varchar(200) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`lead_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `de_leads` (`lead_id`, `title`, `first_name`, `last_name`, `email`, `mobile`, `product`, `budget`, `lead_source`, `lead_type`, `state`, `lead_owner`, `looking_for`, `reference_product`, `referred_by_customer`, `preferred_contact`, `priority`, `created_date`, `updated_date`, `created_by`, `updated_by`) VALUES
(1, 'Test1', 'Test1', 'Test1', 'Test1@gmail.com', '1234567890', 1, '1000', 1, 1, 1, 'abcd', 'Test1', 1, 1, '1234567890', 'Phone', '2014-12-11 00:00:00', '0000-00-00 00:00:00', 2, 0),
(2, 'Test2', 'Test2', 'Test2', 'Test2@gmail.com', '9876543210', 2, '2000', 2, 2, 2, 'xyz', 'Test2', 2, 2, '987654321', 'Email', '2014-12-11 00:00:00', '0000-00-00 00:00:00', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `de_notes`
--

CREATE TABLE IF NOT EXISTS `de_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follow_up_date` datetime NOT NULL,
  `note_type` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `de_opportunities`
--

CREATE TABLE IF NOT EXISTS `de_opportunities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_mobile` int(11) NOT NULL,
  `opportunity_type` int(11) NOT NULL,
  `lead_source` int(11) NOT NULL,
  `referred_by_customer` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `looking_for` text NOT NULL,
  `preferred_contact` int(11) NOT NULL,
  `budget` varchar(100) NOT NULL,
  `progress_of_opportunity` int(11) NOT NULL,
  `urgency` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `probability` int(11) NOT NULL,
  `est_close_date` int(11) NOT NULL,
  `created_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `de_roles`
--

CREATE TABLE IF NOT EXISTS `de_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `de_roles`
--

INSERT INTO `de_roles` (`role_id`, `role_name`) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'partner'),
(4, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `de_users`
--

CREATE TABLE IF NOT EXISTS `de_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(1) NOT NULL,
  `title` varchar(10) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `mobile_number` varchar(16) NOT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `postcode` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `de_users`
--

INSERT INTO `de_users` (`user_id`, `role_id`, `title`, `first_name`, `last_name`, `email`, `password`, `mobile_number`, `address1`, `address2`, `postcode`, `state_id`, `country_id`, `partner_id`, `created_date`, `updated_date`) VALUES
(1, 1, '', 'Super', 'Admin', 'superadmin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', '', '', '', 0, 0, 0, 0, '2014-12-09 00:00:00', '2014-12-09 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

TRUNCATE TABLE  `de_customers`;
TRUNCATE TABLE  `de_leads`;
TRUNCATE TABLE  `de_opportunities`;
TRUNCATE TABLE  `de_notes`;
TRUNCATE TABLE  `de_gridview`;