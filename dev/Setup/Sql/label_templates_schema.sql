CREATE TABLE IF NOT EXISTS `label_templates` (
`template_name` varchar(200) NOT NULL,
`template` TEXT DEFAULT NULL,
PRIMARY KEY (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;