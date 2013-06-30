CREATE TABLE IF NOT EXISTS `vend_log` (
  `vend_tran_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) DEFAULT NULL,
  `rfid_serial` varchar(50) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `enq_datetime` timestamp NULL DEFAULT NULL,
  `req_datetime` timestamp NULL DEFAULT NULL,
  `success_datetime` timestamp NULL DEFAULT NULL,
  `cancelled_datetime` timestamp NULL DEFAULT NULL,
  `failed_datetime` timestamp NULL DEFAULT NULL,
  `amount_scaled` int(11) DEFAULT NULL,
  `position` varchar(10) DEFAULT NULL,
  `denied_reason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`vend_tran_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=404 ;