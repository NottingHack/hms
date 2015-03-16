<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       app.Test.Case
 */
class AllViewTests extends PHPUnit_Framework_TestSuite
{
	public static function suite() 
	{
		$suite = new CakeTestSuite('All View Tests');

		$suite->addTestDirectory(TESTS . 'Case' . DS . 'View');
		$suite->addTestDirectory(TESTS . 'Case' . DS . 'View' . DS . 'Helper');
		return $suite;
	}
}

