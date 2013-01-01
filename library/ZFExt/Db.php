<?php

class ZFExt_Db {

    protected static $source_path = "/config/source_login.ini";
    protected static $app_path = "/config/app_login.ini";

    #there are two allowed database connections
    const APPLICATION = 0;
    const SOURCE = 1;

    //each 2 element arrays to keep each connection
    protected static $db = array();#some kind of Zend_Db_Adapter_Abstract
    protected static $config = array();
    
    #will retrieve the right ZFExt_Db object, initializing it first if needed
    public static function getInstance($type, $database = null) {

        if(!isset($db[$type])){

            switch($type){
                case ZFExt_Db::APPLICATION:
                    $load_path = static::$app_path;
                break;
                case ZFExt_Db::SOURCE:
                    $load_path = static::$source_path;
                break;
            }

            if($type == ZFExt_Db::SOURCE){
                if(isset($database))
                    $config[$type]->database->params->database = $database;
                else
                    throw new Exception("An source database connection must have a second parameter that is the database requested");
            }

            static::$config[$type] = new Zend_Config_Ini( APPLICATION_ROOT . $load_path, APPLICATION_ENV);

            $db[$type] = Zend_Db::factory(static::$config[$type]->database);

        }

        return $db[$type];

    }

}
