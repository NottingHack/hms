CREATE TABLE IF NOT EXISTS `grp` (
  `grp_id` int(11) NOT NULL AUTO_INCREMENT,
  `grp_description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`grp_id`),
  UNIQUE KEY `grp_desc` (`grp_description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;