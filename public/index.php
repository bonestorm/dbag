<?php

    #sets paths and includes autoloader
    require_once 'set_it_up.php';

    #Zend_Application initilization
	require_once 'Zend/Application.php';

	$application = new Zend_Application(
		APPLICATION_ENV,
		APPLICATION_ROOT . '/config/application.ini'
	);

	$application->bootstrap()->run();

