<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       dev.Setup.Updates
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$conn = $this->__getDbConnection('default', true);

$this->__logMessage('Modifiying DB for Banktransaction Updates');

$this->__logMessage('Creating `bank_transactions` table.');
$query = "CREATE TABLE IF NOT EXISTS `bank_transactions` (
           `bank_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
           `transaction_date` date NOT NULL,
           `description` varchar(200) NOT NULL,
           `amount` decimal(8,2) DEFAULT NULL,
           `bank_id` int(11) NOT NULL,
           `account_id` int(11) NULL,
           PRIMARY KEY (`bank_transaction_id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Creating `banks` table.');
$query = "CREATE TABLE IF NOT EXISTS `banks` (
           `bank_id` int(11) NOT NULL AUTO_INCREMENT,
           `name` varchar(100) NOT NULL,
           `sort_code` varchar(8) DEFAULT NULL,
           `account_number` varchar(8) DEFAULT NULL,
           PRIMARY KEY (`bank_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Adding banks to `bank` table');
$query = "INSERT INTO `banks` (`bank_id`, `name`) VALUES
           (1, 'Natwest'),
           (2, 'TSB');";
$this->__runQuery($conn, $query);

$this->__logMessage('new entries into `hms_meta` table.');
$query = "INSERT INTO `hms_meta` (`name`, `value`) VALUES
    ('csv_folder', '/vagrant/app/tmp/csv'),
    ('audit_revoke_interval' , 'P2M'),
    ('audit_warn_interval', 'P1M14D'),
    ('software_team_email', 'software@nottinghack.org.uk'),
    ('accounts_team_email', 'accounts@nottinghack.org.uk'),
    ('trustees_team_email', 'trustees@nottinghack.org.uk')
    ;
    ";
$this->__runQuery($conn, $query);

$this->__logMessage('Add column warned to table members');
$query = "ALTER TABLE `members` 
           ADD COLUMN `warned` TINYINT(1) DEFAULT 0;";
$this->__runQuery($conn, $query);