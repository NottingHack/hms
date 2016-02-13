CREATE TABLE IF NOT EXISTS `banks` (
 `bank_id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(100) NOT NULL,
 `sort_code` varchar(8) DEFUALT NULL,
 `account_number` varchar(8) DEFUALT NULL,
 PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1