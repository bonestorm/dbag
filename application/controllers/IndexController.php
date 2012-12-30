<?php

class IndexController extends Zend_Controller_Action {

	public function init() {}

	public function indexAction() {

		$this->view->title = "DBag : Database Admin Tool";
		$this->view->description = "database visualization and querying tool";
		$this->view->author = "David Jensen";

		//$this->_helper->viewRenderer->setNoRender(true);

	}
}

