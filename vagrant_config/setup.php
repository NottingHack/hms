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
 * @package       dev.Setup.Web
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once ('../Common/Setup.php');

/** 
 * Given an index in the POST var array, return a bool version
 *  
 * @param mixed $index The index of the POST var to parse.
 * @return bool True if value is set, false otherwise.
 */
function parseBoolFromWebVar($index) {
  return array_key_exists($index, $_POST) &&
      $_POST[$index] == 'on';
}

/** 
 * Given an index in the POST var array, return a string version
 *  
 * @param mixed $index The index of the POST var to parse.
 * @return mixed String of value if value is set, null otherwise.
 */
function parseStringFromWebVar($index) {
  if (array_key_exists($index, $_POST) && isset($_POST[$index])) {
    return (string)$_POST[$index];
  }

  return null;
}

$setup = new Setup();

$setup->setCreateDatabase(true);
$setup->setUseRealKrb(true);
$setup->setSetupTempFolders(true);
$setup->setUseDevelopmentEnvironment(true);
$setup->setUserInfo('Admin', 'Account', 'Admin', 'admin@example.org');
$setup->run();

?>

