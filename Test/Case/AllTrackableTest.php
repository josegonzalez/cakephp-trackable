<?php
/**
 * All Trackable plugin tests
 */
class AllTrackableTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite method, defines tests for this suite.
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Trackable tests');
		$path = CakePlugin::path('Trackable') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);
		return $suite;
	}
}
