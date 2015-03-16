<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       app.Test.Case
 */
class AllModelTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new CakeTestSuite('All Model Tests');
        
        $suite->addTestDirectory(TESTS . 'Case' . DS . 'Model');
        return $suite;
    }
}

