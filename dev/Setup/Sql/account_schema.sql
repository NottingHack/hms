CREATE TABLE IF NOT EXISTS `account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_ref` varchar(18) NOT NULL,
  `natwest_ref` varchar(18) NOT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `payment_ref` (`payment_ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
