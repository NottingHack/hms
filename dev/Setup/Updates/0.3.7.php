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

$this->__logMessage('Modifiying Accounts natwest/tsb csv changes');

$this->__logMessage('Add column natwest_ref to table accounts');
$query = "ALTER TABLE `account`
			ADD COLUMN `natwest_ref` varchar(18) NOT NULL";
$this->__runQuery($conn, $query);