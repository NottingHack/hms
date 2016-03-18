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
 * @package       dev.Setup.Updates
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$conn = $this->__getDbConnection('default', true);

$this->__logMessage('Setting up MemberProjecs table');

$this->__logMessage('Creating `member_projects` table.');
$query = "CREATE TABLE IF NOT EXISTS `member_projects` (
    `member_project_id` int(11) NOT NULL AUTO_INCREMENT,
    `member_id` int(11) NOT NULL,
    `project_name` varchar(100) NOT NULL,
    `description` text NOT NULL,
    `start_date` date NOT NULL,
    `complete_date` date DEFAULT NULL,
    `state` int(11) NOT NULL,
    PRIMARY KEY (`member_project_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);
