CREATE TABLE IF NOT EXISTS `membership_status_notifications` (
  `membership_status_notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `time_issued` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_cleared` timestamp NULL DEFAULT NULL,
  `cleared_reason` varchar(7) DEFAULT NULL COMMENT 'REVOKED, PAYMENT, MANUAL (last one in case of audit issues',
  PRIMARY KEY (`membership_status_notification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
