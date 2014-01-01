CREATE TABLE IF NOT EXISTS `pins` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin` varchar(12) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiry` timestamp NULL DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;