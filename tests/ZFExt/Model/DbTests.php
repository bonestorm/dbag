<?php

class ZFExt_Model_DbTests extends PHPUnit_Framework_TestCase {

	public function testDatabaseNames() {

		//$entry = new ZFExt_Model_Entry;
		//$entry->title = 'My Title';
		//$this->assertEquals('My Title', $entry->title);

        $db_admin = ZFExt_Db::getInstance(ZFExt_Db::ADMIN);

        $this->assertNotNull($db_admin);
        //print_r($db_admin);
    
        $admin_model = new ZFExt_Model_Admin($db_admin);
        $db_names = $admin_model->selectDatabaseNames();
    
        $this->assertTrue(isset($db_names),"selected database names should return something");
        $this->assertInternalType('array',$db_names,"selected database names should be returning an array");
        //print_r($db_names);
    
	}

}
