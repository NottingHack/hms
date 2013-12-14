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
 * @package       dev.Setup.Lib.Updates
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$conn = $this->__getDbConnection('default', true);

$this->__logMessage('Removing `member_number` from `members`');
$this->__runQuery($conn, "ALTER TABLE `members` DROP `member_number`");

$this->__logMessage('Removing `handle` from `members`');
$this->__runQuery($conn, "ALTER TABLE `members` DROP `handle`");

$this->__logMessage('Splitting name into firstname and surname');

$this->__logMessage('Renaming `name` to `firstname` in `members`');
$this->__runQuery($conn, 'ALTER TABLE  `members` CHANGE  `name`  `firstname` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL');

$this->__logMessage('Adding `surname` to `members`');
$this->__runQuery($conn, 'ALTER TABLE  `members` ADD  `surname` VARCHAR( 100 ) NULL AFTER  `firstname`');

$this->__logMessage('Removing `unlock_text` from `pins`');
$this->__runQuery($conn, "ALTER TABLE `pins` DROP `unlock_text`");

$this->__logMessage('Removing `description` from `status`');
$this->__runQuery($conn, "ALTER TABLE `status` DROP `description`");

$this->__logMessage('Updating `status`.`title`');

$statements = array(
	"UPDATE `status` SET `title`='Waiting for contact details' WHERE `status_id`=2",
	"UPDATE `status` SET `title`='Waiting for Membership Admin to approve contact details' WHERE `status_id`=3",
	"UPDATE `status` SET `title`='Waiting for standing order payment' WHERE `status_id`=4",
);

$this->__runQuery($conn, join(';', $statements));

$this->__logMessage('Performing split of `member`.`firstname` and `member`.`surname`');
$allMembers = $this->__runQuery($conn, "SELECT * FROM `members`");
$massQuery = '';
foreach ($allMembers as $member) {
	$massQuery .= "UPDATE `members` SET ";
	if ($member['firstname'] == null) {
		$massQuery .= "`firstname`=NULL, `surname`=NULL";
	} else {
		$nameParts = explode(' ', $member['firstname']);

		$firstname = trim($conn->real_escape_string(array_shift($nameParts)));

		if (count($nameParts) > 0) {
			$surname = trim($conn->real_escape_string(implode(' ', $nameParts)));
		} else {
			$surname = '?';
		}

		$massQuery .= "`firstname`='$firstname',`surname`='$surname'";
	}
	$massQuery .= " WHERE `member_id` = {$member['member_id']}" . ';';
}

$this->__runQuery($conn, $massQuery);

$this->__logMessage('Creating `hms_emails` table.');
$query = "CREATE TABLE IF NOT EXISTS `hms_emails` (
		  `hms_email_id` int(11) NOT NULL AUTO_INCREMENT,
		  `member_id` int(11) NOT NULL,
		  `subject` text NOT NULL,
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`hms_email_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$this->__runQuery($conn, $query);

$this->__logMessage('Adding \'Membership Team\' group');
$query = "INSERT INTO  `instrumentation`.`grp` ( `grp_id`, `grp_description`) VALUES (NULL ,  'Membership Team');";
$this->__runQuery($conn, $query);
