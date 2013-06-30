CREATE TABLE IF NOT EXISTS `temperature` (
  `name` varchar(100) DEFAULT NULL,
  `dallas_address` varchar(16) NOT NULL,
  `temperature` float DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dallas_address`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;