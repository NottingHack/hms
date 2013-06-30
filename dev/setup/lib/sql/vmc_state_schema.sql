CREATE TABLE IF NOT EXISTS `vmc_state` (
  `vmc_ref_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`vmc_ref_id`),
  UNIQUE KEY `vmc_state_map` (`vmc_ref_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;