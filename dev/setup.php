<?php

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
		'test_host'		=>	'localhost',
		'test_login'		=>	'hms',
		'test_password'	=>	'',
		'test_database'	=>	'hms_test'
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
	);

include('hms.settings');

$sPath = '../app/Config/';

$aFiles = array(
	'database',
	'hms',
	'krb',
	'mailchimp',
	);

echo("Started<br />\n");

// Create each of the config files
foreach ($aFiles as $sFileName) {
	if (!file_exists($sFileName . '.template')) {
		continue;
	}
	$sFile = file_get_contents($sFileName . '.template');

	$sFile = replaceFields($sFile, $aSettings[$sFileName]);

	if (file_put_contents($sPath . $sFileName . '.php', $sFile) !== FALSE) {
		echo("Created " . $sFileName . '.php' . "<br / >\n");
	}
	else {
		echo("Failed to create " . $sFileName . '.php' . "<br / >\n");
	}
}

// Main Database
$oDB = new mysqli($aSettings['database']['default_host'], $aSettings['database']['default_login'], $aSettings['database']['default_password'], $aSettings['database']['default_database']);

if ($oDB->connect_error) {
	echo("Couldn't connect to main database<br/>\n");
}
else {
	$oDB->multi_query(file_get_contents('hms.sql'));
	echo("Created main database<br/>\n");
}
$oDB->close();

// Test Database
$oDB = new mysqli($aSettings['database']['test_host'], $aSettings['database']['test_login'], $aSettings['database']['test_password'], $aSettings['database']['test_database']);

if ($oDB->connect_error) {
	echo("Couldn't connect to test database<br/>\n");
}
else {
	$oDB->multi_query(file_get_contents('hms_test.sql'));
	echo("Created test database<br/>\n");
}
$oDB->close();

echo("Finished<br />\n");


function replaceFields($sTemplate, $aFields) {
	foreach ($aFields as $sName => $sValue) {
		$sTemplate = str_replace('%%' . $sName . '%%', $sValue, $sTemplate);
	}
	return $sTemplate;
}

?>