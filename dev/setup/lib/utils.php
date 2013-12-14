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
 * @package       dev.Setup.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Given a relative path from this file, get an absolute path.
 * 
 * @param string $path The relative path to convert.
 * @return string The absolute path.
 */
function makeAbsolutePath($path) {
	if (count($path) > 0) {
		$firstChar = $path[0];
		if ($firstChar != '/' && $firstChar != '\\') {
			$path = '/' . $path;
		}
	}
	return dirname(__FILE__) . $path;
}
