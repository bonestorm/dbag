<?php

class AjaxController extends Zend_Controller_Action {

	public function preDispatch() {
		//$action = $this->getRequest()->getActionName();
		//$this->_helper->contextSwitch()
			//->addActionContext($action, 'json')
			//->initContext();
		//$this->_helper->layout()->disableLayout();
		//$this->_helper->viewRenderer->setNoRender();
	}
	

	public function indexAction() {

		//if($this->_request->getQuery('ajax') == 1){//for '?ajax=1' appended
		if($this->_request->isXmlHttpRequest() == 1){

			$this->_helper->contextSwitch()->setAutoJsonSerialization(false)->initContext('json');

            $db_app = ZFExt_Db::getInstance(ZFExt_Db::APPLICATION);
            //$db_source = ZFExt_Db::getInstance(ZFExt_Db::SOURCE);//don't have the database yet

            $app_model = new ZFExt_Model_Application($db_app);
            //$source_model = new ZFExt_Model_Source($db_source);

            $this->_helper->json(array(
                'one' => 1,
                'two' => 2
            ));

		} else {
            throw new Zend_Exception("Wrong request type.  Must be a XmlHttpRequest");
        }


/*
		$myArray = array(
			'someData',
			'moreData' => array('hello')
		);
		$jsonData = Zend_Json::encode($myArray);
		$this->getResponse()->appendBody($jsonData);
*/

	}
}

