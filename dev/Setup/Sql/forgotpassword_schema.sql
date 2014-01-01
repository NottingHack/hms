CREATE TABLE IF NOT EXISTS `forgotpassword` (
  `member_id` int(11) NOT NULL,
  `request_guid` char(36) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `request_guid` (`request_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;