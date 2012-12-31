<?php

class ZFExt_Db {

    protected static $app_path = "/config/app_login.ini";
    protected static $admin_path = "/config/admin_login.ini";

    #there are two allowed database connections
    const ADMIN = 0;
    const APPLICATION = 1;

    #using the ultra-rare Doubleton design pattern
    protected static $db = array();#some kind of Zend_Db_Adapter
    protected static $config = array();
    
    #will retrieve the right ZFExt_Db object, initializing it first if needed
    public static function getInstance($type, $database = null) {

        if(!isset($db[$type])){

            switch($type){
                case ZFExt_Db::ADMIN:
                    $load_path = static::$admin_path;
                break;
                case ZFExt_Db::APPLICATION:
                    $load_path = static::$app_path;
                break;
            }

            if($type == ZFExt_Db::APPLICATION){
                if(isset($database))
                    $config[$type]->database->params->database = $database;
                else
                    throw new Exception("An application database connection must have a second parameter that is the database requested");
            }

            static::$config[$type] = new Zend_Config_Ini( APPLICATION_ROOT . $load_path, APPLICATION_ENV);

            $db[$type] = Zend_Db::factory(static::$config[$type]->database);

        }

        return $db[$type];

    }

}
