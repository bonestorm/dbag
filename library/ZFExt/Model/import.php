<?php

    //autoloader, defines path constants, adds paths to include_path
    $adjust_path = '../../../';
    require_once $adjust_path . 'TestHelper.php';
    TestHelper::set();

    #no way to do this?
    #$db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);
    #$app_model = new ZFExt_Model_Application($db_app);
    #$db_names = $app_model->getDatabaseNames();

    $config = new Zend_Config_Ini( APPLICATION_ROOT . "/config/app_login.ini", APPLICATION_ENV);
    $params = $config->database->params->toArray();

    $app_db = new mysqli('localhost','root','root');#$params['host'], $params['username'], $params['password']);

    $query = "SHOW DATABASES";
    if ($result = $app_db->query($query)) {
      /* fetch object array */
      while ($row = $result->fetch_row()) {
          $db_names[] = $row[0];
      }
    }

    if(!is_array($db_names)){
        throw Exception("failure getting application's database names for import into admin database");
    } else {

        $db_admin = ZFExt_Db::getInstance(ZFExt_Db::ADMIN);
        $admin_model = new ZFExt_Model_Admin($db_admin);

        $rows_added = $admin_model->insertDatabaseNames($db_names);
        if($rows_added == 1){
            echo "{$rows_added} database names added to table_schema\n";
        }

    }
