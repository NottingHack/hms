CREATE TABLE IF NOT EXISTS `bank_transactions` (
 `bank_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
 `transaction_date` date NOT NULL,
 `description` varchar(200) NOT NULL,
 `amount` decimal(8,2) DEFAULT NULL,
 `bank_id` int(11) NOT NULL,
 `account_id` int(11) NULL,
 PRIMARY KEY (`bank_transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1