CREATE TABLE IF NOT EXISTS `access_log` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rfid_serial` varchar(50) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `access_result` int(11) DEFAULT NULL,
  PRIMARY KEY (`access_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9408;