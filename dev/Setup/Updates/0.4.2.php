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

$this->__logMessage('Update tp MemberBoxes table');

$this->__logMessage('Update `member_boxes` table.');
$query = "ALTER TABLE  `member_boxes` CHANGE  `brought_date`  `bought_date` DATE NOT NULL;";
$this->__runQuery($conn, $query);

