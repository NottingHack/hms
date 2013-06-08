<?php

$shortopts = '';
$shortopts .= 'd'; 	// If present, create the database
$shortopts .= 'p';  // If present, populate the database
$shortopts .= 'h:'; // Users handle
$shortopts .= 'n:'; // Users name
$shortopts .= 'e:'; // Users e-mail
$shortopts .= 'k';  // If present, use the 'proper' krb auth script instead of the dummy.

$options = getopt($shortopts);

$newline = "\n";

if(!is_array($options))
{
	// Must be being invoked from a browser, grab the options that way
	$createDb = false;
	if(	isset($_POST['createdb']) &&
		$_POST['createdb'] == "on" )
	{
		$createDb = true;
	}

	$populateDb = false;
	if(	isset($_POST['populatedb']) &&
		$_POST['populatedb'] == "on" )
	{
		$populateDb = true;
	}

	$userRealKrb = false;
	if(	isset($_POST['realKrb']) &&
		$_POST['realKrb'] == "on" )
	{
		$userRealKrb = true;
	}

	$name = 'A. Adminson';
	$email = 'info@example.org';
	$handle = 'admin';

	if(isset($_POST['yourname']))
	{
		$name = $_POST['yourname'];
	}

	if(isset($_POST['youremail']))
	{
		$email = $_POST['youremail'];
	}

	if(isset($_POST['yourhandle']))
	{
		$handle = $_POST['yourhandle'];
	}

	$options = array(
		'h' => $handle,
		'n' => $name,
		'e' => $email,
	);

	// Set the boolean options
	// getopt sets the value to false if the option is there
	// and doesn't have that key in the array otherwise.
	// So we emulate PHP's stupid behaviour
	if($createDb)
	{
		$options['d'] = false;
	}

	if($populateDb)
	{
		$options['p'] = false;
	}

	if($userRealKrb)
	{
		$options['k'] = false;
	}

	$newline = "<br />";
}

// Check for required params...
if(!( isset($options['h']) && 
	  isset($options['n']) && 
	  isset($options['e']) ) )
{
	echo "Missing required arguments";
	exit(1);
}

// Sets up the environment for HMS development
// requires a settings file:
// hms.settings
// Uses these settings if not found:

$aSettings = array(
	'database'	=>	array(
		'default_host'		=>	'localhost',
		'default_login'		=>	'hms',
		'default_password'	=>	'',
		'default_database'	=>	'hms',
		'test_host'			=>	'localhost',
		'test_login'		=>	'hms',
		'test_password'		=>	'',
		'test_database'		=>	'hms_test'
	),
	'hms'	=>	array(
		'streetdoor'	=>	'1234',
		'innerdoor'		=>	'1234',
		'wifi'			=>	'123456',
	),
	'krb' =>	array(

	),
	'mailchimp'	=> array(
		'key'	=>	'123456',
		'list'	=>	'123456',
	),
	'email'	=>	array(
		'from_address'	=>	'site@localhost',
		'host'			=>	'localhost',
		'port'			=>	25,
		'username'		=>	'user',
		'password'		=>	'secret',
	),
);

include('hms.settings');

function replaceFields($sTemplate, $aFields) 
{
	foreach ($aFields as $sName => $sValue) 
	{
		$sTemplate = str_replace('%%' . $sName . '%%', $sValue, $sTemplate);
	}
	return $sTemplate;
}

function createConfigFiles()
{
	global $aSettings;
	global $newline;

	$sPath = '../app/Config/';

	$aFiles = array(
		'database',
		'hms',
		'krb',
		'mailchimp',
		'email',
		);

	// Create each of the config files
	foreach ($aFiles as $sFileName) 
	{
		if (!file_exists($sFileName . '.template')) 
		{
			continue;
		}
		$sFile = file_get_contents($sFileName . '.template');

		$sFile = replaceFields($sFile, $aSettings[$sFileName]);

		if (file_put_contents($sPath . $sFileName . '.php', $sFile) !== FALSE) 
		{
			echo("Created " . $sFileName . '.php' . $newline);
		}
		else 
		{
			echo("Failed to create " . $sFileName . '.php' . $newline);
		}
	}
}

function createDatabases()
{
	global $options;
	global $aSettings;
	global $newline;

	$createDb = array_key_exists('d', $options);
	$populateDb = array_key_exists('p', $options);
	if ($createDb || $populateDb) 
	{
		// Main Database
		$oDB = new mysqli($aSettings['database']['default_host'], $aSettings['database']['default_login'], $aSettings['database']['default_password']);

		if ($oDB->connect_error) 
		{
			echo("Couldn't connect to main database$newline");
		}
		else 
		{
			$defaultDbName = $aSettings['database']['default_database'];
			if($createDb)
			{
				if(!$oDB->query("DROP DATABASE " . $defaultDbName))
				{
					echo "Failed to drop database: $defaultDbName$newline";
				}
				if(!$oDB->query("CREATE DATABASE " . $defaultDbName))
				{
					echo "Failed to drop database: $defaultDbName$newline";
				}
				else
				{
					echo "Created database: $defaultDbName$newline";
				}
			}

			if($oDB->select_db($defaultDbName))
			{
				if($populateDb)
				{
					if ($oDB->multi_query(file_get_contents('hms.sql'))) 
					{
						echo("Populated main database$newline");
						$oDB->store_result();
						while ($oDB->more_results()) 
						{
							$oDB->next_result();
							$oDB->store_result();
						}
						// set up dev user
						$sSql = "INSERT INTO `members` (`member_id`, `member_number`, `name`, `email`, `join_date`, `handle`, `unlock_text`, `balance`, `credit_limit`, `member_status`, `username`, `account_id`, `address_1`, `address_2`, `address_city`, `address_postcode`, `contact_number`) VALUES";
						$sSql .= "(6, 111, '" . $options['n'] . "', '" . $options['e'] . "', '" . date("Y-m-d") . "', '" . $options['h'] . "', 'Welcome " . $options['h'] . "', -1200, 5000, 5, '" . $options['h'] . "', NULL, NULL, NULL, NULL, NULL, NULL);";

						if ($oDB->query($sSql)) 
						{
							echo("Created DEV user$newline");
						}
						else 
						{
							echo("Failed to create DEV user, was your input valid?$newline");
							echo($oDB->error . $newline);
						}
					}
					else 
					{
						echo("Failed to populate main database$newline");
					}
				}
			}
			else
			{
				echo "Unable to select database: $defaultDbName$newline";
			}
		}
		$oDB->close();

		// Test Database
		$oDB = new mysqli($aSettings['database']['test_host'], $aSettings['database']['test_login'], $aSettings['database']['test_password'], $aSettings['database']['test_database']);

		if ($oDB->connect_error) 
		{
			echo("Couldn't connect to test database$newline");
		}
		else 
		{
			$testDbName = $aSettings['database']['test_database'];
			if($createDb)
			{
				if(!$oDB->query("DROP DATABASE " . $testDbName))
				{
					echo "Failed to drop database: $testDbName$newline";
				}
				if(!$oDB->query("CREATE DATABASE " . $testDbName))
				{
					echo "Failed to drop database: $testDbName$newline";
				}
				else
				{
					echo "Created database: $testDbName$newline";
				}
			}

			if($oDB->select_db($testDbName))
			{
				if($populateDb)
				{
					if ($oDB->multi_query(file_get_contents('hms_test.sql'))) 
					{
						echo("Populated test database$newline");
					}
					else 
					{
						echo("Failed to populate test database$newline");
					}
				}
			}
			else
			{
				echo "Unable to select database: $testDbName$newline";
			}
		}
		$oDB->close();
	}
}

function copyKrbLibFile()
{
	global $options;
	global $newline;

	$toFile = "../app/Lib/Krb/krb5_auth.php";
	$fromFile = "krb5_auth.dummy";
	if(array_key_exists('k', $options))
	{
		$fromFile = "krb5_auth.real";
	}

	echo "Attempting to copy $fromFile to $toFile... ";

	if(copy($fromFile, $toFile))
	{
		echo "Copy successful$newline";
	}
	else
	{
		echo "Copy failed$newline";
	}
}

echo("Started$newline");
createConfigFiles();
createDatabases();
copyKrbLibFile();
echo("Finished$newline");

?>