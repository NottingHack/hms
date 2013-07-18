<?php
	
	$conn = $this->_getDbConnection('default', true);

	$this->_logMessage('Removing `member_number` from `members`');
	$this->_runQuery($conn, "ALTER TABLE `members` DROP `member_number`");

	$this->_logMessage('Removing `handle` from `members`');
	$this->_runQuery($conn, "ALTER TABLE `members` DROP `handle`");

	$this->_logMessage('Splitting name into firstname and surname');
	
	$this->_logMessage('Renaming `name` to `firstname` in `members`');
	$this->_runQuery($conn, 'ALTER TABLE  `members` CHANGE  `name`  `firstname` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL');

	$this->_logMessage('Adding `surname` to `members`');
	$this->_runQuery($conn, 'ALTER TABLE  `members` ADD  `surname` VARCHAR( 100 ) NULL AFTER  `firstname`');

	$this->_logMessage('Removing `unlock_text` from `pins`');
	$this->_runQuery($conn, "ALTER TABLE `pins` DROP `unlock_text`");

	$this->_logMessage('Removing `description` from `status`');
	$this->_runQuery($conn, "ALTER TABLE `status` DROP `description`");

	$this->_logMessage('Updating `status`.`title`');

	$statements = array(
		"UPDATE `status` SET `title`='Waiting for contact details' WHERE `status_id`=2",
		"UPDATE `status` SET `title`='Waiting for Membership Admin to approve contact details' WHERE `status_id`=3",
		"UPDATE `status` SET `title`='Waiting for standing order payment' WHERE `status_id`=4",
	);

	$this->_runQuery($conn, join(';', $statements));

	$this->_logMessage('Performing split of `member`.`firstname` and `member`.`surname`');
	$allMembers = $this->_runQuery($conn, "SELECT * FROM `members`");
	$massQuery = '';
	foreach ($allMembers as $member) 
	{
		$nameParts = explode(' ', $member['firstname']);
		if(count($nameParts) > 0)
		{
			$firstname = $conn->real_escape_string(array_shift($nameParts));
			$surname = $conn->real_escape_string(implode(' ', $nameParts));

			$massQuery .= "UPDATE `members` SET `firstname`='$firstname',`surname`='$surname' WHERE `member_id` = {$member['member_id']}" . ';';
		}
	}

	$this->_runQuery($conn, $massQuery);

?>
