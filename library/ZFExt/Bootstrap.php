<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	//protected function _initLayout(){
		//Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH.'/views/layouts'));
	//}

	protected function _initView(){
		$view = new Zend_View();
		$view->setEncoding('UTF-8');
		$view->doctype('HTML5');
		$view->headMeta()->setCharset('UTF-8');
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);
		return $view;
	}

	protected function _initModifiedFrontController(){

		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');

		$response = new Zend_Controller_Response_Http;
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
		$front->setResponse($response);

    		$pluginPageRouter = new ZFExt_PageRouter();
    		$front->registerPlugin($pluginPageRouter);    
	}

}

