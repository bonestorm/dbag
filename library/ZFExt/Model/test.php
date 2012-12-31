<?php


    //autoloader, defines path constants, adds paths to include_path
    $adjust_path = '../../../';
    require_once $adjust_path . 'TestHelper.php';
    TestHelper::set();


    $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);

    print_r($db_app);

    $app_model = new ZFExt_Model_Source($db_app);
    $db_names = $app_model->selectDatabaseNames();

    print_r($db_names);
    die;
