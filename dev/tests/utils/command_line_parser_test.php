<?php

	require_once('utils/command_line_parser.php');

	class CommandLineParserTest extends PHPUnit_Framework_TestCase
	{
		private $clParser = null;

		public function setUp()
		{
			$this->clParser = new CommandLineParser();
		}

		/**
	     * @expectedException InvalidArgumentException
	     */
		public function test_adding_multiple_flags_with_same_name_throws_invalid_argument()
		{
			$this->clParser->addFlag('foo', 'description');
			$this->clParser->addFlag('foo', 'another description');
		}

		/**
	     * @expectedException InvalidArgumentException
	     */
		public function test_adding_multiple_values_with_same_name_throws_invalid_argument()
		{
			$this->clParser->addValue('foo', 'description');
			$this->clParser->addValue('foo', 'another description');
		}

		/**
	     * @expectedException InvalidArgumentException
	     */
		public function test_adding_flag_and_values_with_same_name_throws_invalid_argument()
		{
			$this->clParser->addFlag('foo', 'description');
			$this->clParser->addValue('foo', 'another description');
		}

		public function test_usage_flag()
		{
			$this->clParser->addFlag('foo', 'description');
			$usageStr = $this->clParser->usage();

			$this->assertContains('[--foo]', $usageStr);
			$this->assertContains('description', $usageStr);
		}

		public function test_usage_optional_value()
		{
			$this->clParser->addValue('foo', 'description', 'defaultVal');
			$usageStr = $this->clParser->usage();

			$this->assertContains('[--foo]', $usageStr);
			$this->assertContains('description', $usageStr);
			$this->assertContains('Defaults to: defaultVal', $usageStr);
		}

		public function test_usage_required_value()
		{
			$this->clParser->addValue('foo', 'description');
			$usageStr = $this->clParser->usage();

			$this->assertContains('--foo', $usageStr);
			$this->assertContains('description', $usageStr);
		}

		/**
	     * @expectedException PHPUnit_Framework_Error
	     * @expectedExceptionMessage Undefined index: foo
	     */
		public function test_get_value_throws_if_value_does_not_exist()
		{
			$this->clParser->getValue('foo');
		}

		public function test_parse_no_args()
		{
			$this->assertTrue($this->clParser->parse(array()));
		}

		public function test_parse_single_flag_not_set()
		{
			$this->clParser->addFlag('foo', 'description');
			$this->assertTrue($this->clParser->parse(array()));
			$this->assertFalse($this->clParser->getValue('foo'));
		}

		public function test_parse_single_flag_set()
		{
			$this->clParser->addFlag('foo', 'description');
			$this->assertTrue($this->clParser->parse(array('--foo')));
			$this->assertTrue($this->clParser->getValue('foo'));
		}
	}

?>