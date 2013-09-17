CREATE TABLE IF NOT EXISTS `consumable_request_status_updates` (
  `request_status_update_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `request_status_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_status_update_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;