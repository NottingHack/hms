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

require_once ('../Lib/Setup.php');

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

$setup->setCreateDatabase( parseBoolFromWebVar('createdb') );
$setup->setUseRealKrb( parseBoolFromWebVar('realKrb') );
$setup->setSetupTempFolders( parseBoolFromWebVar('setuptmpfolders') );
$setup->setUseDevelopmentEnvironment( parseBoolFromWebVar('usedevelopmentenv') );
$setup->setUserInfo(
	parseStringFromWebVar('firstname'),
	parseStringFromWebVar('surname'),
	parseStringFromWebVar('username'),
	parseStringFromWebVar('email')
);

?>

<?php
require ('./header.html');
?>
	<p class="results">
			<?php
				$setup->run();
			?>

			<ul class="actions">
				<li>
					<a href="../../../" class="positive">Go to HMS</a>
				</li>
				<li>
					<a href="../../../test.php" class="positive">Run Tests</a>
				</li>
			</ul>
		</p>

<?php
require ('./footer.html');