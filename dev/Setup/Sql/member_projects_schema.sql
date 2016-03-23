CREATE TABLE IF NOT EXISTS `member_projects` (
  `member_project_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `complete_date` date DEFAULT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`member_project_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=latin1;