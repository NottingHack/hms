CREATE TABLE IF NOT EXISTS `group_permissions` (
  `grp_id` int(11) NOT NULL,
  `permission_code` varchar(16) NOT NULL,
  PRIMARY KEY (`grp_id`,`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;