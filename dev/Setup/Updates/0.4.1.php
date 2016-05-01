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

$this->__logMessage('Creating `lable_templates` table.');
$query = "CREATE TABLE IF NOT EXISTS `label_templates` (
    `template_name` varchar(200) NOT NULL,
    `template` TEXT DEFAULT NULL,
    PRIMARY KEY (`template_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Insert into `lable_templates` table.');
    $query = "INSERT INTO `label_templates` (`template_name`, `template`) VALUES
    ('member_project', 'N\r\nq792\r\nA40,5,0,4,3,3,N,"DO NOT HACK"\r\n\r\n;General info\r\nA10,90,0,4,1,1,N,"Project name:"\r\nA10,130,0,4,1,1,N,":projectName"\r\nA10,170,0,4,1,1,N,"Member Name:"\r\nA10,210,0,4,1,1,N,":memberName"\r\nA10,250,0,4,1,1,N,"Member Username:"\r\nA10,290,0,4,1,1,N,":username"\r\nA10,330,0,4,1,1,N,"Start date: :startDate"\r\n\r\n;Worked on box\r\nLO600,5,176,4\r\nLO600,45,176,2\r\nLO600,5,4,563\r\nLO776,5,4,563\r\nLO600,568,176,4\r\nA610,15,0,4,1,1,N,"Worked on"\r\nA610,55,0,3,1,1,N,":lastDate"\r\n\r\n;qrcode and project Id\r\nb10,370,Q,s6,":qrURL"\r\nA220,370,0,4,1,1,N,"Project Id:"\r\nA:idOffset,455,0,4,2,2,N,":memberProjectId"\r\n\r\nP1\r\n');";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `member_boxes` table.');
    $query = "CREATE TABLE IF NOT EXISTS `member_boxes` (
    `member_box_id` int(11) NOT NULL AUTO_INCREMENT,
    `member_id` int(11) NOT NULL,
    `brought_date` date NOT NULL,
    `removed_date` date DEFAULT NULL,
    `state` int(11) NOT NULL,
    PRIMARY KEY (`member_box_id`)
    )ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Insert into `lable_templates` table.');
    $query = "INSERT INTO `label_templates` (`template_name`, `template`) VALUES
    ('member_box', 'N\r\nq792\r\nA40,5,0,4,3,3,N,"MEMBERS BOX"\r\n\r\n;General info\r\nA10,90,0,4,1,1,N,"Member Name:"\r\nA10,130,0,4,2,2,N,":memberName"\r\nA10,230,0,4,1,1,N,"Member Username:"\r\nA10,270,0,4,2,2,N,":username"\r\n\r\n;qrcode and project Id\r\nb10,370,Q,s6,":qrURL"\r\nA220,370,0,4,1,1,N,"Box Id:"\r\nA:idOffset,455,0,4,2,2,N,":memberBoxId"\r\n\r\nP1\r\n');";
$this->__runQuery($conn, $query);

$this->__logMessage('Insert into `hms_meta` table.');
$query = "INSERT INTO `hms_meta` (`name`, `value`) VALUES
    ('member_box_cost', '-500'),
    ('member_box_individual_limit', '3'),
    ('member_box_limit', '129');";
$this->__runQuery($conn, $query);
