<?php

class TestHelper {

    public static function set(){

        spl_autoload_register(create_function('$class',
            "include str_replace('_', '/', \$class) . '.php';"
        ));
    
        define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
        define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/'));
        define('APPLICATION_ENV', 'development');
    
        error_reporting( E_ALL | E_STRICT );
    
        set_include_path(
	        APPLICATION_ROOT . '/library' . PATH_SEPARATOR //model code, domain specific, database interaction
	        . APPLICATION_ROOT . '/vendor' . PATH_SEPARATOR //other vendor libraries
	        . APPLICATION_ROOT . '/tests' . PATH_SEPARATOR //tests
	        . APPLICATION_ROOT . '/vendor/Zend/library' . PATH_SEPARATOR
	        . get_include_path()
        );
    }

}

