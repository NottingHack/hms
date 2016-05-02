CREATE TABLE IF NOT EXISTS `member_boxes` (
`member_box_id` int(11) NOT NULL AUTO_INCREMENT,
`member_id` int(11) NOT NULL,
`bought_date` date NOT NULL,
`removed_date` date DEFAULT NULL,
`state` int(11) NOT NULL,
PRIMARY KEY (`member_box_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=latin1;