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
    public function selectTableFields($tables){

        if(gettype($tables) == "string"){$tables = array($tables);}//in case just one table is passed in

        $res = array();
        foreach($tables as $table){
            $stmt = $this->db->query("SHOW columns from '".$this->db->quote($table)."'";
            if(!array_key_exists($table,$res)){//might be duplicate tables so don't select it again
                $res[$table] = array();
                while($row = $stmt->fetch()){
                    foreach(array('Type','Null') as $ff){
                        $res[$table][$row['Field']][strtolower($ff)] = $row[$ff];
                    }
                }
            }
        }
        return $res;

    }
    
}
