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
 * @package       dev.Setup.Cmd
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once ('../Common/Setup.php');

/**
 * Given an index and an array, return a bool version
 * 
 * @param mixed $index The index of the array to parse.
 * @param array $array The array to parse.
 * @return bool True if value is set, false otherwise.
 */
function parseBoolFromArray($index, $array) {
	return array_key_exists($index, $array);
}

/**
 * Given an index and an array, return a string version
 * 
 * @param mixed $index The index of the array to parse.
 * @param array $array The array to parse.
 * @return mixed String of value if value is set, null otherwise.
 */
function parseStringFromArray($index, $array) {
	if (array_key_exists($index, $array)) {
		return (string)$array[$index];
	}

	return null;
}

$shortopts = '';
$shortopts .= 'd';	// If present, create the database
$shortopts .= 'h:'; // Users handle
$shortopts .= 'n:'; // Users firstname
$shortopts .= 's:'; // Users surname
$shortopts .= 'e:'; // Users e-mail
$shortopts .= 'k';	// If present, use the 'proper' krb auth script instead of the dummy.
$shortopts .= 'f';	// If present, set-up the tmp folders
$shortopts .= 'v';	// If present, use development configs, settings and databases

$options = getopt($shortopts);

if (is_array($options)) {
	$setup = new Setup();

	$setup->setCreateDatabase( parseBoolFromArray('d', $options) );
	$setup->setUseRealKrb( parseBoolFromArray('k', $options) );
	$setup->setSetupTempFolders( parseBoolFromArray('f', $options) );
	$setup->setUseDevelopmentEnvironment( parseBoolFromArray('v', $options) );
	$setup->setUserInfo(
		parseStringFromArray('n', $options),
		parseStringFromArray('s', $options),
		parseStringFromArray('h', $options),
		parseStringFromArray('e', $options)
	);

	$setup->run();
} else {
	echo 'Unable to parse options\n';
	exit(1);
}