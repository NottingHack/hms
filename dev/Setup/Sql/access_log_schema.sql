CREATE TABLE IF NOT EXISTS `access_log` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rfid_serial` varchar(50) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `access_result` int(11) DEFAULT NULL COMMENT 'denied/granted',
  `member_id` int(11) DEFAULT NULL,
  `door_id` int(11) DEFAULT NULL,
  `denied_reason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`access_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1
