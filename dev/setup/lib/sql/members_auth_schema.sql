CREATE TABLE IF NOT EXISTS `members_auth` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `salt` varchar(16) DEFAULT NULL,
  `passwd` varchar(40) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;