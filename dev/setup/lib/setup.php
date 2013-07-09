<?php

	require_once('utils.php');
	require_once('data_generator.php');

	//! Setup is a class that is responsible for parsing options and then performing certain steps based on those options.
	class Setup
	{
		private $logIndet = 0;			//!< Indent level for the log

		//! Options
		private $createDb = false;					//!< If true create the instrumentation and instrumentation_test databases.
		private $populateDb = false;				//!< If true populate the databases with default tables and data.
		private $useRealKrb = false;				//!< If true then use the real KRB Auth lib, otherwise use a dummy one.
		private $setupTempFolders = false;			//!< If true create the temporary folders CakePHP needs.
		private $environmentType = 'production';	//!< Prefer files with this suffix.

		//! The following details are used to create a HMS login for the user with admin rights.
		private $firstname = '';			//!< Firstname of the user.
		private $surname = '';				//!< Surname of the user.
		private $username = '';				//!< Username of the user.
		private $email = '';				//!< Email of the user.


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
			@param bool $createDb If true then the database will be created.
			@param bool $populateDb If true then database will be populated.
		*/
		public function setDatabaseOptions($createDb, $populateDb)
		{
			$this->createDb = $createDb;
			$this->populateDb = $populateDb;
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
		private function _setupConfigFiles($settings)
		{
			$this->_logMessage('Setting up config files');
			$this->_pushLogIndent();

			$configPath = '../../../app/Config/';

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
						$this->_logMessage("Deleting settings file at $fullFilePath because we couldn't find a matching template");
						unlink($fullFilePath);
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

		private function _getSqlFilesContaining($name)
		{
			$validFiles = array();
			$dirList = scandir(makeAbsolutePath('./sql'));

			foreach ($dirList as $filename) 
			{
				if( pathinfo($filename, PATHINFO_EXTENSION) == 'sql' &&
					strpos($filename, $name) !== FALSE )
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

		private function _runQueryFromFile($databaseObj, $filepath)
		{
			$this->_logMessage("Executing SQL in: $filepath");
			if ($databaseObj->multi_query(file_get_contents($filepath)))
			{
				$databaseObj->store_result();
				while ($databaseObj->more_results()) 
				{
					$databaseObj->next_result();
					$databaseObj->store_result();
				}
			}
		}

		private function _runAllQueriesInFileList($databaseObj, $fileList)
		{
			foreach ($fileList as $file) 
			{
				$this->_runQueryFromFile($databaseObj, $file);
			}
		}

		private function _runSchemaQueries($databaseObj)
		{
			$files = $this->_getSqlFilesContaining('schema');
			$this->_runAllQueriesInFileList($databaseObj, $files);
		}

		private function _runDataQueries($databaseObj)
		{
			$files = $this->_getSqlFilesContaining('data');
			$this->_runAllQueriesInFileList($databaseObj, $files);
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
					return false;
				}
			}

			return true;
		}

		//! Create and/or populate the databases for HMS
		/*!
			@param array $settings The settings to use.
		*/
		private function _createDatabases($settings)
		{
			if ($this->createDb || $this->populateDb) 
			{
				$this->_logMessage('Creating and/or populating databases');
				$this->_pushLogIndent();

				// Main Database
				$oDB = new mysqli($settings['database']['default_host'], $settings['database']['default_login'], $settings['database']['default_password']);

				if ($oDB->connect_error) 
				{
					$this->_logMessage("Couldn't connect to main database");
				}
				else 
				{
					$defaultDbName = $settings['database']['default_database'];
					if($this->createDb || $this->populateDb)
					{
						if(!$oDB->query("DROP DATABASE " . $defaultDbName))
						{
							$this->_logMessage("Failed to drop database: $defaultDbName");
						}
						if(!$oDB->query("CREATE DATABASE " . $defaultDbName))
						{
							$this->_logMessage("Failed to drop database: $defaultDbName");
						}
						else
						{
							$this->_logMessage("Created database: $defaultDbName");
						}
					}

					if($oDB->select_db($defaultDbName))
					{
						if($this->populateDb)
						{
							$this->_runSchemaQueries($oDB);
							$this->_runDataQueries($oDB);
						}
					}
					else
					{
						$this->_logMessage("Unable to select database: $defaultDbName");
					}
				}
				$oDB->close();

				// Test Database
				$oDB = new mysqli($settings['database']['test_host'], $settings['database']['test_login'], $settings['database']['test_password']);

				if ($oDB->connect_error) 
				{
					$this->_logMessage("Couldn't connect to test database");
				}
				else 
				{
					$testDbName = $settings['database']['test_database'];
					if($this->createDb || $this->populateDb)
					{
						if(!$oDB->query("DROP DATABASE " . $testDbName))
						{
							$this->_logMessage("Failed to drop database: $testDbName");
						}
						if(!$oDB->query("CREATE DATABASE " . $testDbName))
						{
							$this->_logMessage("Failed to drop database: $testDbName");
						}
						else
						{
							$this->_logMessage("Created database: $testDbName");
						}
					}

					
				}
				$oDB->close();

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
			if($this->populateDb)
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
			$path = makeAbsolutePath('database.version');
			$versionStr = $this->_versionToString($version);
			$this->_logMessage("Writing db version $versionStr to $path");
			file_put_contents($path, $versionStr);
		}

		//! Attempt to read the database version from the database.version file
		/*!
			@retval mixed Array of version data if successful, null on error.
		*/
		private function _readDbVersion()
		{
			$path = makeAbsolutePath('database.version');
			$trimmedContents = trim(file_get_contents($path));
			$version = $this->_stringToVersion($trimmedContents);
			if($this->_isValidVersion($version))
			{
				return $version;
			}
			return null;
		}

		//! Get the difference between two versions.
		/*!
			@param array $versionA An array of version data.
			@param array $versionB An array of version data.
			@retval mixed An integer representing the difference between the versions, or null on error.
		*/
		private function _compareVersions($versionA, $versionB)
		{
			$numA = $this->_versionToNumber($versionA);
			$numB = $this->_versionToNumber($versionB);

			if($numA == null || $numB == null)
			{
				return null;
			}

			return $numA - $numB;
		}

		//! Given an array of version data, get a number uniquely representing that version.
		/*!
			@param array $version An array of version data.
			@retval mixed A number representing $version on success, or null on error.
		*/
		private function _versionToNumber($version)
		{
			if($this->_isValidVersion($version))
			{
				$number = 0;
				$multiplier = 1;

				$versionParts = array($version['build'], $version['minor'], $version['major']);

				foreach ($versionParts as $part) 
				{
					$number += ((int)$part) * $multiplier;
					$multiplier *= 10;
				}

				return $number;
			}
			return null;
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
			$this->_logMessage('Updating database');
			$this->_pushLogIndent();

			// Find out what version we're updating from
			$currentDbVersion = $this->_readDbVersion();
			if($currentDbVersion == null)
			{
				$this->_logMessage('Error: Could not read current database version');
				return;
			}

			// Find out which version we should be updating to
			$codeVersion = $this->_getCodeVersion();
			if($codeVersion == null)
			{
				$this->_logMessage('Error: Could not get code version');
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

					$versionNumber = $this->_versionToNumber($fileVersion);
					$updateFiles[$versionNumber] = array(
						'path' => $file,
						'version' => $fileVersion,
					);
				}
			}

			ksort($updateFiles);

			// Then execute the version file for any version that's ahead of us
			// until we hit the code version
			$currentVersionNumber = $this->_versionToNumber($currentDbVersion);
			$codeVersionNumber = $this->_versionToNumber($codeVersion);

			foreach ($updateFiles as $versionNumber => $data) 
			{
				if(	$versionNumber > $currentVersionNumber &&
					$versionNumber <= $codeVersionNumber )
				{
					$this->_logMessage('Executing update ' . $data['path']);

					if($this->_executeUpdate($data['path']))
					{
						$this->_writeDbVersion($data['version']);
						$currentVersionNumber = $versionNumber;

						$this->_logMessage('Updated to version: ' . $this->_versionToString($data['version']));
					}
					else
					{
						$this->_logMessage('Error: Failed to execute update ' . $data['path']);
					}
				}
			}
			$this->_popLogIndent();
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
				include($path);
				$messages = ob_get_clean();
				$this->_pushLogIndent();
				foreach (explode(PHP_EOL, $messages) as $message)
				{
					$this->_logMessage($message);
				}
				$this->_popLogIndent();
				return true;
			}
			return false;
		}

		//! Run all selected setup steps.
		public function run()
		{
			if(!$this->_validateOptions())
			{
				$this->_logMessage('Invalid arguments.');
				exit(1);
			}

			$this->_logMessage("Started");

			$settings = $this->_getSettings();

			$this->_setupConfigFiles($settings);
			if(!$this->_generateData())
			{
				$this->_logMessage('Failed to generate and write data.');
				exit(1);
			}
			$this->_createDatabases($settings);
			$this->_runDatabaseUpdate();
			$this->_copyKrbLibFile();
			$this->_copyMcapiLibFile();
			$this->_setupTempFolders();


			$this->_logMessage("Finished");
		}
	}

?>
