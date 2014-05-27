CREATE TABLE  IF NOT EXISTS `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `transaction_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` int(11) DEFAULT NULL,
  `transaction_type` varchar(6) NOT NULL,
  `transaction_status` varchar(8) NOT NULL,
  `transaction_desc` varchar(512) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1