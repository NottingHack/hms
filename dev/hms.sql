CREATE TABLE IF NOT EXISTS `access_log` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rfid_serial` varchar(50) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `access_result` int(11) DEFAULT NULL,
  PRIMARY KEY (`access_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9408 ;

CREATE TABLE IF NOT EXISTS `account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_ref` varchar(18) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `payment_ref` (`payment_ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=239 ;

CREATE TABLE IF NOT EXISTS `forgotpassword` (
  `member_id` int(11) NOT NULL,
  `request_guid` char(36) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `request_guid` (`request_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `group_permissions` (
  `grp_id` int(11) NOT NULL,
  `permission_code` varchar(16) NOT NULL,
  PRIMARY KEY (`grp_id`,`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `group_permissions` (`grp_id`, `permission_code`) VALUES
(1, 'ADD_GROUP       '),
(1, 'ADD_GRP_MEMBER  '),
(1, 'ADD_MEMBER      '),
(1, 'ADD_UPD_PRODUCT '),
(1, 'AMEND_PINS      '),
(1, 'CHG_GRP_PERM    '),
(1, 'DEL_GROUP       '),
(1, 'REC_TRAN        '),
(1, 'REC_TRAN_OWN    '),
(1, 'REM_GRP_MEMBER  '),
(1, 'SET_CREDIT_LIMIT'),
(1, 'SET_PASSWORD    '),
(1, 'UPD_VEND_CONFIG '),
(1, 'VIEW_ACCESS_MEM '),
(1, 'VIEW_BALANCES   '),
(1, 'VIEW_GROUPS     '),
(1, 'VIEW_GRP_MEMBERS'),
(1, 'VIEW_GRP_PERMIS '),
(1, 'VIEW_MEMBERS    '),
(1, 'VIEW_MEMBER_LIST'),
(1, 'VIEW_MEMBER_PINS'),
(1, 'VIEW_MEMBER_RFID'),
(1, 'VIEW_OWN_TRANS  '),
(1, 'VIEW_PRD_DETAIL '),
(1, 'VIEW_PRODUCTS   '),
(1, 'VIEW_SALES      '),
(1, 'VIEW_TRANS      '),
(1, 'VIEW_VEND_CONFIG'),
(1, 'VIEW_VEND_LOG   '),
(1, 'WEB_LOGON       '),
(2, 'REC_TRAN_OWN    '),
(2, 'VIEW_OWN_TRANS  '),
(2, 'VIEW_PRD_DETAIL '),
(2, 'VIEW_PRODUCTS   '),
(2, 'VIEW_VEND_CONFIG'),
(2, 'WEB_LOGON       '),
(3, 'ADD_UPD_PRODUCT '),
(3, 'REC_TRAN        '),
(3, 'REC_TRAN_OWN    '),
(3, 'SET_CREDIT_LIMIT'),
(3, 'UPD_VEND_CONFIG '),
(3, 'VIEW_BALANCES   '),
(3, 'VIEW_OWN_TRANS  '),
(3, 'VIEW_PRD_DETAIL '),
(3, 'VIEW_PRODUCTS   '),
(3, 'VIEW_SALES      '),
(3, 'VIEW_TRANS      '),
(3, 'VIEW_VEND_CONFIG'),
(3, 'VIEW_VEND_LOG   '),
(3, 'WEB_LOGON       '),
(4, 'ADD_MEMBER      '),
(4, 'AMEND_PINS      '),
(4, 'VIEW_ACCESS_MEM '),
(4, 'VIEW_MEMBERS    '),
(4, 'VIEW_MEMBER_LIST'),
(4, 'VIEW_MEMBER_PINS'),
(4, 'VIEW_MEMBER_RFID');

CREATE TABLE IF NOT EXISTS `grp` (
  `grp_id` int(11) NOT NULL AUTO_INCREMENT,
  `grp_description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`grp_id`),
  UNIQUE KEY `grp_desc` (`grp_description`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `grp` (`grp_id`, `grp_description`) VALUES
(2, 'Current members'),
(1, 'Full Access'),
(4, 'Gatekeeper admin'),
(5, 'Member Admin'),
(3, 'Snackspace admin');

CREATE TABLE IF NOT EXISTS `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `invoice_from` date DEFAULT NULL,
  `invoice_to` date DEFAULT NULL,
  `invoice_generated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `invoice_status` varchar(16) DEFAULT NULL,
  `invoice_amount` int(11) DEFAULT NULL,
  `email_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

CREATE TABLE IF NOT EXISTS `member_group` (
  `member_id` int(11) NOT NULL,
  `grp_id` int(11) NOT NULL,
  PRIMARY KEY (`member_id`,`grp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `member_group` (`member_id`, `grp_id`) VALUES
(1, 2),
(2, 2),
(3, 2),
(3, 5),
(4, 2),
(5, 2),
(6, 2),
(6, 1);

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_number` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `join_date` date NOT NULL,
  `handle` varchar(100) DEFAULT NULL,
  `unlock_text` varchar(95) DEFAULT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `credit_limit` int(11) NOT NULL DEFAULT '0',
  `member_status` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `address_1` varchar(100) DEFAULT NULL,
  `address_2` varchar(100) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_postcode` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `member_name` (`handle`),
  UNIQUE KEY `member_number` (`member_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=199 ;

INSERT INTO `members` (`member_id`, `member_number`, `name`, `email`, `join_date`, `handle`, `unlock_text`, `balance`, `credit_limit`, `member_status`, `username`, `account_id`, `address_1`, `address_2`, `address_city`, `address_postcode`, `contact_number`) VALUES
(1, 1011, 'Alexander Reid', 'test1@example.com', '2012-12-05', 'alexreid', 'Welcome Alex', 0, 5000, 5, 'alexreid', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 18, 'Adam Pickering', 'test2@example.com', '2011-02-24', 'apick', 'Welcome Adam!', -300, 5000, 5, 'apick', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'Jasmine Morley', 'test3@example.com', '2010-08-18', 'morley', 'Welcome Jasmine', 0, 5000, 5, 'morley', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 5, 'Oscar Brooks', 'test4@example.com', '2010-09-22', 'brooky', 'Back Again Oscar?', 0, 5000, 5, 'brooky', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 6, 'Jodie Allen', 'test5@example.com', '2010-09-22', 'jodie', 'Welcome Jodie', -1200, 5000, 5, 'jodie', NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `members_auth` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `salt` varchar(16) DEFAULT NULL,
  `passwd` varchar(40) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `members_auth` (`member_id`, `salt`, `passwd`, `last_login`) VALUES
(1, 'l5lsQNhiE67XSBXF', 'd775d3899d580f3565360c66003e6f1ca39e132a', NULL),
(2, 'I5pEUR8r9KdflGge', '14290252b2a3031f73f300e3f7449ba7915b52fb', '2012-11-24 17:11:26');

CREATE TABLE IF NOT EXISTS `password_reset` (
  `reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `pr_key` varchar(40) DEFAULT NULL,
  `pr_status` varchar(16) DEFAULT NULL,
  `pr_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pr_completed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_code` varchar(16) NOT NULL,
  `permission_desc` varchar(200) NOT NULL,
  PRIMARY KEY (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `permissions` (`permission_code`, `permission_desc`) VALUES
('ADD_GROUP       ', 'Add group'),
('ADD_GRP_MEMBER  ', 'Add member to group'),
('ADD_MEMBER      ', 'Add member'),
('ADD_UPD_PRODUCT ', 'Add / update product'),
('AMEND_PINS      ', 'Add / Cancel PINs'),
('CHG_GRP_PERM    ', 'Change/toggle state of group permissions'),
('DEL_GROUP       ', 'Delete group'),
('REC_TRAN        ', 'Record transaction (against any member)'),
('REC_TRAN_OWN    ', 'Record transaction (against self)'),
('REM_GRP_MEMBER  ', 'Remove member from group'),
('SET_CREDIT_LIMIT', 'Set member credit limit'),
('SET_PASSWORD    ', 'Set any members password'),
('UPD_VEND_CONFIG ', 'Update vending machine config'),
('VIEW_ACCESS_MEM ', 'View Access > Members'),
('VIEW_BALANCES   ', 'View member balances / credit limit'),
('VIEW_GROUPS     ', 'View list of access groups'),
('VIEW_GRP_MEMBERS', 'View group members'),
('VIEW_GRP_PERMIS ', 'View group permissions'),
('VIEW_MEMBERS    ', 'View members list (add member to group listbox - handle+id only)'),
('VIEW_MEMBER_LIST', 'View full members list'),
('VIEW_MEMBER_PINS', 'View entry PINs'),
('VIEW_MEMBER_RFID', 'View registered RFID card details'),
('VIEW_OWN_TRANS  ', 'View own transactions'),
('VIEW_PRD_DETAIL ', 'View product details'),
('VIEW_PRODUCTS   ', 'View products'),
('VIEW_SALES      ', 'View sales list of a product (inc. handle of purchaser)'),
('VIEW_TRANS      ', 'View member transactions'),
('VIEW_VEND_CONFIG', 'View vending machine setup (product in each location)'),
('VIEW_VEND_LOG   ', 'View vending machine log'),
('WEB_LOGON       ', 'Allow logon to nh-web');

CREATE TABLE IF NOT EXISTS `pins` (
  `pin_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin` varchar(12) DEFAULT NULL,
  `unlock_text` varchar(100) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiry` timestamp NULL DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=166 ;

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `barcode` varchar(25) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `shortdesc` varchar(25) DEFAULT NULL,
  `longdesc` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `product_barcode` (`barcode`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

INSERT INTO `products` (`product_id`, `price`, `barcode`, `available`, `shortdesc`, `longdesc`) VALUES
(1, 1500, NULL, 10, 'MiniPOV3', 'Third revision of the MiniPOV'),
(3, 2000, NULL, 10, 'Bat listener kit', 'Bat listener kit'),
(4, 1000, NULL, 1, 'Xino', 'Xino'),
(5, 400, NULL, 10, 'Minimus', 'Minimus AVR USB'),
(6, 1200, NULL, 10, 'TV-B-Gone', 'TV-B-Gone'),
(7, 300, NULL, 10, 'El wire', 'Electro Luminescent Wire'),
(8, 0, NULL, 10, 'Joule theif', 'Joule theif'),
(9, 500, NULL, 10, 'Stamp making kit', 'Rubber stamp making kit'),
(10, 200, NULL, 10, 'Flashing badge', 'Flashing badge kit'),
(11, 100, NULL, 10, 'Donate!', 'Donate!'),
(12, 400, NULL, 10, '12in1 screwdriver', '12 in 1 screndriver'),
(13, 200, '', 1, 'Pot luck kit', 'A random soldering kit'),
(14, 200, NULL, 10, 'Packet of Red @Sugru', 'Packet of Red Sugru'),
(15, 200, NULL, 10, 'Packet of Blue @Sugru', 'Packet of Blue Sugru'),
(16, 200, NULL, 10, 'Packet of Yellow @Sugru', 'Packet of Yellow Sugru'),
(17, 300, NULL, 10, 'Wire bending tool', 'Wire bending tool'),
(18, 1500, NULL, 1, 'Iron Man Arc', 'Iron Man Arc kit'),
(19, 300, NULL, 1, 'LASER rubber', 'A6 piece of LASER rubber'),
(20, 1500, NULL, 1, 'RGB LED', 'Phenoptix LED floodlight'),
(21, 1000, NULL, 1, 'Drawdio', 'Drawdio kit'),
(22, 1800, NULL, 1, 'Arduino', 'Arduino UNO'),
(23, 400, NULL, 1, 'Slice of Pi', 'Slice of Pi'),
(24, 200, NULL, 1, 'Plastic stuff', 'Martins plastic stuff'),
(25, 300, NULL, 1, 'Hackspace Passports', 'EU Hackspace passports'),
(26, 500, '30', 1, 'RGB LED Small', 'Small 12V RGB LED board'),
(27, 2500, NULL, 1, 'Lock Picks', '13pcs Majestic Set of Sport Lock Picks'),
(28, 1000, NULL, 1, 'PiBow', 'Pi Rainbow Case');

CREATE TABLE IF NOT EXISTS `rfid_tags` (
  `member_id` int(11) NOT NULL,
  `rfid_serial` varchar(50) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rfid_serial`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `rfid_tags` (`member_id`, `rfid_serial`, `state`) VALUES
(1, '1008759360', 10),
(2, '102800066', 10),
(3, '1034720438', 10);

CREATE TABLE IF NOT EXISTS `service_status` (
  `service_name` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `status_str` varchar(1024) DEFAULT NULL,
  `query_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reply_time` timestamp NULL DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`service_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `service_status` (`service_name`, `status`, `status_str`, `query_time`, `reply_time`, `description`) VALUES
('MySQL', 1, 'Connected', '2012-11-13 17:31:55', '2012-11-13 17:31:55', NULL),
('Mosquitto', 1, 'Connected', '2012-11-13 17:31:55', '2012-11-13 17:31:55', NULL),
('nh-irccat', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-irc', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-matrix', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('GateKeeper', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-temperature', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-irc-misc', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-vend', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL),
('nh-gk-if', 0, NULL, '2012-11-23 18:04:20', NULL, NULL),
('nh-mini-matrix', 1, 'Running', '2012-11-23 18:04:20', '2012-11-23 18:04:20', NULL);

CREATE TABLE IF NOT EXISTS `state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`state_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `state` (`state_id`, `name`, `value`) VALUES
(1, 'OpenState', 'OPEN'),
(2, 'OpenTime', '2011-07-17 09:46:20');

CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

INSERT INTO `status` (`status_id`, `title`, `description`) VALUES
(1, 'Prospective Member', 'Interested in the hackspace, we have their e-mail. May be receiving the newsletter'),
(2, 'Pre-Member (stage 1)', 'Member has HMS login details, waiting for them to enter contact details'),
(3, 'Pre-Member (stage 2)', 'Waiting for member-admin to approve contact details'),
(4, 'Pre-Member (stage 3)', 'Waiting for standing order'),
(5, 'Current Member', 'Active member'),
(6, 'Ex Member', 'Former member, details only kept for a while');

CREATE TABLE IF NOT EXISTS `status_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `old_status` int(11) NOT NULL,
  `new_status` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

CREATE TABLE IF NOT EXISTS `temperature` (
  `name` varchar(100) DEFAULT NULL,
  `dallas_address` varchar(16) NOT NULL,
  `temperature` float DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dallas_address`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `temperature` (`name`, `dallas_address`, `temperature`, `time`) VALUES
('ComfyArea', '10C3282902080021', 13.13, '2012-11-23 17:57:51'),
('BlueRoom', '1032C01602080052', 20.69, '2012-11-23 18:02:51'),
('Studio', '106E951602080046', 13.25, '2012-11-23 17:47:51'),
('Fridge', '284298C203000056', 2.38, '2012-11-23 18:03:49');

CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `transaction_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` int(11) DEFAULT NULL,
  `transaction_type` varchar(6) NOT NULL,
  `transaction_status` varchar(8) NOT NULL,
  `transaction_desc` varchar(512) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=279 ;

CREATE TABLE IF NOT EXISTS `vend_log` (
  `vend_tran_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) DEFAULT NULL,
  `rfid_serial` varchar(50) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `enq_datetime` timestamp NULL DEFAULT NULL,
  `req_datetime` timestamp NULL DEFAULT NULL,
  `success_datetime` timestamp NULL DEFAULT NULL,
  `cancelled_datetime` timestamp NULL DEFAULT NULL,
  `failed_datetime` timestamp NULL DEFAULT NULL,
  `amount_scaled` int(11) DEFAULT NULL,
  `position` varchar(10) DEFAULT NULL,
  `denied_reason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`vend_tran_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=404 ;

CREATE TABLE IF NOT EXISTS `vmc_ref` (
  `vmc_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `vmc_id` int(11) DEFAULT NULL,
  `loc_encoded` varchar(10) DEFAULT NULL,
  `loc_name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`vmc_ref_id`),
  UNIQUE KEY `vmc_ref_loc` (`vmc_id`,`loc_encoded`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

INSERT INTO `vmc_ref` (`vmc_ref_id`, `vmc_id`, `loc_encoded`, `loc_name`) VALUES
(1, 1, '41-31', 'A1'),
(2, 1, '41-33', 'A3'),
(3, 1, '41-35', 'A5'),
(4, 1, '41-37', 'A7'),
(5, 1, '42-31', 'B1'),
(6, 1, '42-33', 'B3'),
(7, 1, '42-35', 'B5'),
(8, 1, '42-37', 'B7'),
(9, 1, '43-31', 'C1'),
(10, 1, '43-33', 'C3'),
(11, 1, '43-35', 'C5'),
(12, 1, '43-37', 'C7'),
(13, 1, '44-31', 'D1'),
(14, 1, '44-33', 'D3'),
(15, 1, '44-35', 'D5'),
(16, 1, '44-37', 'D7'),
(17, 1, '45-31', 'E1'),
(18, 1, '45-32', 'E2'),
(19, 1, '45-33', 'E3'),
(20, 1, '45-34', 'E4'),
(21, 1, '45-35', 'E5'),
(22, 1, '45-36', 'E6'),
(23, 1, '45-37', 'E7'),
(24, 1, '45-38', 'E8'),
(25, 1, '46-31', 'F1'),
(26, 1, '46-32', 'F2'),
(27, 1, '46-33', 'F3'),
(28, 1, '46-34', 'F4'),
(29, 1, '46-35', 'F5'),
(30, 1, '46-36', 'F6'),
(31, 1, '46-37', 'F7');

CREATE TABLE IF NOT EXISTS `vmc_state` (
  `vmc_ref_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`vmc_ref_id`),
  UNIQUE KEY `vmc_state_map` (`vmc_ref_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `vmc_state` (`vmc_ref_id`, `product_id`) VALUES
(1, 7),
(2, 7),
(3, 26),
(4, 13),
(5, 1),
(6, 14),
(7, 15),
(8, 16),
(9, 3),
(10, 6),
(11, 9),
(12, 25),
(13, 19),
(14, 20),
(15, 21),
(16, 28),
(17, 12),
(18, 17),
(19, 4),
(20, 22),
(21, 10),
(22, 27),
(23, 23),
(24, 11),
(25, 5),
(26, 5),
(29, 24);