CREATE TABLE IF NOT EXISTS `mv_ideas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idea` varchar(255) NOT NULL,
  `description` text,
  `votes` int(11) NOT NULL DEFAULT '0',
  `status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;