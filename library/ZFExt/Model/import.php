<?php

    //autoloader, defines path constants, adds paths to include_path
    $adjust_path = '../../../';
    require_once $adjust_path . 'TestHelper.php';
    TestHelper::set();

    #no way to do this?
    #$db_source = ZFExt_Db::getInstance(ZFExt_Db::SOURCE);
    #$source_model = new ZFExt_Model_Source($db_source);
    #$db_names = $source_model->getDatabaseNames();

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
        throw new Exception("failure getting application's database names for import into admin database");
    } else {

        $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);
        $app_model = new ZFExt_Model_Source($db_app);

        $rows_added = $app__model->insertDatabaseNames($db_names);
        if($rows_added == 1){
            echo "{$rows_added} database names added to table_schema\n";
        }

    }
