CREATE TABLE IF NOT EXISTS `mv_ideas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idea` varchar(255) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;