<?php

class ZFExt_Model_Application {

    protected $db;

    public function __construct(Zend_Db_Adapter_Abstract $new_db){
        $this->db = $new_db;
    }

    public function insertDatabaseNames($db_names){

        $this->db->delete("table_schema");//deletes all the database names that exists already

        $rows_added = 0;
        foreach($db_names as $db_name){
            $rows_added += $this->db->insert("table_schema",array('name'  => $db_name));
        }
        return $rows_added;
    }

    public function selectDatabaseNames(){
        #$this->db->setFetchMode(Zend_Db::FETCH_COLUMN);#support for this is very sketchy
        $select = $this->db->select()->from('table_schema');
        $ass_array = $this->db->fetchAll($select);
        $result = array();
        foreach($ass_array as $row){
            $result[] = $row['name'];
        }
        return $result;
        
    }
    
}
