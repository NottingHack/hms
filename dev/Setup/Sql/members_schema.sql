CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `join_date` date NOT NULL,
  `unlock_text` varchar(95) DEFAULT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `credit_limit` int(11) NOT NULL DEFAULT '0',
  `member_status` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `address_1` varchar(100) DEFAULT NULL,
  `address_2` varchar(100) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_postcode` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `member_name` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;