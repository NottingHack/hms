<?php

$shortopts = '';
$shortopts .= 'd'; 	// If present, create the database
$shortopts .= 'p';  // If present, populate the database
$shortopts .= 'h:'; // Users handle
$shortopts .= 'n:'; // Users name
$shortopts .= 'e:'; // Users e-mail
$shortopts .= 'k';  // If present, use the 'proper' krb auth script instead of the dummy.
$shortopts .= 'f';  // If present, set-up the tmp folders

$options = getopt($shortopts);

$newline = "\n";

function logMessage($message)
{
	global $newline;
	
	echo sprintf("[%s] %s$newline", date("H:i:s"), $message);
}

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

	$setupTempFolders = false;
	if(	isset($_POST['setuptmpfolders']) &&
		$_POST['setuptmpfolders'] == "on" )
	{
		$setupTempFolders = true;
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

	if($setupTempFolders)
	{
		$options['f'] = false;
	}

	$newline = "<br />";
}

// Check for required params...
if(!( isset($options['h']) && 
	  isset($options['n']) && 
	  isset($options['e']) ) )
{
	logMessage("Missing required arguments");
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
			logMessage("Created $sFileName.php");
		}
		else 
		{
			logMessage("Failed to create $sFileName.php");
		}
	}
}

function createDatabases()
{
	global $options;
	global $aSettings;

	$createDb = array_key_exists('d', $options);
	$populateDb = array_key_exists('p', $options);
	if ($createDb || $populateDb) 
	{
		// Main Database
		$oDB = new mysqli($aSettings['database']['default_host'], $aSettings['database']['default_login'], $aSettings['database']['default_password']);

		if ($oDB->connect_error) 
		{
			logMessage("Couldn't connect to main database");
		}
		else 
		{
			$defaultDbName = $aSettings['database']['default_database'];
			if($createDb)
			{
				if(!$oDB->query("DROP DATABASE " . $defaultDbName))
				{
					logMessage("Failed to drop database: $defaultDbName");
				}
				if(!$oDB->query("CREATE DATABASE " . $defaultDbName))
				{
					logMessage("Failed to drop database: $defaultDbName");
				}
				else
				{
					logMessage("Created database: $defaultDbName");
				}
			}

			if($oDB->select_db($defaultDbName))
			{
				if($populateDb)
				{
					if ($oDB->multi_query(file_get_contents('hms.sql'))) 
					{
						logMessage("Populated main database");
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
							logMessage("Created DEV user");
						}
						else 
						{
							logMessage("Failed to create DEV user, was your input valid?");
							logMessage($oDB->error);
						}
					}
					else 
					{
						logMessage("Failed to populate main database");
					}
				}
			}
			else
			{
				logMessage("Unable to select database: $defaultDbName");
			}
		}
		$oDB->close();

		// Test Database
		$oDB = new mysqli($aSettings['database']['test_host'], $aSettings['database']['test_login'], $aSettings['database']['test_password'], $aSettings['database']['test_database']);

		if ($oDB->connect_error) 
		{
			logMessage("Couldn't connect to test database");
		}
		else 
		{
			$testDbName = $aSettings['database']['test_database'];
			if($createDb)
			{
				if(!$oDB->query("DROP DATABASE " . $testDbName))
				{
					logMessage("Failed to drop database: $testDbName");
				}
				if(!$oDB->query("CREATE DATABASE " . $testDbName))
				{
					logMessage("Failed to drop database: $testDbName");
				}
				else
				{
					logMessage("Created database: $testDbName");
				}
			}

			if($oDB->select_db($testDbName))
			{
				if($populateDb)
				{
					if ($oDB->multi_query(file_get_contents('hms_test.sql'))) 
					{
						logMessage("Populated test database");
					}
					else 
					{
						logMessage("Failed to populate test database");
					}
				}
			}
			else
			{
				logMessage("Unable to select database: $testDbName");
			}
		}
		$oDB->close();
	}
}

function copyKrbLibFile()
{
	global $options;

	$toFile = "../app/Lib/Krb/krb5_auth.php";
	$fromFile = "krb5_auth.dummy";
	if(array_key_exists('k', $options))
	{
		$fromFile = "krb5_auth.real";
	}

	$message = "Attempting to copy $fromFile to $toFile... ";

	if(copy($fromFile, $toFile))
	{
		$message .= "Copy successful";
	}
	else
	{
		$message .= "Copy failed";
	}

	logMessage($message);
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function setupTempFolders()
{
	global $options;

	if(array_key_exists('f', $options))
	{
		$foldersToMake = array(
			'../app/tmp',
			'../app/tmp/cache',
			'../app/tmp/cache/models',
			'../app/tmp/cache/persistent',
			'../app/tmp/cache/views',
			'../app/tmp/logs',
			'../app/tmp/sessions',
			'../app/tmp/tests',
		);

		foreach ($foldersToMake as $folder) 
		{
			if(file_exists($folder))
			{
				logMessage("Folder $folder already exists, deleting...");
				deleteDir($folder);
			}
			if(mkdir($folder, 0777, true))
			{
				logMessage("Created folder: $folder");
			}
			else
			{
				logMessage("Failed to create folder: $folder");
			}
		}
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>HMS Setup</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>

</head>
<body id="main_body" >
	<img id="top" src="top.png" alt="">
	<div id="form_container">
		<h1><a>Invisible Text</a></h1>
		<div class="results_header">
			<div class="form_description">
				<h2>HMS Setup</h2>
				<p>Get up and running with HMS easily</p>
			</div>
		</div>

		<p class="results">
			<?php
				logMessage("Started");
				createConfigFiles();
				createDatabases();
				copyKrbLibFile();
				setupTempFolders();
				logMessage("Finished");
			?>

			<ul class="actions">
				<li>
					<a href="../" class="positive">Go to HMS</a>
				</li>
				<li>
					<a href="../test.php" class="positive">Run Tests</a>
				</li>
			</ul>
		</p>

		<div id="footer">
			Generated by <a href="http://www.phpform.org">pForm</a>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
	</body>
</html>

