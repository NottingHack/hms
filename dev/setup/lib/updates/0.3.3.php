<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       dev.Setup.Lib.Updates
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$conn = $this->__getDbConnection('default', true);

$this->__logMessage('Setting up MemberVoice tables');

$this->__logMessage('Creating `mv_ideas` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_ideas` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea` varchar(255) NOT NULL,
		  `description` text,
		  `votes` int(11) NOT NULL DEFAULT '0',
		  `status_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `mv_categories` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_categories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `category` varchar(255) NOT NULL,
		  `parent` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `mv_categories_ideas` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_categories_ideas` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea_id` int(11) NOT NULL,
		  `category_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `mv_comments` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_comments` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea_id` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `user_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `mv_statuses` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_statuses` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `status` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `mv_votes` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_votes` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `idea_id` int(11) NOT NULL,
		  `votes` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('MemberVoice setup complete');