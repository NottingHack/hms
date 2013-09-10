<?php

$conn = $this->_getDbConnection('default', true);

$this->_logMessage('Setting up MemberVoice tables');

$this->_logMessage('Creating `mv_ideas` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_ideas` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea` varchar(255) NOT NULL,
		  `description` text,
		  `votes` int(11) NOT NULL DEFAULT '0',
		  `status_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('Creating `mv_categories` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_categories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `category` varchar(255) NOT NULL,
		  `parent` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('Creating `mv_categories_ideas` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_categories_ideas` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea_id` int(11) NOT NULL,
		  `category_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('Creating `mv_comments` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_comments` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idea_id` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `user_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('Creating `mv_statuses` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_statuses` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `status` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('Creating `mv_votes` table.');
$query = "CREATE TABLE IF NOT EXISTS `mv_votes` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `idea_id` int(11) NOT NULL,
		  `votes` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->_runQuery($conn, $query);

$this->_logMessage('MemberVoice setup complete');

?>