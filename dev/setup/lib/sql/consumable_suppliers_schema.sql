CREATE TABLE IF NOT EXISTS `consumable_suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text,
  `address` text,
  `url` text,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
