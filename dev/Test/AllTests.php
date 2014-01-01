<?php

class AllTests extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $name => $object) {
			if (substr($name, -8) === 'Test.php') {
				$suite->addTestFile($name);
			}
		}

		return $suite;
	}
}