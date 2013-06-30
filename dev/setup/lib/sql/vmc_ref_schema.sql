CREATE TABLE IF NOT EXISTS `vmc_ref` (
  `vmc_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `vmc_id` int(11) DEFAULT NULL,
  `loc_encoded` varchar(10) DEFAULT NULL,
  `loc_name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`vmc_ref_id`),
  UNIQUE KEY `vmc_ref_loc` (`vmc_id`,`loc_encoded`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;