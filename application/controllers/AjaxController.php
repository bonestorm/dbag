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
 
getDatabaseNames: gets names of all the databases in dbag.table_schema
passed in:  ['action' => 'getDatabaseNames']
results:    ['action' => 'getDatabaseNames', 'data' => ['database1','database2','database3'....


getTableIds: gets names of all the tables in the selected database
passed in:  ['action' => 'getTableIds', 'database' => 'database_name']
results:    ['action' => 'getTableIds', 'database' => 'database_name', 'data' => ['table1' => '<table1_id> or -1 if table not placed in grid,'table2' => '<table2_id or -1...

getAllObjects: (getTableIds is calle implicitely and its results are added to table_info, giving any tables not placed a '-1' value)
passed in:  ['action' => 'getAllObjects', 'database' => 'database_name']
results:    ['action' => 'getAllObjects', 'database' => 'database_name', 
  'data' => [
    grid_info: ['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...],['id' => 70, 'type' => 'JOIN'....]
    table_info: ['table1' => '<table1_id> or -1 if table not placed in grid,'table2' => '<table2_id or -1...]
  ]
]


getTableFields: gets information about a table (or tables)
passed in:  ['action' => 'getTableFields', 'database' => 'database_name', 'data' => ['table_one','table_two','table_three']]
results:    ['action' => 'getTableFields', 'database' => 'database_name', 'data' => ['table_one' => ['field_one' => ['Type' => 'int(11)','Null' => true],'field_two' => ['Type......


saveObject: (value of returned 'inserted' and 'updated' field is the id of the grid object)
if there is no 'id' then it will insert
passed in:  ['action' => 'saveObject', 'data' => ['type' => 'TABLE','x' => 12,'y' => 23...]]
results:    ['action' => 'inserted', 'id' => 60]
if there IS an 'id' then it will update
passed in:  ['action' => 'saveObject', 'data' => ['id' => 60, 'type' => 'TABLE','x' => 12,'y' => 23...]]
results:    ['action' => 'updated', 'id' => 60]
        
deleteObject:
passed in:  ['action' => 'deleteObject', 'id' => 23]
results:    ['action' => 'deleted', 'id' => 23]
 
*/

        //every item in the stack is one of these actions:
        $stack_actions = array('getTableFields','getDatabaseNames','getTableNames','getAllObjects','saveObject','deleteObject');

        foreach($stack as &$stack_row){

            //when a database comes around we can connect to the source database
            if(isset($stack_row['database']) && (!isset($database) || $database != $stack_row['database'])){
                $database = $stack_row['database'];
                if(isset($database)){
                    $db_source = ZFExt_Db::getInstance(ZFExt_Db::SOURCE,$database);
                    $source_model = new ZFExt_Model_Source($db_source);
                }
            }

            if(in_array($stack_row['action'],$stack_actions)){
                switch($stack_row['action']){

                    case 'getDatabaseNames':
                      //this used to get the database names from source model and it was just all of the tables that were in the RDBMS
                      //but now they're imported to the application model and selected from there when we need them
                      $stack_row['data'] = $app_model->getDatabaseNames();
                    break;

                    case 'getTableNames':
                      $stack_row['data'] = $source_model->getTableNames();
                    break;
                    case 'getAllObjects':

                      if(!isset($source_model)){throw new Exception("source database model missing, can't get al objects");}

                      //get the raw information
                      $table_names = $source_model->getTableNames();
                      $grid_info = $app_model->getAllObjects($stack_row['database']);

                      //pull it all together
                      $table_ids = array();
                      $deleted_table_ids = array();
                      foreach($table_names as $tname){
                        $table_ids[$tname] = -1;
                      }
                     
                      //give table_ids the the ids from grid_info 
                      foreach($grid_info as $row){
                        if($row['type'] == "TABLE"){
                          if(!isset($table_ids[$row['name']])){
                            //table removed from database after being worked on
                            //mark this one so the software can mark it red or something
                            $deleted_table_ids[$row['name']] = $row['id'];
                          }

                          //these ids include tables that have been deleted from the source database
                          $table_ids[$row['name']] = $row['id'];

                        }
                      }

//error_log(print_r($grid_info,true));
//error_log(print_r($table_ids,true));
                      //send it all back
                      $stack_row['data'] = array('grid_info' => $grid_info,'table_ids' => $table_ids,'deleted_table_ids' => $deleted_table_ids);

                    break;

                    case 'getTableFields':
                      $stack['data'] = $source_model->getTableFields($stack_row['data']);
                    break;

                    case 'saveObject':
                      $res = $app_model->saveObject($stack_row['data']);
                      if($res['action'] == "inserted"){
                          $stack_row = array('action' => 'inserted', 'id' => $res['id']);
                      } else {//update
                          $stack_row = array('action' => 'updated', 'id' => $res['id']);
                      }
                    break;
                    case 'deleteObject':
                      $num_del = $app_model->deleteObject($stack_row['data']);
                      if($num_del > 0){ $stack_row = array('action' => 'deleted', 'id' => $res['id']); }
                    break;
                }
            } else {
                throw new Exception("stack action not recognized");
            }
        }

//error_log(print_r($stack,true));

        $this->_helper->json($stack);

		} else {
            throw new Zend_Exception("Wrong request type.  Must be a XmlHttpRequest");
    }

	}

}

