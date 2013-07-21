CREATE TABLE IF NOT EXISTS `hms_emails` (
  `hms_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hms_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;