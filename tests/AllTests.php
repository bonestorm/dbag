<?php


if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

//autoloader, defines path constants, adds paths to include_path
if(!defined('PATHS_LOADED')){
    require_once realpath(dirname(__FILE__).'/../TestHelper.php');
    TestHelper::set();
    define('PATHS_LOADED',true);
}

class AllTests {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite(){
		$suite = new PHPUnit_Framework_TestSuite('All Model Tests');
		$suite->addTest(ZFExt_Model_AllTests::suite());
		return $suite;
	}
}

if(PHPUnit_MAIN_METHOD == 'AllTests::main'){
	AllTests::main();
}

