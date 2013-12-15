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

ini_set('max_execution_time', 600); // Give us 10 mins to execute, we might be doing a lot

require_once ('Utils.php');
require_once ('DataGenerator.php');

/**
 * Setup is a class that is responsible for parsing options and then performing certain steps based on those options.
 */
class Setup {

/**
 * Current log indentation level.
 * @var string
 */
	private $__logIndent = 0;

/**
 * Array of __settings data.
 * @var string
 */
	private $__settings = array();

/**
 * If true create the instrumentation and instrumentation_test databases and populate them with tables and data.
 * @var string
 */
	private $__createDb = false;

/**
 * If true then use the real KRB Auth lib, otherwise use a dummy one.
 * @var string
 */
	private $__useRealKrb = false;

/**
 * If true create the temporary folders CakePHP needs.
 * @var string
 */
	private $__setupTempFolders = false;

/**
 * Prefer files with this suffix.
 * @var string
 */
	private $__environmentType = 'production';

/**
 * Firstname the admin user should be created with.
 * @var string
 */
	private $__firstname = '';

/**
 * Surname the admin user should be created with.
 * @var string
 */
	private $__surname = '';

/**
 * Username the admin user should be created with.
 * @var string
 */
	private $__username = '';

/**
 * Email address the admin user should be created with.
 * @var string
 */
	private $__email = '';

/**
 * Constructor.
 */
	public function __construct() {
		if (!$this->__validateOptions()) {
			$this->__logMessage('Invalid arguments.');
			exit(1);
		}
		$this->__settings = $this->__getSettings();
	}

/**
 * Increase the log indent level.
 */
	private function __pushLogIndent() {
		$this->__logIndent++;
	}

/**
 * Decrease the log indent level.
 */
	private function __popLogIndent() {
		$this->__logIndent--;
		if ($this->__logIndent < 0) {
			$this->__logIndent = 0;
			$this->__logMessage('Warning: Log indent was popped too many times.');
		}
	}

/** 
 * Set up the database options.
 *
 * @param bool $__createDb If true then the database will be created..
 */
	public function setCreateDatabase($__createDb) {
		$this->__createDb = $__createDb;
	}

/** 
 * Set if we should use the real KRB lib or not.
 *
 * @param bool $useRealKrb If true then the real KRB code will be copied in to the lib folder, if false, a dummy file will be copied.
 */
	public function setUseRealKrb($useRealKrb) {
		$this->__useRealKrb = $useRealKrb;
	}

/** 
 * Set if we should use development or production configs, __settings and databases.
 *
 * @param bool $useDevelopmentEnv If true then use development configs, __settings and databases.
 */
	public function setUseDevelopmentEnvironment($useDevelopmentEnv) {
		$this->__environmentType = $useDevelopmentEnv ? 'development' : 'production';
	}

/** 
 * Set if we should create the temporary folders.
 *
 * @param bool $setupTempFolders If true then temporary folders will be created.
 */
	public function setSetupTempFolders($setupTempFolders) {
		$this->__setupTempFolders = $setupTempFolders;
	}

/** 
 * Set up the user info.
 *
 * @param string $firstname First name of the user.
 * @param string $surname Surname of the user.
 * @param string $username Username of the user.
 * @param string $email Users e-mail.
 */
	public function setUserInfo($firstname, $surname, $username, $email) {
		$this->__firstname = $firstname;
		$this->__surname = $surname;
		$this->__username = $username;
		$this->__email = $email;
	}

/** 
 * Replace variables in a template using the options in fields.
 *
 * @param string $template The template to work with.
 * @param array $fields Key-value pair of names and the values to substitute.
 * @return string The template with values replaced.
 */
	private function __replaceFields($template, $fields) {
		foreach ($fields as $name => $value) {
			$template = str_replace('%%' . $name . '%%', $value, $template);
		}
		return $template;
	}

/** 
 * Create the config files for the current environment, delete any no-longer needed config files.
 *
 * @param array $__settings The __settings to use.
 */
	private function __setupConfigFiles() {
		$this->__logMessage('Setting up config files');
		$this->__pushLogIndent();

		$configPath = '../../../app/Config/';

		$__settings = $this->__settings;

		// ... Not that I'm OCD or anything
		asort($__settings);

		// Create each of the config files
		foreach ($__settings as $settingName => $settingData) {
			// First check for dev/production templates
			$templateFilePath = makeAbsolutePath("$settingName.{$this->__environmentType}.template");
			if (!file_exists($templateFilePath)) {
				// Fall back to the regular template
				$templateFilePath = makeAbsolutePath("$settingName.template");

				if (!file_exists($templateFilePath)) {
					$fullFilePath = makeAbsolutePath("$configPath$settingName.php");
					if (file_exists($fullFilePath)) {
						$this->__logMessage("Deleting __settings file at $fullFilePath because we couldn't find a matching template");
						unlink($fullFilePath);
					}
					continue;
				}
			}

			$currentContents = file_get_contents($templateFilePath);
			$newContents = $this->__replaceFields($currentContents, $settingData);

			if (file_put_contents("$configPath$settingName.php", $newContents) !== false) {
				$this->__logMessage("Created $settingName.php from template $templateFilePath");
			} else {
				$this->__logMessage("Failed to create $settingName.php");
			}
		}

		$this->__popLogIndent();
	}

/** 
 * Get an array of all sql files in the ./sql folder that are valid for the current configuration and contain a certain string.
 *
 * @param string $subStr Files returned must contain this string.
 * @return array A list of filenames for sql files that match the $subStr and environment criteria.
 */
	private function __getSqlFilesContaining($subStr) {
		$validFiles = array();
		$dirList = scandir(makeAbsolutePath('./sql'));

		foreach ($dirList as $filename) {
			if ( pathinfo($filename, PATHINFO_EXTENSION) == 'sql' &&
				strpos($filename, $subStr) !== false ) {
				// Check if it passes the environment type test.
				$parts = explode('.', basename($filename));
				if (count($parts) > 1) {
					// Is this file for all environment types or the one we're currently using?
					if ($parts[1] == 'sql' ||
						$parts[1] == $this->__environmentType) {
						array_push($validFiles, makeAbsolutePath('./sql/' . $filename));
					}
				}
			}
		}

		return $validFiles;
	}

/** 
 * Run a query on a database object, returning the result
 *
 * @param mysqli $databaseObj A database object to run the query on.
 * @param string $query The query to run.
 * @return mixed An array of results if query is successful, or null on error.
 */
	private function __runQuery($databaseObj, $query) {
		$results = array();
		if ($databaseObj->multi_query($query)) {
			do {
				if ($result = $databaseObj->store_result()) {
					array_push($results, $result->fetch_all(MYSQLI_ASSOC));
					$result->free();
				}

				if (!$databaseObj->more_results()) {
					break;
				}
				$databaseObj->next_result();
			} while (true);

			// If we only ran one query and got one result
			// just return that
			if (count($results) == 1) {
				return $results[0];
			}

			return $results;
		} else {
			if ($databaseObj->error) {
				$this->__logMessage(sprintf('Error: Query - %s failed with message - %s', $query, $databaseObj->error));
			}
		}

		return null;
	}

/** 
 * Run a query from a file.
 *
 * @param mysqli $databaseObj A database object to run the query on.
 * @param string $filepath Path to the file containing the query to run.
 * @return mixed An array of results if query is successful, or null on error.
 */
	private function __runQueryFromFile($databaseObj, $filepath) {
		$this->__logMessage("Executing SQL in: $filepath");
		return $this->__runQuery($databaseObj, file_get_contents($filepath));
	}

/** 
 * Given a list of files containing sql queries, run all the queries.
 *
 * @param mysqli $databaseObj A database object to run the queries on.
 * @param string $fileList The list of files.
 * @return mixed An array of results of the queries that succeded.
 */
	private function __runAllQueriesInFileList($databaseObj, $fileList) {
		$results = array();
		foreach ($fileList as $file) {
			$queryResult = $this->__runQueryFromFile($databaseObj, $file);
			if (is_array($queryResult)) {
				array_merge($queryResult);
			}
		}

		return $results;
	}

/** 
 * Run all queries marked as 'schema'.
 *
 * @param mysqli $databaseObj A database object to run the queries on.
 * @return mixed An array of results of the queries that succeded.
 */
	private function __runSchemaQueries($databaseObj) {
		$files = $this->__getSqlFilesContaining('schema');
		return $this->__runAllQueriesInFileList($databaseObj, $files);
	}

/** 
 * Run all queries marked as 'data'.
 *
 * @param mysqli $databaseObj A database object to run the queries on.
 * @return mixed An array of results of the queries that succeded.
 */
	private function __runDataQueries($databaseObj) {
		$files = $this->__getSqlFilesContaining('data');
		return $this->__runAllQueriesInFileList($databaseObj, $files);
	}

/** 
 * Divide $membersRemaining by $divisor, return the result and adjust $membersRemaining.
 *
 * @param int $membersRemaining (in, out) The number of members left to distribute.
 * @param int $divisor The portion of members that should be used.
 * @return int The number of members used.
 */
	private function __distribureMembers(&$membersRemaining, $divisor) {
		$numUsed = (int)($membersRemaining / $divisor);
		$membersRemaining -= $numUsed;
		return $numUsed;
	}

/** 
 * Write $data to the file at $path, overwriting the file if it exists.
 *
 * @param string $path The path to the file.
 * @param string $data The data to write.
 * @return bool True if data was written successfully, false otherwise.
 */
	private function __writeToFile($path, $data) {
		$handle = fopen(makeAbsolutePath($path), 'w');
		if ($handle) {
			$result = fwrite($handle, $data);
			fclose($handle);

			return $result !== false;
		}

		return false;
	}

/** 
 * Generate the data that will populate the database.
 *
 * @return bool True if data was generated and written correctly, false otherwise.
 */
	private function __generateData() {
		$this->__logMessage('Generating test data');
		$this->__pushLogIndent();

		$totalMembersToGenerate = 200;
		$membersRemaining = $totalMembersToGenerate;

		$genDetails = array();

		// Half the members should be current members
		$genDetails[Status::CURRENT_MEMBER] = $this->__distribureMembers($membersRemaining, 2);

		// 1/3rd of the remaining members should be ex members
		$genDetails[Status::EX_MEMBER] = $this->__distribureMembers($membersRemaining, 3);

		// Distribute the rest of the members evenly over the other statuses
		$toAssign = (int)floor($membersRemaining / 4);

		$genDetails[Status::PROSPECTIVE_MEMBER] = $toAssign;
		$genDetails[Status::PRE_MEMBER_1] = $toAssign;
		$genDetails[Status::PRE_MEMBER_2] = $toAssign;
		$genDetails[Status::PRE_MEMBER_3] = $toAssign;

		$membersRemaining -= ($toAssign * 4);

		// Any left? Make them current members
		$genDetails[Status::CURRENT_MEMBER] += $membersRemaining;

		$this->__logMessage("Generating data for $totalMembersToGenerate members.");

		// Generate!
		$gen = new DataGenerator();
		foreach ($genDetails as $status => $num) {
			for ($i = 0; $i < $num; $i++) {
				$gen->generateMember($status);
			}
		}

		// Finally generate the dev user
		$details = array(
			'firstname' => $this->__firstname,
			'surname' => $this->__surname,
			'email' => $this->__email,
			'username' => $this->__username,
			'groups' => array(
				Group::CURRENT_MEMBERS, Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN,
			),
		);
		$gen->generateMember(Status::CURRENT_MEMBER, $details);

		$this->__logMessage('Writing SQL files');

		$pathsAndFunctions = array(
			'./sql/members_data.sql' => function() use (&$gen)
			{
				return $gen->getMembersSql();
			},

			'./sql/member_group_data.sql' => function() use (&$gen)
			{
				return $gen->getMembersGroupSql();
			},

			'./sql/account_data.sql' => function() use (&$gen)
			{
				return $gen->getAccountsSql();
			},

			'./sql/pins_data.sql' => function() use (&$gen)
			{
				return $gen->getPinsSql();
			},

			'./sql/rfid_tags_data.sql' => function() use (&$gen)
			{
				return $gen->getRfidTagsSql();
			},

			'./sql/status_updates_data.sql' => function() use (&$gen)
			{
				return $gen->getStatusUpdatesSql();
			},

			'./sql/mailinglists_data.development.sql' => function() use (&$gen)
			{
				return $gen->getMailingListsSql();
			},

			'./sql/mailinglist_subscriptions_data.development.sql' => function() use (&$gen)
			{
				return $gen->getMailingListSubscriptionsSql();
			},

			'./sql/hms_emails_data.development.sql' => function() use (&$gen)
			{
				return $gen->getEmailRecordSql();
			},
		);

		foreach ($pathsAndFunctions as $path => $func) {
			if ($this->__writeToFile($path, $func())) {
				$this->__logMessage("Wrote $path");
			} else {
				$this->__logMessage("Failed to write $path");
				$this->__popLogIndent();
				return false;
			}
		}

		$this->__popLogIndent();
		return true;
	}

/** 
 * Get a mysqli database connection.
 *
 * @param string $connName The name of the connection to get (e.g. default/test)
 * @param bool $select If true then the database will be selected.
 * @return mixed A mysql connection on success, or null on failure.
 */
	private function __getDbConnection($connName, $select) {
		$hostName = $connName . '_host';
		$login = $connName . '_login';
		$password = $connName . '_password';
		$database = $connName . '_database';

		$__settings = $this->__settings['database'];

		if ( array_key_exists($hostName, $__settings) &&
			array_key_exists($login, $__settings) &&
			array_key_exists($password, $__settings) &&
			array_key_exists($database, $__settings) ) {
			$conn = new mysqli($__settings[$hostName], $__settings[$login], $__settings[$password]);
			if ($select) {
				$conn->select_db($__settings[$database]);
			}
			return $conn;
		}

		return null;
	}

/** 
 * Set-up a database.
 *
 * @param string $prefix The prefix of the database to create.
 * @param bool $populate If true then run schema and data queries on the newly created database.
 */
	private function __setupDatabase($prefix, $populate) {
		$conn = $this->__getDbConnection($prefix, false);

		// @codingStandardsIgnoreStart
		$connError = $conn->connect_error;
		// @codingStandardsIgnoreEnd

		if ($connError) {
			$this->__logMessage("Couldn't connect to main database");
		} else {
			$dbName = $this->__settings['database'][$prefix . '_database'];
			if (!$conn->query("DROP DATABASE " . $dbName)) {
				$this->__logMessage("Failed to drop database: $dbName");
			}

			if (!$conn->query("CREATE DATABASE " . $dbName)) {
				$this->__logMessage("Failed to drop database: $dbName");
			} else {
				$this->__logMessage("Created database: $dbName");
			}

			if ( $populate && $conn->select_db($dbName)) {
				$this->__runSchemaQueries($conn);
				$this->__runDataQueries($conn);
			} else {
				$this->__logMessage("Unable to select database: $dbName");
			}
		}
		$conn->close();
	}

/** 
 * Create and/or populate the databases for HMS
 */
	private function __createDatabases() {
		if ($this->__createDb) {
			$this->__logMessage('Creating and/or populating databases');
			$this->__pushLogIndent();

			$this->__setupDatabase('default', true);
			$this->__setupDatabase('test', false);

			// If we've just created the data in the database then we're already on
			// the latest database version
			$this->__writeDbVersion($this->__getCodeVersion());

			$this->__popLogIndent();
		}
	}

/** 
 * Copy a file from one location to another, creating the folder if needed.
 *
 * @param string $fromFile The path to copy the file from.
 * @param string $toFile The path to copy the file to.
 */
	private function __copyLibFile($fromFile, $toFile) {
		$libFolder = dirname($toFile);
		if (!file_exists($libFolder)) {
			if (mkdir($libFolder)) {
				$this->__logMessage("Created folder at: $libFolder");
			} else {
				$this->__logMessage("Failed to create folder at: $libFolder");
			}
		}

		$this->__logMessage("Attempting to copy $fromFile to $toFile");

		if (copy(makeAbsolutePath($fromFile), $toFile)) {
			$this->__logMessage('Copy successful');
		} else {
			$this->__logMessage('Copy failed');
		}
	}

/** 
 * Copy either the real or dummy KRB Auth lib file to the lib folder.
 */
	private function __copyKrbLibFile() {
		$this->__logMessage('Copying KRB lib file');
		$this->__pushLogIndent();

		$fromFile = $this->__useRealKrb ? 'krb5Auth.real.php' : 'krb5Auth.dummy.php';
		$this->__copyLibFile($fromFile, '../../../app/Lib/Krb/krb5Auth.php');

		$this->__popLogIndent();
	}

/** 
 * Copy either the production or development MCAPI file.
 */
	private function __copyMcapiLibFile() {
		$this->__logMessage('Copying MCAPI lib file');
		$this->__pushLogIndent();

		$fromFile = "MCAPI.{$this->__environmentType}";
		$this->__copyLibFile($fromFile, '../../../app/Lib/MailChimp/MCAPI.php');

		$this->__popLogIndent();
	}

/** 
 * Given a path to a directory, delete the directory and all it's contents.
 *
 * @param string $dirPath The path to the directory to delete.
 * @throws InvalidArgumentException if $dirPath is not a directory.
 */
	private function __deleteDir($dirPath) {
		if (!is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}

		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}

		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->__deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

/** 
 * Create the temporary folders for HMS, will delete the old ones if they exist.
 */
	private function __setupTempFolders() {
		if ($this->__setupTempFolders) {
			$this->__logMessage('Setting up temp folders');
			$this->__pushLogIndent();

			$foldersToMake = array(
				'../../../app/tmp',
				'../../../app/tmp/cache',
				'../../../app/tmp/cache/models',
				'../../../app/tmp/cache/persistent',
				'../../../app/tmp/cache/views',
				'../../../app/tmp/logs',
				'../../../app/tmp/sessions',
				'../../../app/tmp/tests',
			);

			foreach ($foldersToMake as $folder) {
				if (file_exists($folder)) {
					$this->__logMessage("Folder $folder already exists, deleting...");
					$this->__deleteDir($folder);
				}

				if (mkdir($folder, 0777, true)) {
					$this->__logMessage("Created folder: $folder");
				} else {
					$this->__logMessage("Failed to create folder: $folder");
				}
			}

			$this->__popLogIndent();
		}
	}

/** 
 * Check to see if we're running on the command line
 *
 * @return bool True if we're running on the command line, false otherwise.
 */
	private function __isOnCommandline() {
		// Note: Like most things in PHP this function isn't reliable:
		// http://php.net/manual/en/function.php-sapi-name.php
		$sapiType = php_sapi_name();

		return ($sapiType == 'cli');
	}

/** 
 * Format and write a log message, prepends timestamp and appends a newline.
 *
 * @param string $message The message to write.
 */
	private function __logMessage($message) {
		$timestamp = date("H:i:s");
		if ($this->__isOnCommandline()) {
			echo sprintf("[%s]%s%s%s", $timestamp, str_repeat("    ", $this->__logIndent), $message, PHP_EOL);
		} else {
			echo '<span class="logLine">';
			echo "[$timestamp] ";
			echo str_repeat('<span class="logSpacer"> </span>', $this->__logIndent + 1);
			echo $message;
			echo '</span>';

			ob_flush();
			flush();
		}
	}

/** 
 * Check if the options used are valid.
 *
 * @return bool True if options are valid, false otherwise.
 */
	private function __validateOptions() {
		// Certain variables are required
		if ($this->__createDb) {
			if (!(isset($this->__firstname) && isset($this->__surname) && isset($this->__username) && isset($this->__email))) {
				return false;
			}
		}

		return true;
	}

/** 
 * Get the __settings to use, either default setting or those loaded in from a file.
 */
	private function __getSettings() {
		// Default __settings
		$defaultSettings = array(
			'database'	=>	array(
				'default_host' => 'localhost',
				'default_login' => 'hms',
				'default_password' => '',
				'default_database' => 'hms',
				'test_host' => 'localhost',
				'test_login' =>	'hms',
				'test_password' => '',
				'test_database' => 'hms_test'
			),
			'hms' => array(
				'streetdoor' => '1234',
				'innerdoor' => '1234',
				'wifi' => '123456',
			),
			'debug' => array(
			),
			'krb' => array(
			),
			'mailchimp'	=> array(
				'key' => 'w1zg905ych1e090og9pvjb7td6b05vlg-2y8',
				'list' => 'us8gz1v8rq',
			),
			'email' => array(
				'from_address' => 'site@localhost',
				'host' => 'localhost',
				'port' => 25,
				'username' => 'user',
				'password' => 'hunter2',
			),
		);

		$overrideSettings = 'hms.settings';
		if (file_exists(makeAbsolutePath($overrideSettings))) {
			include ($overrideSettings);
		}

		// Merge the default and override settings, preferring the override ones
		$finalSettings = array();
		foreach ($defaultSettings as $name => $values) {
			if (array_key_exists($name, $aSettings)) {
				$finalSettings[$name] = $aSettings[$name];
			} else {
				$finalSettings[$name] = $defaultSettings[$name];
			}
		}

		return $finalSettings;
	}

/** 
 * Given an array of version information, check if it is valid.
 *
 * @param array $version An array of version data.
 * @return bool True if version is valid, false otherwise.
 */
	private function __isValidVersion($version) {
		return
			array_key_exists('major', $version) &&
			array_key_exists('minor', $version) &&
			array_key_exists('build', $version);
	}

/** 
 * Read the current version from the code.
 *
 * @return mixed Array of the code version number, or null on error.
 */
	private function __getCodeVersion() {
		$versionFilePath = '../../../app/Controller/AppController.php';

		$version = array();
		$lines = explode(';', file_get_contents(makeAbsolutePath($versionFilePath)));

		$versionIds = array(
			"VERSION_MAJOR" => 'major',
			"VERSION_MINOR" => 'minor',
			"VERSION_BUILD" => 'build',
		);

		foreach ($lines as $line) {
			foreach ($versionIds as $codeId => $arrayIdx) {
				$matches = array();
				$regex = $codeId . " = (\d+?)";
				if (preg_match("/$regex/", trim($line), $matches)) {
					$version[$arrayIdx] = $matches[1];
				}
			}
		}

		if ($this->__isValidVersion($version)) {
			return $version;
		}
		return null;
	}

/** 
 * Write a version to the database version file.
 *
 * @param string $version The version to write.
 */
	private function __writeDbVersion($version) {
		$versionStr = $this->__versionToString($version);
		$this->__logMessage("Writing db version $versionStr to database");

		$conn = $this->__getDbConnection('default', true);
		$this->__runQueryFromFile($conn, makeAbsolutePath('sql/hms_meta_schema.sql'));
		$this->__runQuery($conn, "INSERT INTO `hms_meta` (`name`, `value`) VALUES ('db_version', '$versionStr') ON DUPLICATE KEY UPDATE value='$versionStr'");
	}

/** 
 * Attempt to read the database version from the database.version file
 *
 * @return mixed Array of version data if successful, null on error.
 */
	private function __readDbVersion() {
		$conn = $this->__getDbConnection('default', true);
		$result = $this->__runQuery($conn, "SELECT `value` FROM `hms_meta` WHERE `name`='db_version'");
		if (is_array($result) && count($result) > 0) {
			$key = 'value';
			$data = $result[0];

			if (array_key_exists($key, $data)) {
				return $this->__stringToVersion($data[$key]);
			}
		}

		return null;
	}

/** 
 * Get the difference between two versions.
 *
 * @param mixed $versionA An array of version data, or a string that can be converted into an array.
 * @param mixed $versionB An array of version data, or a string that can be converted into an array.
 * @return mixed An integer representing the difference between the versions, or null on error.
 */
	private function __compareVersions($versionA, $versionB) {
		if (is_string($versionA)) {
			$versionA = $this->__stringToVersion($versionA);
		}

		if (is_string($versionB)) {
			$versionB = $this->__stringToVersion($versionB);
		}

		if ($versionA == null || $versionB == null) {
			return null;
		}

		$majorDiff = $versionA['major'] - $versionB['major'];
		$minorDiff = $versionA['minor'] - $versionB['minor'];
		$buildDiff = $versionA['build'] - $versionB['build'];

		if ($majorDiff != 0) {
			return $majorDiff;
		}

		if ($minorDiff != 0) {
			return $minorDiff;
		}

		if ($buildDiff != 0) {
			return $buildDiff;
		}

		return 0;
	}

/** 
 * Given an array of version data, return a string representation of that version.
 *
 * @param array $version An array of version data.
 * @return string A string representing the version data.
 */
	private function __versionToString($version) {
		return sprintf('%s.%s.%s', $version['major'], $version['minor'], $version['build']);
	}

/** 
 * Given a string representation of a version, return an array of version data.
 *
 * @param string $versionStr A string representing a version.
 * @return array An array of version data.
 */
	private function __stringToVersion($versionStr) {
		list($major, $minor, $build) = explode('.', $versionStr);
		return compact('major', 'minor', 'build');
	}

/** 
 * Update the database to the current version.
 */
	private function __runDatabaseUpdate() {
		if (!$this->__createDb) {
			$this->__logMessage('Updating database');
			$this->__pushLogIndent();

			// Find out what version we're updating from
			$currentDbVersion = $this->__readDbVersion();
			if ($currentDbVersion == null) {
				$this->__logMessage('Error: Could not read current database version');
				return;
			}
			$this->__logMessage('DB Version: ' . $this->__versionToString($currentDbVersion));

			// Find out which version we should be updating to
			$codeVersion = $this->__getCodeVersion();
			if ($codeVersion == null) {
				$this->__logMessage('Error: Could not get code version');
				return;
			}
			$this->__logMessage('Code Version: ' . $this->__versionToString($codeVersion));

			if ( $this->__compareVersions($currentDbVersion, $codeVersion) == 0 ) {
				$this->__logMessage('No update required');
				$this->__popLogIndent();
				return;
			}

			$this->__logMessage(sprintf('Updating from version: %s to version %s',
				$this->__versionToString($currentDbVersion), $this->__versionToString($codeVersion)));

			// Ok, lets get started.
			// First we find all the update files, and sort them by version
			$updatesPath = makeAbsolutePath('updates');
			$updateFiles = array();
			$files = glob($updatesPath . '/*.php');
			foreach ($files as $file) {
				if (is_file($file)) {
					$fileParts = pathinfo($file);
					$fileVersion = $this->__stringToVersion($fileParts['filename']);
					if (!$this->__isValidVersion($fileVersion)) {
						$this->__logMessage("Warning: Found update php with a filename that is not a valid version $file");
						continue;
					}

					$updateFiles[$this->__versionToString($fileVersion)] = $file;
				}
			}

			uksort($updateFiles, array($this, "__compareVersions"));

			// Then execute the version file for any version that's ahead of us
			// until we hit the code version
			foreach ($updateFiles as $updateVersion => $path) {
				$currentVersionDiff = $this->__compareVersions($updateVersion, $currentDbVersion);
				$codeVersionDiff = $this->__compareVersions($updateVersion, $codeVersion);

				if ($currentVersionDiff > 0 && $codeVersionDiff <= 0 ) {
					$this->__logMessage('Executing update ' . $path);

					if ($this->__executeUpdate($path)) {
						$this->__writeDbVersion($this->__stringToVersion($updateVersion));
						$currentDbVersion = $updateVersion;

						$this->__logMessage('Updated to version: ' . $updateVersion);
					} else {
						$this->__logMessage('Error: Failed to execute update ' . $path);
					}
				}
			}

			// There will be some sql files that need to be ran even during an update
			$sqlFiles = array(
				'mailinglists',
				'mailinglist_subscriptions',
			);

			$conn = $this->__getDbConnection('default', true);

			foreach ($sqlFiles as $filename) {
				$schemaFiles = $this->__getSqlFilesContaining($filename . '_schema');

				// Kill the databases that are in thiese files
				foreach ($schemaFiles as $file) {
					$tableName = $this->__getTableNameFromSchemaFile($file);
					if ($tableName == null) {
						$this->__logMessage("Error: Unable to parse table name from file: $file");
					}
					$this->__logMessage("Dropping table `$tableName`");
					$this->__runQuery($conn, "DROP TABLE `$tableName`");

					$this->__runQueryFromFile($conn, $file);
				}

				// And add the data
				$dataFiles = $this->__getSqlFilesContaining($filename . '_data');
				foreach ($dataFiles as $file) {
					$this->__runQueryFromFile($conn, $file);
				}
			}

			$this->__popLogIndent();
		}
	}

/** 
 * Given the path to an sql file that creats a table, get the name of the table
 *
 * @param string $path The path to the file to read.
 * @return mixed The name of the table if read successfully, null otherwise.
 */
	private function __getTableNameFromSchemaFile($path) {
		$contents = file_get_contents($path);

		$matches;
		if (preg_match("/CREATE TABLE(.+)`(.+)`/", $contents, $matches)) {
			return $matches[2];
		}

		return null;
	}

/** 
 * Attempt to execute the contents of an update file.
 *
 * @param string $path The path to the update file.
 * @return bool True if execution was successful, false otherwise.
 */
	private function __executeUpdate($path) {
		if (file_exists($path)) {
			ob_start();
			$this->__pushLogIndent();
			include ($path);
			$this->__popLogIndent();
			return true;
		}
		return false;
	}

/** 
 * Run all selected setup steps.
 */
	public function run() {
		$this->__logMessage("Started");

		$this->__setupConfigFiles();
		if (!$this->__generateData()) {
			$this->__logMessage('Failed to generate and write data.');
			exit(1);
		}

		$this->__createDatabases();
		$this->__runDatabaseUpdate();
		$this->__copyKrbLibFile();
		$this->__copyMcapiLibFile();
		$this->__setupTempFolders();

		$this->__logMessage("Finished");
	}
}