CREATE TABLE IF NOT EXISTS `password_reset` (
  `reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `pr_key` varchar(40) DEFAULT NULL,
  `pr_status` varchar(16) DEFAULT NULL,
  `pr_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pr_completed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;