<?php
/**
 * Controller Plugin to hook in the ACL in the controller dispatching
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Plugin
 * @namespace App_Plugin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Plugin_Acl
 * @extends Zend_Controller_Plugin_Abstract
 * @todo set the response right: currently if there is an error somewhere (PHP error)
 *         - the error message is prepend to the php error in the response (makes it impossible to read on frontendside!)
 *         - on non xml requests: user is just routed to login page, without any information
 *          => critical Bug, needs to be fixed in the next releases (in preDispatch)
 * @todo ACL does not work after update of rules. Need to update (or delete) the ACL Cache after changing rules
 */
class App_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * Query the ACL if the user is allowed to be dispatched to the resource
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Exception if user is not allowed (handled by error controller)
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $resource   = $module . '/' . $controller;


        $auth = Zend_Auth::getInstance();

        IF($auth->hasIdentity() === TRUE) {
            $user = $auth->getIdentity();
        } ELSE {
            $user = new App_User;
            $user->setRole(Zend_Registry::get('acl_default_role_name'), Zend_Registry::get('acl_default_role_id'));
        }
        $auth->getStorage()->write($user);

        /**
         * load acl stuff from cache.
         * the acl is created, that it doesnot grab the data from the database again
         * so, we should have a little bit of performance here
         */
        /*
        //FIXME: ACL Caching seems be faulty or its the development process
        //       After changing rules, ACL doesn't match anymore
        //       Fix: After Changing roles/rules refresh the ACL Cache Object
        $cache = Zend_Registry::get('Cache_Acl');
        $acl   = $cache->load('acl_object');
        IF(!$acl) {
            $acl = new App_Acl;
        }
         */
        $acl = new App_Acl; // FIXME: remove after above is fixed
        $acl->buildResourceRules($module, $controller, $action, $user);

        // $cache->save($acl, 'acl_object'); // FIXME: enabled again after above problem is fixed
        
        FOREACH($user->getRoles() AS $roleId => $roleName) {
            IF($acl->isAllowed($roleId, $resource, $action)) {
                RETURN TRUE;
            }
            FOREACH($acl->getRole($roleId)->getParentRole() AS $roleId => $roleName) {
                IF($acl->isAllowed($roleId, $resource, $action)) {
                    RETURN TRUE;
                }
            }
        }

        /**
         * This part is critical (see todo in class docs)
         *
         * 1. On XML Requests:
         *      The setbody just adds information to the body. If an php error occure, the
         *      setBody just prepend the this error to the php error => the return is an Json/html mixed response, unreadable for Ajax Client
         * 2. normal HTTP resposen:
         *      anonymouse rerouting to login page, no reason or any notification to the user
         */
        IF($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setBody(Zend_Json_Encoder::encode(array('success' => FALSE, 'error_message' => 'No Right to execute this action')));
        } ELSEIF($controller !== 'error') {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoSimple('login', 'auth', 'noc');
        }
    }

}
?>
