CREATE TABLE IF NOT EXISTS `hms_meta` (
  `name` varchar(200) NOT NULL,
  `value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;