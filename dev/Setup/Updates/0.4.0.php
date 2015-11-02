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

$this->__logMessage('Modifiying DB for Bouncer upgrades');

$this->__logMessage('Add column friendly_name to table rfid_tags');
$query = "ALTER TABLE `rfid_tags` 
			ADD COLUMN `friendly_name` varchar(128) NULL DEFAULT NULL";
$this->__runQuery($conn, $query);