<?php

class ZFExt_Model_DbTests extends PHPUnit_Framework_TestCase {

	public function testDatabaseNames() {

		//$entry = new ZFExt_Model_Entry;
		//$entry->title = 'My Title';
		//$this->assertEquals('My Title', $entry->title);

        $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);

        $this->assertNotNull($db_app);
        //print_r($db_app);
    
        $app_model = new ZFExt_Model_Application($db_app);
        $db_names = $app_model->getDatabaseNames();
    
        $this->assertTrue(isset($db_names),"selected database names should return something");
        $this->assertInternalType('array',$db_names,"selected database names should be returning an array");
        //print_r($db_names);
    
	}

}
