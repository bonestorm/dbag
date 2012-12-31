<?PHP
class ZFExt_PageRouter extends Zend_Controller_Plugin_Abstract {

  public function preDispatch(Zend_Controller_Request_Abstract $req) {
    $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
    if (!$dispatcher->isDispatchable($req, $req)) {

      $req->setModuleName('default');
      $req->setControllerName('index');
      $req->setActionName('index');
    }
  }

}
