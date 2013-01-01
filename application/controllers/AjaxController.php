<?php

class AjaxController extends Zend_Controller_Action {

	public function preDispatch() {
		$action = $this->getRequest()->getActionName();
		$this->_helper->contextSwitch()
			->addActionContext($action, 'json')
			->initContext();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	

	public function indexAction() {


		//if($this->_request->getQuery('ajax') == 1){//for '?ajax=1' appended
		if($this->_request->isXmlHttpRequest() == 1){

			$this->_helper->contextSwitch()->setAutoJsonSerialization(false)->initContext('json');

            $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);
            $app_model = new ZFExt_Model_Application($db_app);

            $database = $ajax_data['database'];
            if(isset($datasbase){
                $db_source = ZFExt_Db::getInstance(ZFExt_Db::SOURCE,$database);
                $source_model = new ZFExt_Model_Source($db_source);
            }


            $ajax_data = $this->getRequest()->getPost();


            //sends a stack of actions. 
            //we send back this stack but with results
            $stack = $ajax_data['stack'];

/*
            getTableFields: gets information about a table (or tables)
            passed in:  ['action' => 'getTableFields', 'data' => ['table_one','table_two','table_three']]
            results:    ['action' => 'getTableFields', 'data' => ['table_one' => ['field_one' => ['Type' => 'int(11)'],'field_two' => ['Type......

            getDatabaseNames: gets names of all the databases in dbag.table_schema
            passed in:  ['action' => 'getDatabaseNames']
            results:    ['action' => 'getDatabaseNames', 'data' => ['database1','database2','database3'....

            getAllObjects:
            passed in:  ['action' => 'getAllObjects', 'database' => 'database_name']
            results:    ['action' => 'getAllObjects', 'data' => [['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...],['id' => 70, 'type' => 'JOIN'....

            saveObject:
            if there is no 'id' then it will insert
            passed in:  ['action' => 'saveObject', 'data' => [            'type' => 'TABLE','x' => 12,'y' => 23...]]
            results:    ['action' => 'saveObject', 'data' => ['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...]]
            if there IS an 'id' then it will update
            passed in:  ['action' => 'saveObject', 'data' => ['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...]]
            results:    ['action' => 'saveObject', 'updated' => 1]
*/

            //every item in the stack is one of these actions:
            $stack_actions = array('getTableFields','getDatabaseNames','getAllObjects','saveObject','deleteObject');

            foreach($stack as &$stack_row){
                if(in_array($stack_row['action'],$stack_actions)){
                    switch($stack_row['action']){
                        case 'getTableFields':
                        break;
                        case 'getDatabaseNames':
                        break;
                        case 'getAllObjects':
                        break;
                        case 'saveObject':
                        break;
                        case 'deleteObject':
                        break;
                    }
                } else {
                    throw new Exception("stack action not recognized");
                }
            }

            $this->_helper->json($stack);

		} else {
            throw new Zend_Exception("Wrong request type.  Must be a XmlHttpRequest");
        }

	}

}

