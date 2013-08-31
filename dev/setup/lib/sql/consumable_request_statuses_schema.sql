CREATE TABLE IF NOT EXISTS `consumable_request_statuses` (
  `request_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`request_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;