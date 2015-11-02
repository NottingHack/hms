CREATE TABLE IF NOT EXISTS `rfid_tags` (
  `member_id` int(11) NOT NULL,
  `rfid_serial` varchar(50) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  `last_used` timestamp NULL DEFAULT NULL,
  `friendly_name` varchar(128) NULL DEFAULT NULL,
  PRIMARY KEY (`rfid_serial`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;