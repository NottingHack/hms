<?php
	require_once('../lib/setup.php');

	//! Given an index in the POST var array, return a bool version
	/*!
		@param mixed $index The index of the POST var to parse.
		@retval bool True if value is set, false otherwise.
	*/
	function parseBoolFromWebVar($index)
	{
		return array_key_exists($index, $_POST) &&
				$_POST[$index] == 'on';
	}

	//! Given an index in the POST var array, return a string version
	/*!
		@param mixed $index The index of the POST var to parse.
		@retval mixed String of value if value is set, null otherwise.
	*/
	function parseStringFromWebVar($index)
	{
		if(array_key_exists($index, $_POST) && 
			isset($_POST[$index]))
		{
			return (string)$_POST[$index];
		}

		return null;
	}

	$setup = new Setup();

	$setup->setDatabaseOptions( parseBoolFromWebVar('createdb'), parseBoolFromWebVar('populatedb') );
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
	require('./setup_header.php');
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
	require('./setup_footer.php');
?>