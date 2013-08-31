CREATE TABLE IF NOT EXISTS `consumable_request_comments` (
  `request_comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_id` int(11) NOT NULL,
  PRIMARY KEY (`request_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;