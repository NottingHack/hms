CREATE TABLE IF NOT EXISTS `service_status` (
  `service_name` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `status_str` varchar(1024) DEFAULT NULL,
  `query_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reply_time` timestamp NULL DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`service_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;