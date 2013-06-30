CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_code` varchar(16) NOT NULL,
  `permission_desc` varchar(200) NOT NULL,
  PRIMARY KEY (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;