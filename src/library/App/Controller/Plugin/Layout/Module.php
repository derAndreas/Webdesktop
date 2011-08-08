<?php
/**
 * Description of Module
 *
 * @author Andreas
 */
class App_Controller_Plugin_Layout_Module extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        #var_dump($request->getParams());
        // Changes the Layout based on the module name
        Zend_Layout::getMvcInstance()->setLayout($request->getModuleName());
    }

}
?>
