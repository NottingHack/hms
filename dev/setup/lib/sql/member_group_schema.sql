CREATE TABLE IF NOT EXISTS `member_group` (
  `member_id` int(11) NOT NULL,
  `grp_id` int(11) NOT NULL,
  PRIMARY KEY (`member_id`,`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;