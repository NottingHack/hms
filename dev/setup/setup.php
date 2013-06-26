<?php

	//! Setup is a class that is responsible for parsing options and then performing certain steps based on those options.
	class Setup
	{
		private $newline = null; 	//!< Newline character, different depending on output (HTML or text).

		//! Options
		private $createDb = false;			//!< If true create the instrumentation and instrumentation_test databases.
		private $populateDb = false;		//!< If true populate the databases with default tables and data.
		private $useRealKrb = false;		//!< If true then use the real KRB Auth lib, otherwise use a dummy one.
		private $setupTempFolders = false;	//!< If true create the temporary folders CakePHP needs.

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
			$path = '../../app/Config/';

			$files = array(
				'database',
				'hms',
				'krb',
				'mailchimp',
				'email',
				);

			// Create each of the config files
			foreach ($files as $fileName) 
			{
				if (!file_exists($fileName . '.template')) 
				{
					continue;
				}
				$file = file_get_contents($fileName . '.template');

				$file = $this->_replaceFields($file, $settings[$fileName]);

				if (file_put_contents($path . $fileName . '.php', $file) !== FALSE) 
				{
					$this->_logMessage("Created $fileName.php");
				}
				else 
				{
					$this->_logMessage("Failed to create $fileName.php");
				}
			}
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
							if ($oDB->multi_query(file_get_contents('hms.sql'))) 
							{
								$this->_logMessage("Populated main database");
								$oDB->store_result();
								while ($oDB->more_results()) 
								{
									$oDB->next_result();
									$oDB->store_result();
								}
								// set up dev user
								$sSql = "INSERT INTO `members` (`member_id`, `member_number`, `firstname`, `surname`, `email`, `join_date`, `handle`, `unlock_text`, `balance`, `credit_limit`, `member_status`, `username`, `account_id`, `address_1`, `address_2`, `address_city`, `address_postcode`, `contact_number`) VALUES";
								$sSql .= "(6, 111, '" . $this->firstname . "', '" . $this->surname . "', '" . $this->email . "', '" . date("Y-m-d") . "', '" . $this->username . "', 'Welcome " . $this->username . "', -1200, 5000, 5, '" . $this->username . "', NULL, NULL, NULL, NULL, NULL, NULL);";

								if ($oDB->query($sSql)) 
								{
									$this->_logMessage("Created DEV user");
								}
								else 
								{
									$this->_logMessage("Failed to create DEV user, was your input valid?");
									_logMessage($oDB->error);
								}
							}
							else 
							{
								$this->_logMessage("Failed to populate main database");
							}
						}
					}
					else
					{
						$this->_logMessage("Unable to select database: $defaultDbName");
					}
				}
				$oDB->close();

				// Test Database
				$oDB = new mysqli($settings['database']['test_host'], $settings['database']['test_login'], $settings['database']['test_password'], $settings['database']['test_database']);

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

					if($oDB->select_db($testDbName))
					{
						if($this->populateDb)
						{
							if ($oDB->multi_query(file_get_contents('hms_test.sql'))) 
							{
								$this->_logMessage("Populated test database");
							}
							else 
							{
								$this->_logMessage("Failed to populate test database");
							}
						}
					}
					else
					{
						$this->_logMessage("Unable to select database: $testDbName");
					}
				}
				$oDB->close();
			}
		}

		//! Copy either the real or dummy KRB Auth lib file to the lib folder.
		private function _copyKrbLibFile()
		{
			$krbFolder = '../../app/Lib/Krb/';

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

			$message = "Attempting to copy $fromFile to $toFile... ";

			if(copy($fromFile, $toFile))
			{
				$message .= "Copy successful";
			}
			else
			{
				$message .= "Copy failed";
			}

			$this->_logMessage($message);
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
					'../../app/tmp',
					'../../app/tmp/cache',
					'../../app/tmp/cache/models',
					'../../app/tmp/cache/persistent',
					'../../app/tmp/cache/views',
					'../../app/tmp/logs',
					'../../app/tmp/sessions',
					'../../app/tmp/tests',
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

			$this->_createConfigFiles($settings);
			$this->_createDatabases($settings);
			$this->_copyKrbLibFile();
			$this->_setupTempFolders();


			$this->_logMessage("Finished");
		}
	}

?>
