<?php

class ZFExt_Model_Source {

    private $db;

    public function __construct($new_db){
        $this->db = $new_db;
    }

    ##
    #Selects field information in a list of tables
    #It includes the field type and whether it can be null or not
    ##
    public function getTableFields($tables){

        if(gettype($tables) == "string"){$tables = array($tables);}//in case just one table is passed in

        $res = array();
        foreach($tables as $table){
            if(!array_key_exists($table,$res)){//might be duplicate tables so don't select it again

                $res[$table] = array();

                $meta = $this->db->describeTable($table);#get the info

                foreach($meta as $fname => $finfo){
                  foreach(array('DATA_TYPE','NULLABLE') as $ff){
                      $res[$table][$fname][strtolower($ff)] = $finfo[$ff];
                  }
                }
            }
        }

        return $res;

    }

    ##
    #Selects all table names in the connected database
    ##
    public function getTableNames(){
      return $this->db->fetchCol("SHOW tables");
    }

    
    
}
