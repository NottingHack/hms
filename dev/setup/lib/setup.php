<?php

	require_once('utils.php');
	require_once('data_generator.php');

	//! Setup is a class that is responsible for parsing options and then performing certain steps based on those options.
	class Setup
	{
		private $newline = null; 	//!< Newline character, different depending on output (HTML or text).

		//! Options
		private $createDb = false;				//!< If true create the instrumentation and instrumentation_test databases.
		private $populateDb = false;			//!< If true populate the databases with default tables and data.
		private $useRealKrb = false;			//!< If true then use the real KRB Auth lib, otherwise use a dummy one.
		private $setupTempFolders = false;		//!< If true create the temporary folders CakePHP needs.
		private $useDevelopmentConfigs = true;	//!< If true then use the development version of config files

		//! The following details are used to create a HMS login for the user with admin rights.
		private $firstname = '';			//!< Firstname of the user.
		private $surname = '';				//!< Surname of the user.
		private $username = '';				//!< Username of the user.
		private $email = '';				//!< Email of the user.

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

		//! Set if we should create the temporary folders CakePHP requires.
		/*!
			@param bool $setupTempFolders If true then temporary folders will be created.
		*/
		public function setUseDevelopmentConfigs($useDevelopmentConfigs)
		{
			$this->useDevelopmentConfigs = $useDevelopmentConfigs;
		}

		//! Set if we should use the development or production configs.
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

		//! Create the config files HMS needs to run.
		/*!
			@param array $settings The settings to use.
		*/
		private function _createConfigFiles($settings)
		{
			$configPath = '../../../app/Config/';

			$files = array(
				'database',
				'hms',
				'krb',
				'mailchimp',
				'email',
				'debug',
			);

			// ... Not that I'm OCD or anything
			asort($files);

			// Create each of the config files
			foreach ($files as $fileName) 
			{
				// First check for dev/production templates
				$templateType = 'production';
				if($this->useDevelopmentConfigs)
				{
					$templateType = 'development';
				}

				$templateFilePath = makeAbsolutePath("$fileName.$templateType.template");
				if (!file_exists($templateFilePath))
				{
					// Fall back to the regular template
					$templateFilePath = makeAbsolutePath("$fileName.template");

					if(!file_exists($templateFilePath))
					{
						$this->_logMessage("Skipping config file: $filename because we couldn't find a matching template");
						continue;
					}
				}

				$currentContents = file_get_contents($templateFilePath);
				$newContents = $this->_replaceFields($currentContents, $settings[$fileName]);

				if (file_put_contents("$configPath$fileName.php", $newContents) !== FALSE) 
				{
					$this->_logMessage("Created $fileName.php from template $templateFilePath");
				}
				else 
				{
					$this->_logMessage("Failed to create $fileName.php");
				}
			}
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
					array_push($validFiles, makeAbsolutePath('./sql/' . $filename));
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
					Group::CURRENT_MEMBERS, Group::FULL_ACCESS, Group::MEMBER_ADMIN,
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
				// Main Database
				$oDB = new mysqli($settings['database']['default_host'], $settings['database']['default_login'], $settings['database']['default_password']);

				if ($oDB->connect_error) 
				{
					$this->_logMessage("Couldn't connect to main database");
				}
				else 
				{
					$defaultDbName = $settings['database']['default_database'];
					if($this->createDb)
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
					if($this->createDb)
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
			}
		}

		//! Copy either the real or dummy KRB Auth lib file to the lib folder.
		private function _copyKrbLibFile()
		{
			$krbFolder = '../../../app/Lib/Krb/';

			$toFile = $krbFolder . 'krb5_auth.php';
			$fromFile = 'krb5_auth.dummy';

			if($this->useRealKrb)
			{
				$fromFile = 'krb5_auth.real';
			}

			if(!file_exists($krbFolder))
			{
				if(mkdir($krbFolder))
				{
					$this->_logMessage("Created folder at: $krbFolder");
				}
				else
				{
					$this->_logMessage("Failed to create folder at: $krbFolder");
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
			}
		}

		//! Get the appropriate newline character.
		/*
			@retval string An a string representing a newline for the current output.
		*/
		private function _getNewline()
		{
			if(!isset($this->newline))
			{
				// Note: Like most things in PHP this function isn't reliable:
				// http://php.net/manual/en/function.php-sapi-name.php
				$sapiType = php_sapi_name();

				if($sapiType == 'cli')
				{
					// We're probably being called from command line.
					$newline = PHP_EOL;
				}
				else
				{
					// We're probably being called from the web.
					$newline = '<br/>';
				}
			}
			
			return $newline;
		}

		//! Format and write a log message, prepends timestamp and appends a newline.
		/*!
			@param string $message The message to write.
		*/
		private function _logMessage($message)
		{
			echo sprintf("[%s] %s%s", date("H:i:s"), $message, $this->_getNewline());
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

			return $aSettings;
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

			// Connect to the database, if we do this mysql_real_escape_string might work
			$oDB = new mysqli($settings['database']['default_host'], $settings['database']['default_login'], $settings['database']['default_password']);
			if ($oDB->connect_error) 
			{
				$this->_logMessage("Couldn't connect to main database");
			}

			$this->_createConfigFiles($settings);
			if(!$this->_generateData())
			{
				$this->_logMessage('Failed to generate and write data.');
				exit(1);
			}
			$this->_createDatabases($settings);
			$this->_copyKrbLibFile();
			$this->_setupTempFolders();


			$this->_logMessage("Finished");
		}
	}

?>
