<?php

	ini_set('max_execution_time', 600); // Give us 10 mins to execute, we might be doing a lot

	require_once('utils.php');
	require_once('data_generator.php');

	//! Setup is a class that is responsible for parsing options and then performing certain steps based on those options.
	class Setup
	{
		private $logIndet = 0;						//!< Indent level for the log
		private $settings = array();				//!< Array of settings data

		//! Options
		private $createDb = false;					//!< If true create the instrumentation and instrumentation_test databases and populate them with tables and data.
		private $useRealKrb = false;				//!< If true then use the real KRB Auth lib, otherwise use a dummy one.
		private $setupTempFolders = false;			//!< If true create the temporary folders CakePHP needs.
		private $environmentType = 'production';	//!< Prefer files with this suffix.

		//! The following details are used to create a HMS login for the user with admin rights.
		private $firstname = '';					//!< Firstname of the user.
		private $surname = '';						//!< Surname of the user.
		private $username = '';						//!< Username of the user.
		private $email = '';						//!< Email of the user.


		//! Constructor
		public function __construct()
		{
			if(!$this->_validateOptions())
			{
				$this->_logMessage('Invalid arguments.');
				exit(1);
			}
			$this->settings = $this->_getSettings();
		}


		//! Increase the log indent level
		private function _pushLogIndent()
		{
			$this->logIndet++;
		}

		//! Decrease the log indent level
		private function _popLogIndent()
		{
			$this->logIndet--;
			if($this->logIndet < 0)
			{
				$this->logIndet = 0;
				$this->_logMessage('Warning: Log indent was popped too many times.');
			}
		}

		//! Set up the database options.
		/*
			@param bool $createDb If true then the database will be created..
		*/
		public function setCreateDatabase($createDb)
		{
			$this->createDb = $createDb;
		}

		//! Set if we should use the real KRB lib or not.
		/*!
			@param bool $userRealKrb If true then the real KRB code will be copied in to the lib folder, if false, a dummy file will be copied.
		*/
		public function setUseRealKrb($useRealKrb)
		{
			$this->useRealKrb = $useRealKrb;
		}

		//! Set if we should use development or production configs, settings and databases.
		/*!
			@param bool $useDevelopmentEnv If true then use development configs, settings and databases.
		*/
		public function setUseDevelopmentEnvironment($useDevelopmentEnv)
		{
			$this->environmentType = $useDevelopmentEnv ? 'development' : 'production';
		}

		//! Set if we should create the temporary folders.
		/*!
			@param bool $setupTempFolders If true then temporary folders will be created.
		*/
		public function setSetupTempFolders($setupTempFolders)
		{
			$this->setupTempFolders = $setupTempFolders;
		}

		//! Set up the user info.
		/*
			@param string $firstname First name of the user.
			@param string $surname Surname of the user.
			@param string $username Username of the user.
			@param string $email Users e-mail.
		*/
		public function setUserInfo($firstname, $surname, $username, $email)
		{
			$this->firstname = $firstname;
			$this->surname = $surname;
			$this->username = $username;
			$this->email = $email;
		}

		//! Replace variables in a template using the options in fields.
		/*!
			@param string $template The template to work with.
			@param array $fields Key-value pair of names and the values to substitute.
			@retval string The template with values replaced.
		*/
		private function _replaceFields($template, $fields) 
		{
			foreach ($fields as $name => $value) 
			{
				$template = str_replace('%%' . $name . '%%', $value, $template);
			}
			return $template;
		}

		//! Create the config files for the current environment, delete any no-longer needed config files.
		/*!
			@param array $settings The settings to use.
		*/
		private function _setupConfigFiles()
		{
			$this->_logMessage('Setting up config files');
			$this->_pushLogIndent();

			$configPath = '../../../app/Config/';

			$settings = $this->settings;

			// ... Not that I'm OCD or anything
			asort($settings);

			// Create each of the config files
			foreach ($settings as $settingName => $settingData) 
			{
				// First check for dev/production templates
				$templateFilePath = makeAbsolutePath("$settingName.{$this->environmentType}.template");
				if (!file_exists($templateFilePath))
				{
					// Fall back to the regular template
					$templateFilePath = makeAbsolutePath("$settingName.template");

					if(!file_exists($templateFilePath))
					{
						$fullFilePath = makeAbsolutePath("$configPath$settingName.php");
						if(file_exists($fullFilePath))
						{
							$this->_logMessage("Deleting settings file at $fullFilePath because we couldn't find a matching template");
							unlink($fullFilePath);
						}
						continue;
					}
				}

				$currentContents = file_get_contents($templateFilePath);
				$newContents = $this->_replaceFields($currentContents, $settingData);

				if (file_put_contents("$configPath$settingName.php", $newContents) !== FALSE) 
				{
					$this->_logMessage("Created $settingName.php from template $templateFilePath");
				}
				else 
				{
					$this->_logMessage("Failed to create $settingName.php");
				}
			}

			$this->_popLogIndent();
		}

		//! Get an array of all sql files in the ./sql folder that are valid for the current configuration and contain a certain string.
		/*!
			@param string $substr Files returned must contain this string.
			@retval array A list of filenames for sql files that match the $subStr and environment criteria.
		*/
		private function _getSqlFilesContaining($subStr)
		{
			$validFiles = array();
			$dirList = scandir(makeAbsolutePath('./sql'));

			foreach ($dirList as $filename) 
			{
				if( pathinfo($filename, PATHINFO_EXTENSION) == 'sql' &&
					strpos($filename, $subStr) !== FALSE )
				{
					// Check if it passes the environment type test.
					$parts = explode('.', basename($filename));
					if(count($parts) > 1)
					{
						// Is this file for all environment types or the one we're currently using?
						if(	$parts[1] == 'sql' || 
							$parts[1] == $this->environmentType)
						{
							array_push($validFiles, makeAbsolutePath('./sql/' . $filename));
						}
					}
				}
			}

			return $validFiles;
		}

		//! Run a query on a database object, returning the result
		/*!
			@param mysqli $databaseObj A database object to run the query on.
			@param string $query The query to run.
			@retval mixed An array of results if query is successful, or null on error.
		*/
		private function _runQuery($databaseObj, $query)
		{
			$results = array();
			if ($databaseObj->multi_query($query))
			{
				do
				{
					if($result = $databaseObj->store_result())
					{
						array_push($results, $result->fetch_all(MYSQLI_ASSOC));	
						$result->free();
					}

					if(!$databaseObj->more_results())
					{
						break;
					}
					$databaseObj->next_result();
				} while(true);

				// If we only ran one query and got one result
				// just return that
				if(count($results) == 1)
				{
					return $results[0];
				}

				return $results;
			}
			else
			{
				if($databaseObj->error) 
				{
					$this->_logMessage(sprintf('Error: Query - %s failed with message - %s', $query, $databaseObj->error));
				}
			}

			return null;
		}

		//! Run a query from a file.
		/*!
			@param mysqli $databaseObj A database object to run the query on.
			@param string $filepath Path to the file containing the query to run.
			@retval mixed An array of results if query is successful, or null on error.
		*/
		private function _runQueryFromFile($databaseObj, $filepath)
		{
			$this->_logMessage("Executing SQL in: $filepath");
			return $this->_runQuery($databaseObj, file_get_contents($filepath));
		}

		//! Given a list of files containing sql queries, run all the queries.
		/*!
			@param mysqli $databaseObj A database object to run the queries on.
			@param string $fileList The list of files.
			@retval mixed An array of results of the queries that succeded.
		*/
		private function _runAllQueriesInFileList($databaseObj, $fileList)
		{
			$results = array();
			foreach ($fileList as $file) 
			{
				$queryResult = $this->_runQueryFromFile($databaseObj, $file);
				if(is_array($queryResult))
				{
					array_merge($queryResult);
				}
			}

			return $results;
		}

		//! Run all queries marked as 'schema'.
		/*!
			@param mysqli $databaseObj A database object to run the queries on.
			@retval mixed An array of results of the queries that succeded.
		*/
		private function _runSchemaQueries($databaseObj)
		{
			$files = $this->_getSqlFilesContaining('schema');
			return $this->_runAllQueriesInFileList($databaseObj, $files);
		}

		//! Run all queries marked as 'data'.
		/*!
			@param mysqli $databaseObj A database object to run the queries on.
			@retval mixed An array of results of the queries that succeded.
		*/
		private function _runDataQueries($databaseObj)
		{
			$files = $this->_getSqlFilesContaining('data');
			return $this->_runAllQueriesInFileList($databaseObj, $files);
		}

		//! Divide $membersRemaining by $divisor, return the result and adjust $membersRemaining.
		/*!
			@param ref int $membersRemaining The number of members left to distribute.
			@param int $divisor The portion of members that should be used.
			@retval int The number of members used.
		*/
		private function _distribureMembers(&$membersRemaining, $divisor)
		{
			$numUsed = (int)($membersRemaining / $divisor);
			$membersRemaining -= $numUsed;
			return $numUsed;
		}

		//! Write $data to the file at $path, overwriting the file if it exists.
		/*!
			@param string $path The path to the file.
			@param string $data The data to write.
			@retval bool True if data was written successfully, false otherwise.
		*/
		private function _writeToFile($path, $data)
		{
			$handle = fopen(makeAbsolutePath($path), 'w');
			if($handle)
			{
				$result = fwrite($handle, $data);
				fclose($handle);

				return $result !== FALSE;
			}

			return false;
		}

		//! Generate the data that will populate the database.
		/*!
			@retval bool True if data was generated and written correctly, false otherwise.
		*/
		private function _generateData()
		{
			$this->_logMessage('Generating test data');
			$this->_pushLogIndent();

			$totalMembersToGenerate = 200;
			$membersRemaining = $totalMembersToGenerate;

			$genDetails = array();

			// Half the members should be current members
			$genDetails[Status::CURRENT_MEMBER] = $this->_distribureMembers($membersRemaining, 2);
			
			// 1/3rd of the remaining members should be ex members
			$genDetails[Status::EX_MEMBER] = $this->_distribureMembers($membersRemaining, 3);

			// Distribute the rest of the members evenly over the other statuses
			$toAssign = (int)floor($membersRemaining / 4);

			$genDetails[Status::PROSPECTIVE_MEMBER] = $toAssign;
			$genDetails[Status::PRE_MEMBER_1] = $toAssign;
			$genDetails[Status::PRE_MEMBER_2] = $toAssign;
			$genDetails[Status::PRE_MEMBER_3] = $toAssign;

			$membersRemaining -= ($toAssign * 4);


			// Any left? Make them current members
			$genDetails[Status::CURRENT_MEMBER] += $membersRemaining;

			$this->_logMessage("Generating data for $totalMembersToGenerate members.");

			// Generate!
			$gen = new DataGenerator();
			foreach ($genDetails as $status => $num) 
			{
				for($i = 0; $i < $num; $i++)
				{
					$gen->generateMember($status);
				}	
			}

			// Finally generate the dev user
			$details = array(
				'firstname' => $this->firstname,
				'surname' => $this->surname,
				'email' => $this->email,
				'username' => $this->username,
				'groups' => array(
					Group::CURRENT_MEMBERS, Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN,
				),
			);
			$gen->generateMember(Status::CURRENT_MEMBER, $details);

			$this->_logMessage('Writing SQL files');

			$pathsAndFunctions = array(
				'./sql/members_data.sql' => function() use (&$gen) { return $gen->getMembersSql(); },
				'./sql/member_group_data.sql' => function() use (&$gen) { return $gen->getMembersGroupSql(); },
				'./sql/account_data.sql' => function() use (&$gen) { return $gen->getAccountsSql(); },
				'./sql/pins_data.sql' => function() use (&$gen) { return $gen->getPinsSql(); },
				'./sql/rfid_tags_data.sql' => function() use (&$gen) { return $gen->getRfidTagsSql(); },
				'./sql/status_updates_data.sql' => function() use (&$gen) { return $gen->getStatusUpdatesSql(); },
				'./sql/mailinglists_data.development.sql' => function() use (&$gen) { return $gen->getMailingListsSql(); },
				'./sql/mailinglist_subscriptions_data.development.sql' => function() use (&$gen) { return $gen->getMailingListSubscriptionsSql(); },
				'./sql/hms_emails_data.development.sql' => function() use (&$gen) { return $gen->getEmailRecordSql(); },
			);

			foreach ($pathsAndFunctions as $path => $func) 
			{
				if($this->_writeToFile($path, $func()))
				{
					$this->_logMessage("Wrote $path");
				}
				else
				{
					$this->_logMessage("Failed to write $path");
					$this->_popLogIndent();
					return false;
				}
			}

			$this->_popLogIndent();
			return true;
		}

		//! Get a mysqli database connection.
		/*!
			@param string $connName The name of the connection to get (e.g. default/test)
			@param bool $select If true then the database will be selected.
			@retval mixed A mysql connection on success, or null on failure.
		*/
		private function _getDbConnection($connName, $select)
		{
			$hostName = $connName . '_host';
			$login = $connName . '_login';
			$password = $connName . '_password';
			$database = $connName . '_database';

			$settings = $this->settings['database'];

			if( array_key_exists($hostName, $settings) && 
				array_key_exists($login, $settings) &&
				array_key_exists($password, $settings) &&
				array_key_exists($database, $settings) )
			{
				$conn = new mysqli($settings[$hostName], $settings[$login], $settings[$password]);
				if($select)
				{
					$conn->select_db($settings[$database]);
				}
				return $conn;
			}

			return null;
		}

		//! Set-up a database.
		/*!
			@param string $prefix The prefix of the database to create.
			@param bool $populate If true then run schema and data queries on the newly created database.
		*/
		private function _setupDatabase($prefix, $populate)
		{
			$conn = $this->_getDbConnection($prefix, false);

			if ($conn->connect_error) 
			{
				$this->_logMessage("Couldn't connect to main database");
			}
			else 
			{
				$dbName = $this->settings['database'][$prefix . '_database'];
				if(!$conn->query("DROP DATABASE " . $dbName))
				{
					$this->_logMessage("Failed to drop database: $dbName");
				}
				if(!$conn->query("CREATE DATABASE " . $dbName))
				{
					$this->_logMessage("Failed to drop database: $dbName");
				}
				else
				{
					$this->_logMessage("Created database: $dbName");
				}

				if( $populate &&
					$conn->select_db($dbName))
				{
					$this->_runSchemaQueries($conn);
					$this->_runDataQueries($conn);
				}
				else
				{
					$this->_logMessage("Unable to select database: $dbName");
				}
			}
			$conn->close();
		}

		//! Create and/or populate the databases for HMS
		/*!
			@param array $settings The settings to use.
		*/
		private function _createDatabases()
		{
			if ($this->createDb) 
			{
				$this->_logMessage('Creating and/or populating databases');
				$this->_pushLogIndent();

				$this->_setupDatabase('default', true);
				$this->_setupDatabase('test', false);

				// If we've just created the data in the database then we're already on
				// the latest database version
				$this->_writeDbVersion($this->_getCodeVersion());

				$this->_popLogIndent();
			}
		}

		//! Copy a file from one location to another, creating the folder if needed.
		/*!
			@param string $fromFile The path to copy the file from.
			@param string $toFile The path to copy the file to.
		*/
		private function _copyLibFile($fromFile, $toFile)
		{
			$libFolder = dirname($toFile);
			if(!file_exists($libFolder))
			{
				if(mkdir($libFolder))
				{
					$this->_logMessage("Created folder at: $libFolder");
				}
				else
				{
					$this->_logMessage("Failed to create folder at: $libFolder");
				}
			}

			$this->_logMessage("Attempting to copy $fromFile to $toFile");

			if(copy(makeAbsolutePath($fromFile), $toFile))
			{
				$this->_logMessage('Copy successful');
			}
			else
			{
				$this->_logMessage('Copy failed');
			}
		}

		//! Copy either the real or dummy KRB Auth lib file to the lib folder.
		private function _copyKrbLibFile()
		{
			$this->_logMessage('Copying KRB lib file');
			$this->_pushLogIndent();

			$fromFile = $this->useRealKrb ? 'krb5_auth.real' : 'krb5_auth.dummy';
			$this->_copyLibFile($fromFile, '../../../app/Lib/Krb/krb5_auth.php');

			$this->_popLogIndent();
		}

		//! Copy either the production or development MCAPI file.
		private function _copyMcapiLibFile()
		{
			$this->_logMessage('Copying MCAPI lib file');
			$this->_pushLogIndent();

			$fromFile = "MCAPI.{$this->environmentType}";
			$this->_copyLibFile($fromFile, '../../../app/Lib/MailChimp/MCAPI.php');

			$this->_popLogIndent();
		}


		//! Given a path to a directory, delete the directory and all it's contents.
		/*!
			@param string $dirPath The path to the directory to delete.
		*/
		private function _deleteDir($dirPath) 
		{
		    if (!is_dir($dirPath)) 
		    {
		        throw new InvalidArgumentException("$dirPath must be a directory");
		    }

		    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') 
		    {
		        $dirPath .= '/';
		    }

		    $files = glob($dirPath . '*', GLOB_MARK);
		    foreach ($files as $file) 
		    {
		        if (is_dir($file)) 
		        {
		            $this->_deleteDir($file);
		        } 
		        else
		        {
		            unlink($file);
		        }
		    }
		    rmdir($dirPath);
		}

		//! Create the temporary folders for HMS, will delete the old ones if they exist.
		private function _setupTempFolders()
		{
			if($this->setupTempFolders)
			{
				$this->_logMessage('Setting up temp folders');
				$this->_pushLogIndent();

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

				foreach ($foldersToMake as $folder) 
				{
					if(file_exists($folder))
					{
						$this->_logMessage("Folder $folder already exists, deleting...");
						$this->_deleteDir($folder);
					}
					if(mkdir($folder, 0777, true))
					{
						$this->_logMessage("Created folder: $folder");
					}
					else
					{
						$this->_logMessage("Failed to create folder: $folder");
					}
				}

				$this->_popLogIndent();
			}
		}

		//! Check to see if we're running on the command line
		/*
			@ratval bool True if we're running on the command line, false otherwise.
		*/
		private function _isOnCommandline()
		{
			// Note: Like most things in PHP this function isn't reliable:
			// http://php.net/manual/en/function.php-sapi-name.php
			$sapiType = php_sapi_name();

			return ($sapiType == 'cli');
		}

		//! Format and write a log message, prepends timestamp and appends a newline.
		/*!
			@param string $message The message to write.
		*/
		private function _logMessage($message)
		{
			$timestamp = date("H:i:s");
			if($this->_isOnCommandline())
			{
				echo sprintf("[%s]%s%s%s", $timestamp, str_repeat("    ", $this->logIndet), $message, PHP_EOL);	
			}
			else
			{
				echo '<span class="logLine">';
				echo "[$timestamp] ";
				echo str_repeat('<span class="logSpacer"> </span>', $this->logIndet + 1);
				echo $message;
				echo '</span>';

				ob_flush();
    			flush();
			}
		}

		//! Check if the options used are valid.
		/*!
			@retval bool True if options are valid, false otherwise.
		*/
		private function _validateOptions()
		{
			// Certain variables are required
			if($this->createDb)
			{
				if(! (isset($this->firstname) && isset($this->surname) && isset($this->username) && isset($this->email)) )
				{
					return false;
				}
			}

			return true;
		}

		//! Get the settings to use, either default setting or those loaded in from a file.
		private function _getSettings()
		{
			// Default settings
			$defaultSettings = array(
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
				'debug' => array(
				),
				'krb' =>	array(

				),
				'mailchimp'	=> array(
					'key'	=>	'w1zg905ych1e090og9pvjb7td6b05vlg-2y8',
					'list'	=>	'us8gz1v8rq',
				),
				'email'	=>	array(
					'from_address'	=>	'site@localhost',
					'host'			=>	'localhost',
					'port'			=>	25,
					'username'		=>	'user',
					'password'		=>	'hunter2',
				),
			);

			$overrideSettings = 'hms.settings';
			if(file_exists(makeAbsolutePath($overrideSettings)))
			{
				include($overrideSettings);
			}

			// Merge the default and override settings, preferring the override ones	
			$finalSettings = array();
			foreach ($defaultSettings as $name => $values)
			{
				if(array_key_exists($name, $aSettings))
				{
					$finalSettings[$name] = $aSettings[$name];
				}
				else
				{
					$finalSettings[$name] = $defaultSettings[$name];
				}
			}

			return $finalSettings;
		}

		//! Given an array of version information, check if it is valid.
		/*!
			@param array $version An array of version data.
			@retval bool True if version is valid, false otherwise.
		*/
		private function _isValidVersion($version)
		{
			return 
				array_key_exists('major', $version) &&
				array_key_exists('minor', $version) &&
				array_key_exists('build', $version);
		}

		//! Read the current version from the code.
		/*
			@retval mixed Array of the code version number, or null on error.
		*/
		private function _getCodeVersion()
		{
			$versionFilePath = '../../../app/Controller/AppController.php';

			$version = array();
			$lines = explode(';', file_get_contents(makeAbsolutePath($versionFilePath)));

			$versionIds = array(
				"VERSION_MAJOR" => 'major',
				"VERSION_MINOR" => 'minor',
				"VERSION_BUILD" => 'build',
			);

			foreach ($lines as $line) 
			{
				foreach ($versionIds as $codeId => $arrayIdx) 
				{
					$matches = array();
					$regex = $codeId . " = (\d+?)";
					if(preg_match("/$regex/", trim($line), $matches))
					{
						$version[$arrayIdx] = $matches[1];
					}
				}
			}

			if($this->_isValidVersion($version))
			{
				return $version;
			}
			return null;
		}

		//! Write a version to the database version file.
		/*!
			@param string $version The version to write.
		*/
		private function _writeDbVersion($version)
		{
			$versionStr = $this->_versionToString($version);
			$this->_logMessage("Writing db version $versionStr to database");

			$conn = $this->_getDbConnection('default', true);
			$this->_runQueryFromFile($conn, makeAbsolutePath('sql/hms_meta_schema.sql'));
			$this->_runQuery($conn, "INSERT INTO `hms_meta` (`name`, `value`) VALUES ('db_version', '$versionStr') ON DUPLICATE KEY UPDATE value='$versionStr'");
		}

		//! Attempt to read the database version from the database.version file
		/*!
			@retval mixed Array of version data if successful, null on error.
		*/
		private function _readDbVersion()
		{
			$conn = $this->_getDbConnection('default', true);
			$result = $this->_runQuery($conn, "SELECT `value` FROM `hms_meta` WHERE `name`='db_version'");
			if(is_array($result) && count($result) > 0)
			{
				$key = 'value';
				$data = $result[0];

				if(array_key_exists($key, $data))
				{
					return $this->_stringToVersion($data[$key]);
				}
			}

			return null;
		}

		//! Get the difference between two versions.
		/*!
			@param mixed $versionA An array of version data, or a string that can be converted into an array.
			@param mixed $versionB An array of version data, or a string that can be converted into an array.
			@retval mixed An integer representing the difference between the versions, or null on error.
		*/
		private function _compareVersions($versionA, $versionB)
		{
			if(is_string($versionA))
			{
				$versionA = $this->_stringToVersion($versionA);
			}

			if(is_string($versionB))
			{
				$versionB = $this->_stringToVersion($versionB);
			}

			if($versionA == null || $versionB == null)
			{
				return null;
			}

			$majorDiff = $versionA['major'] - $versionB['major'];
			$minorDiff = $versionA['minor'] - $versionB['minor'];
			$buildDiff = $versionA['build'] - $versionB['build'];

			if($majorDiff != 0)
			{
				return $majorDiff;
			}

			if($minorDiff != 0)
			{
				return $minorDiff;
			}

			if($buildDiff != 0)
			{
				return $buildDiff;
			}

			return 0;
		}

		//! Given an array of version data, return a string representation of that version.
		/*!
			@param array $version An array of version data.
			@retval string A string representing the version data.
		*/
		private function _versionToString($version)
		{
			return sprintf('%s.%s.%s', $version['major'], $version['minor'], $version['build']);
		}

		//! Given a string representation of a version, return an array of version data.
		/*!
			@param string $versionStr A string representing a version.
			@retval array An array of version data.
		*/
		private function _stringToVersion($versionStr)
		{
			list($major, $minor, $build) = explode('.', $versionStr);
			return compact('major', 'minor', 'build');
		}

		//! Update the database to the current version.
		private function _runDatabaseUpdate()
		{
			if(!$this->createDb)
			{
				$this->_logMessage('Updating database');
				$this->_pushLogIndent();

				// Find out what version we're updating from
				$currentDbVersion = $this->_readDbVersion();
				if($currentDbVersion == null)
				{
					$this->_logMessage('Error: Could not read current database version');
					return;
				}
				$this->_logMessage('DB Version: ' . $this->_versionToString($currentDbVersion));

				// Find out which version we should be updating to
				$codeVersion = $this->_getCodeVersion();
				if($codeVersion == null)
				{
					$this->_logMessage('Error: Could not get code version');
					return;
				}
				$this->_logMessage('Code Version: ' . $this->_versionToString($codeVersion));

				if( $this->_compareVersions($currentDbVersion, $codeVersion) == 0 )
				{
					$this->_logMessage('No update required');
					$this->_popLogIndent();
					return;
				}

				$this->_logMessage(sprintf('Updating from version: %s to version %s', 
					$this->_versionToString($currentDbVersion), $this->_versionToString($codeVersion)));

				// Ok, lets get started.
				// First we find all the update files, and sort them by version
				$updatesPath = makeAbsolutePath('updates');
				$updateFiles = array();
				$files = glob($updatesPath . '/*.php');
				foreach ($files as $file) 
				{
					if(is_file($file))
					{
						$fileParts = pathinfo($file);
						$fileVersion = $this->_stringToVersion($fileParts['filename']);
						if(!$this->_isValidVersion($fileVersion))
						{
							$this->_logMessage("Warning: Found update php with a filename that is not a valid version $file");
							continue;
						}

						$updateFiles[$this->_versionToString($fileVersion)] = $file;
					}
				}

				uksort($updateFiles, array($this, "_compareVersions"));

				// Then execute the version file for any version that's ahead of us
				// until we hit the code version
				foreach ($updateFiles as $updateVersion => $path) 
				{
					$currentVersionDiff = $this->_compareVersions($updateVersion, $currentDbVersion);
					$codeVersionDiff = $this->_compareVersions($updateVersion, $codeVersion);

					if(	$currentVersionDiff > 0 &&
						$codeVersionDiff <= 0 )
					{
						$this->_logMessage('Executing update ' . $path);

						if($this->_executeUpdate($path))
						{
							$this->_writeDbVersion($this->_stringToVersion($updateVersion));
							$currentDbVersion = $updateVersion;

							$this->_logMessage('Updated to version: ' . $updateVersion);
						}
						else
						{
							$this->_logMessage('Error: Failed to execute update ' . $path);
						}
					}
				}

				// There will be some sql files that need to be ran even during an update
				$sqlFiles = array(
					'mailinglists',
					'mailinglist_subscriptions',
				);

				$conn = $this->_getDbConnection('default', true);

				foreach ($sqlFiles as $filename) 
				{
					$schemaFiles = $this->_getSqlFilesContaining($filename . '_schema');

					// Kill the databases that are in thiese files
					foreach ($schemaFiles as $file) 
					{
						$tableName = $this->_getTableNameFromSchemaFile($file);
						if($tableName == null)
						{
							$this->_logMessage("Error: Unable to parse table name from file: $file");
						}
						$this->_logMessage("Dropping table `$tableName`");
						$this->_runQuery($conn, "DROP TABLE `$tableName`");

						$this->_runQueryFromFile($conn, $file);
					}

					// And add the data
					$dataFiles = $this->_getSqlFilesContaining($filename . '_data');
					foreach ($dataFiles as $file) 
					{
						$this->_runQueryFromFile($conn, $file);
					}
				}

				$this->_popLogIndent();
			}
		}

		//! Given the path to an sql file that creats a table, get the name of the table
		/*!
			@param string $path The path to the file to read.
			@retval mixed The name of the table if read successfully, null otherwise.
		*/
		private function _getTableNameFromSchemaFile($path)
		{
			$contents = file_get_contents($path);

			$matches;
			if(preg_match("/CREATE TABLE(.+)`(.+)`/", $contents, $matches))
			{
				return $matches[2];
			}

			return null;
		}

		//! Attempt to execute the contents of an update file.
		/*
			@param string $path The path to the update file.
			@retval bool True if execution was successful, false otherwise.
		*/
		private function _executeUpdate($path)
		{
			if(file_exists($path))
			{
				ob_start();
				$this->_pushLogIndent();
				include($path);
				$this->_popLogIndent();
				return true;
			}
			return false;
		}

		//! Run all selected setup steps.
		public function run()
		{
			$this->_logMessage("Started");

			$this->_setupConfigFiles();
			if(!$this->_generateData())
			{
				$this->_logMessage('Failed to generate and write data.');
				exit(1);
			}
 			$this->_createDatabases();
			$this->_runDatabaseUpdate();
			$this->_copyKrbLibFile();
			$this->_copyMcapiLibFile();
			$this->_setupTempFolders();


			$this->_logMessage("Finished");
		}
	}

?>
