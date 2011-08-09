<?php
/**
 * Plugin to changes the Layout based on the module name
 * Enables multi layout system
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Controller
 * @namespace App_Controller_Plugin_Layout
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Controller_Plugin_Layout_Module
 */
class App_Controller_Plugin_Layout_Module extends Zend_Controller_Plugin_Abstract {

    /**
     * Changes the Layout based on the module name
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        Zend_Layout::getMvcInstance()->setLayout($request->getModuleName());
    }
}
?>
