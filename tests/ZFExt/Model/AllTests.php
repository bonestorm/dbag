<?php

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'ZFExt_Model_AllTests::main');
}

if(!defined('PATHS_LOADED')){
    require_once realpath(dirname(__FILE__).'/../../../TestHelper.php');
    TestHelper::set();
    define('PATHS_LOADED',true);


}

require_once 'ZFExt/Model/DbTests.php';

class ZFExt_Model_AllTests {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite(){

		//$suite = new PHPUnit_Framework_TestSuite('ZFSTDE Blog Suite: Model');
		//$suite->addTestSuite('ZFExt_Model_EntryTest');

		$suite = new PHPUnit_Framework_TestSuite('Database Health Tests');
		$suite->addTestSuite('ZFExt_Model_DbTests');

		return $suite;
	}
}

if(PHPUnit_MAIN_METHOD == 'ZFExt_Model_AllTests::main'){
	ZFExt_Model_AllTests::main();
}

