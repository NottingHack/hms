CREATE TABLE IF NOT EXISTS `mailinglist_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mailinglist_id` varchar(10) NOT NULL,
  `email` text NOT NULL,
  `timestamp` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;