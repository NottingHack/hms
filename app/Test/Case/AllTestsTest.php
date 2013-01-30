<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       app.Test.Case
 */
class AllTests extends PHPUnit_Framework_TestSuite 
{
	public static function suite() 
	{
		$suite = new CakeTestSuite('All Tests');

		$suite->addTestDirectory(TESTS . 'Case' . DS . 'Model');
		$suite->addTestDirectory(TESTS . 'Case' . DS . 'Controller');
		$suite->addTestDirectory(TESTS . 'Case' . DS . 'View');
		$suite->addTestDirectory(TESTS . 'Case' . DS . 'View' . DS . 'Helper');
		return $suite;
	}
}
