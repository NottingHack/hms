<?php

	class CommandLineParser
	{
		private $expectedArgs = array();
		private $values = array();

		const TYPE_FLAG = 0;
		const TYPE_VALUE = 1;

		public function addFlag($flagName, $description)
		{
			$this->addArg($flagName, $description, false, CommandLineParser::TYPE_FLAG);
		}

		public function addValue($name, $description, $defaultVal = null)
		{
			$this->addArg($name, $description, $defaultVal, CommandLineParser::TYPE_VALUE);
		}

		public function parse($input = null)
		{
			// Grab input from the command line if not specified
			if(is_null($input))
			{
				$input = $argv;
			}

			if(!$this->parseImpl($input))
			{
				return false;
			}
			return true;
		}

		private function parseImpl($args)
		{
			// Set the default values first
			foreach ($this->expectedArgs as $name => $argDetails) 
			{
				if(isset($argDetails['default']))
				{
					$this->values[$name] = $argDetails['default'];
				}
			}

			for($i = 0; $i < count($args);)
			{
				$argName = substr($args[$i], 2);

				$inc = 1;
				if(array_key_exists($argName, $this->expectedArgs))
				{
					switch ($this->expectedArgs[$argName]['type']) 
					{
						case CommandLineParser::TYPE_FLAG:
							$this->values[$argName] = true;
							break;

						case CommandLineParser::TYPE_VALUE:
							if($i < count($args) - 1)
							{
								$this->values[$argName] = $args[$i + 1];
								$inc = 2;
							}
							break;
						
						default:
							throw new Exception('Unknown argument type');
					}
				}

				$i += $inc;
			}

			// Check all required values were set
			foreach ($this->expectedArgs as $name => $argDetails) 
			{
				if($argDetails['required'] && !array_key_exists($name, $this->values))
				{
					// Required arg was not set
					return false;
				}
			}

			return true;
		}

		public function getValue($key)
		{
			return $this->values[$key];
		}

		public function usage()
		{
			uksort($this->expectedArgs, function($a, $b) { return (int)$a['required'] - (int)$b['required']; });

			$usage = "Usage: " . PHP_EOL;
			foreach ($this->expectedArgs as $key => $arg)
			{
				$usage .= "\t";
				$usage .= $arg['required'] ? "--$key" : "[--$key]";
				$usage .= " {$arg['description']}";
				if(isset($arg['default']))
				{
					$usage .= " Defaults to: {$arg['default']}";
				}
				$usage .= PHP_EOL;
			}

			return $usage;
		}

		private function addArg($name, $description, $default, $type)
		{
			if(array_key_exists($name, $this->expectedArgs))
			{
				throw new InvalidArgumentException("An argument with the name $name already exists");
			}

			$this->expectedArgs[$name] = array(
				'description' => $description,
				'required' => !isset($default),
				'default' => $default,
				'type' => $type,
			);
		}
	}

?>