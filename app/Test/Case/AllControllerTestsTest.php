<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       app.Test.Case
 */
class AllControllerTests extends PHPUnit_Framework_TestSuite
{
	public static function suite() 
	{
		$suite = new CakeTestSuite('All Controller Tests');

		$suite->addTestDirectory(TESTS . 'Case' . DS . 'Controller');
		return $suite;
	}
}

