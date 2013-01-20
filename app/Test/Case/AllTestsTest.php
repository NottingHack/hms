<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       app.Test.Case
 */
class AllTests extends CakeTestSuite 
{
	public static function suite() 
	{
		$suite = new CakeTestSuite('All Tests');
		$suite->addTestDirectoryRecursive(TESTS);
		return $suite;
	}
}
