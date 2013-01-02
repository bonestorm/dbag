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

        $ajax_data = $this->getRequest()->getPost();

        $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);
        $app_model = new ZFExt_Model_Application($db_app);

        //sends a stack of actions. 
        //we send back this stack but with results
        $stack = $ajax_data['stack'];

/*
getTableFields: gets information about a table (or tables)
passed in:  ['action' => 'getTableFields', 'database' => 'database_name', 'data' => ['table_one','table_two','table_three']]
results:    ['action' => 'getTableFields', 'database' => 'database_name', 'data' => ['table_one' => ['field_one' => ['Type' => 'int(11)','Null' => true],'field_two' => ['Type......

getDatabaseNames: gets names of all the databases in dbag.table_schema
passed in:  ['action' => 'getDatabaseNames']
results:    ['action' => 'getDatabaseNames', 'data' => ['database1','database2','database3'....

getAllObjects:
passed in:  ['action' => 'getAllObjects', 'database' => 'database_name']
results:    ['action' => 'getAllObjects', 'data' => [['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...],['id' => 70, 'type' => 'JOIN'....

saveObject: (value of returned 'inserted' and 'updated' field is the id of the grid object)
if there is no 'id' then it will insert
passed in:  ['action' => 'saveObject', 'data' => ['type' => 'TABLE','x' => 12,'y' => 23...]]
results:    ['action' => 'saveObject', 'data' => ['inserted' => 60]]
if there IS an 'id' then it will update
passed in:  ['action' => 'saveObject', 'data' => ['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...]]
results:    ['action' => 'saveObject', 'data' => ['updated' => 60]]

deleteObject:
passed in:  ['action' => 'deleteObject', 'id' => 23]
results:    ['action' => 'deleteObject', 'data' => ['deleted' => 23]]

*/

        //every item in the stack is one of these actions:
        $stack_actions = array('getTableFields','getDatabaseNames','getAllObjects','saveObject','deleteObject');

        foreach($stack as &$stack_row){

            //when a database comes around we can connect to the source database
            if(isset($ajax_data['database']) && (!isset($database) || $database != $ajax_data['database'])){
                $database = $ajax_data['database'];
                if(isset($database)){
                    $db_source = ZFExt_Db::getInstance(ZFExt_Db::SOURCE,$database);
                    $source_model = new ZFExt_Model_Source($db_source);
                }
            }

            if(in_array($stack_row['action'],$stack_actions)){
                switch($stack_row['action']){
                    case 'getTableFields':
                      $table_info = $source_model->getTableFields($stack_row['data']);
                      $table_info['data'] = $table_info;
                    break;
                    case 'getDatabaseNames':
                      //this used to get the database names from source model and it was just all of the tables that were in the RDBMS
                      //but now they're imported to the application model and selected from there when we need them
                      $stack_row['data'] = $app_model->getDatabaseNames();
                    break;
                    case 'getAllObjects':
                      $stack_row['data'] = $app_model->getAllObjects($stack_row['data']);
                    break;
                    case 'saveObject':
                      $res = $app_model->saveObject($stack_row['data']);
                      if($res['action'] == "inserted"){
                          $stack_row['data'] = array('inserted' => $res['id']);
                      } else {//update
                          $stack_row['data'] = array('updated' => $res['id']);
                      }
                    break;
                    case 'deleteObject':
                      $num_del = $app_model->deleteObject($stack_row['data']);
                      if($num_del > 0){$stack_row['data'] = array('deleted' => $stack_row['data']['id']);}
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

