<?php

spl_autoload_register(create_function('$class',
"include str_replace('_', '/', \$class) . '.php';"
));

error_reporting( E_ALL | E_STRICT );


if(!defined('APPLICATION_PATH')){
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
}

if(!defined('APPLICATION_ROOT')){
	define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..'));
}

if(!defined('APPLICATION_ENV')){
	if(getenv('APPLICATION_ENV')){
		$env = getenv('APPLICATION_ENV');
	} else {
		$env = 'production';
	}
	define('APPLICATION_ENV', $env);
}

set_include_path(
	  APPLICATION_ROOT . '/library' . PATH_SEPARATOR //model code, domain specific, database interaction
	. APPLICATION_ROOT . '/vendor' . PATH_SEPARATOR //other vendor libraries
	. APPLICATION_ROOT . '/vendor/Zend/library' . PATH_SEPARATOR
	. get_include_path()
);

unset($env);

