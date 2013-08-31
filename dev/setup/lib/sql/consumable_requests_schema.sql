CREATE TABLE IF NOT EXISTS `consumable_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `detail` text NOT NULL,
  `url` text,
  `request_status_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `area_id` int(11) NOT NULL,
  `repeat_purchase_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;