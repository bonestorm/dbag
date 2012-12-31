<?php


    //autoloader, defines path constants, adds paths to include_path
    $adjust_path = '../../../';
    require_once $adjust_path . 'TestHelper.php';
    TestHelper::set();


    $db_admin = ZFExt_Db::getInstance(ZFExt_Db::ADMIN);

    print_r($db_admin);

    $admin_model = new ZFExt_Model_Admin($db_admin);
    $db_names = $admin_model->selectDatabaseNames();

    print_r($db_names);
    die;
