<?php

class ZFExt_Model_Application {

    private $db;

    public function __construct($new_db){
        $this->db = $new_db;
    }

/*
    public function getDatabaseNames() {
        $stmt = $this->db->query('SELECT * from grid_objects');
        $stmt->setFetchMode(Zend_Db::FETCH_COLUMN);
        return $stmt->fetchAll();
    }
*/
    
}
