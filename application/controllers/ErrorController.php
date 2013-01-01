<?php

class ErrorController extends Zend_Controller_Action {
    public function errorAction() {
        $error = $this->_getParam('error_handler');
        switch($error->type){
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->statusCode = 404;
            break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->statusCode = 500;
            break;
        }
        $this->view->message = $error->exception->getMessage();
        error_log("ERORR CONTROLLER caught error: ".$this->view->message);
    }
}

