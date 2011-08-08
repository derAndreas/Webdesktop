<?php
/**
 * API Controller for the virtual Desktop
 *
 * This is the main Controller to work with with modules in the webdesktop
 * environment. Every call to the backend should go through here to validate
 * the request against the ACL.
 *
 * After the permission for a request is granted, the ApiController
 * routes the request to the Webdesktop Module and Action with the parameters
 * _module and _action.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @namespace Webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop_ApiController
 * @extends Zend_Controller_Action
 * @todo see comments in the whole class: A lot of todo's here
 */
class Webdesktop_ApiController extends Zend_Controller_Action
{
    /**
     * Constant to define with which parameter the Module
     * should be identified and launched on the backend
     * @var string
     */
    const CALL_PARAM_MODULE_IDENT = '_module';
    /**
     * Constant to define with which parameter the action in
     * the module should be called and validated against the
     * permission ACL.
     * @var string
     */
    const CALL_PARAM_ACTION_IDENT = '_action';
    /**
     * Constant to define the error code for BADREQUEST
     * Follows the model of HTML Error Codes
     * @var int (value 400)
     */
    const REQUEST_ERROR_BADREQUEST = 400;
    /**
     * Constant to define the error code for AUTH Problem
     * Follows the model of HTML Error Codes
     * @var int (value 401)
     */
    const REQUEST_ERROR_AUTHENTICATION = 401;
    /**
     * Constant to define the error code for any unspecific problem
     * Follows the model of HTML Error Codes
     * @var int (value 400)
     */
    const REQUEST_ERROR_PRECONDITION = 412;

    /**
     * Current User
     *
     * @var App_User
     */
    protected $user;
    /**
     * Webdesktop Global configuration file (configs/webdesktop.ini)
     *
     * @var Zend_Config
     * @todo In the old design this was needed, check if with new design
     *       of the backend is still needed
     * @deprecated
     */
    protected $config;
    /**
     * Webdesktop Model
     *
     * @var Webdesktop_Model_Webdesktop
     * @todo In the old design this was needed, check if with new design
     *       of the backend is still needed
     * @deprecated
     */
    protected $model;
    /**
     * Cache used for ACL caching
     *
     * @var Zend_Cache
     */
    protected $cache;
    /**
     * The Webdesktop specific ACL. The ACL works with App_ACL in the same way
     * and depends on App_ACL
     *
     * @var Webdesktop_Model_Acl
     * @todo currently there is an own ACL module for the webdesktop
     *       To reduce complexity, move contents of Webdesktop_Model_Acl this to App_Acl
     */
    protected $acl;
    /**
     * Contains the value for the module parameter on each call.
     *
     * In every method in this ApiController the first step is to detect the call
     * parameters for module and action. The results where stored in the class variables
     * $module and $action.
     *
     * @var string
     */
    protected $module;
    /**
     * Contains the value for the action parameter on each call.
     *
     * In every method in this ApiController the first step is to detect the call
     * parameters for module and action. The results where stored in the class variables
     * $module and $action.
     *
     * @var string
     */
    protected $action;

    /**
     * initial function - directly called after constructir
     * loads at the beginning of a initiation of this controller
     *
     * @todo check if user or session is valid and return json error
     */
    public function init()
    {
        // disable layout and render, because this is just an API controller
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $auth         = Zend_Auth::getInstance();
        $this->user   = $auth->getIdentity();
        $this->config = new Zend_Config_Ini(Zend_Registry::get('appConfigPath') . 'webdesktop.ini');
        //$this->model  = new Webdesktop_Model_Webdesktop($this->user, $this->config); //@todo: see class variable documentation??
        $this->acl    = new Webdesktop_Model_Acl;
        $this->acl->setUser($this->user);
        
        // if no module is present, then we can not continue here
        IF($this->detectCallParameters(TRUE, FALSE) === FALSE) {
            RETURN $this->defaultResponses('Invalid call, need Module and Action defined', self::REQUEST_ERROR_PRECONDITION);
        }
    }
    /**
     * Index Action of Api Controller
     *
     * @access public
     */
    public function indexAction()
    {
        RETURN $this->defaultResponses('Cannot call Index', self::REQUEST_ERROR_BADREQUEST);
    }

    /**
     * Load a webmodule with JS and CSS Code
     *
     * This is legacy code of the old design and will be removed in the next releases.
     * With ExtJs3 there was no Loader for modules. With the new Class System,
     * the Ext.Loader and several other updates that came with ExtJs4 this is not
     * needed any more
     *
     * The method is not updated for ExtJs4 and will fail in any case!
     *
     * @access public
     * @deprecated
     * @todo remove
     */
    public function loadAction()
    {
        $modelDbModules = new Webdesktop_Model_DbTable_Modules;
        $module = $modelDbModules->findModuleById($this->module);
        
        IF($module->count() === 1) {
            $class = $module->current()->m_classname ;
            $obj = new $class;
            $load = $obj->preLoad();
            
            $privileges = array();
            FOREACH($load['actions'] AS $action) {
                $privileges[$action] = (bool) $this->acl->isAllowed($load['moduleId'], $action);
            }

            $load['actions'] = $privileges;

            $load['scripts'] = $obj->getFilesModuleJs($this->config->path->modules);

            $load['libs']['css'] = $obj->getFilesLibsCss($this->config->path->libraries);
            $load['libs']['js']  = $obj->getFilesLibsJs($this->config->path->libraries);
            

            $load['success'] = TRUE;

            $this->_helper->json->sendJson($load);

        } ELSE {
            RETURN $this->defaultResponses('Cannot load Module, module not found', self::REQUEST_ERROR_PRECONDITION);
        }
    }

    /**
     * Main request method
     *
     * Every call to a module/action should be routed through  this method, as
     * it is responsible for loading and ACL validating the call.
     *
     * It dynamicly load the right module class on the backend and passes the
     * request.
     *
     * @return array
     * @todo refactor: I think it can be removed -> self::detectCallParameters()
     * @todo refactor: remove the pass in of the config object
     * @todo refactor: use method chaining on $obj
     * @todo introduce Admin_Model_DbRow_Module to remove Db Column names
     */
    public function requestAction()
    {
        IF($this->detectCallParameters(TRUE, TRUE) === FALSE) {
            RETURN $this->defaultResponses('Invalid call, need Module and Action defined', self::REQUEST_ERROR_PRECONDITION);
        }

        $dbModules = new Webdesktop_Model_DbTable_Modules;
        $module    = $dbModules->findModuleById($this->module);
        
        IF($module->count() === 1) {
            try {
                $class = $module->current()->m_classname;
                $obj = new $class;
                IF($obj->has($this->action) === FALSE) {
                    throw new Exception('Action is not defined');
                }

                IF($this->acl->isAllowed($module->current()->m_moduleId, $this->action) === FALSE) {
                    RETURN $this->defaultResponses('No userrights to perform this action', self::REQUEST_ERROR_BADREQUEST);
                }

            } catch(Exception $e) {
                RETURN $this->defaultResponses($e->getMessage(), self::REQUEST_ERROR_PRECONDITION);
            }
            
            $obj->setWebDesktopConfig($this->config);
            $obj->setRequest($this->getRequest());
            $obj->setResponse($this->getResponse());
            $obj->init();
            
            try {
                $return = $obj->{$this->action . 'Action'}();
            } catch (Webdesktop_Model_Exception $e) {
                RETURN $this->defaultResponses($e->getMessage(), self::REQUEST_ERROR_PRECONDITION);
            }

            $this->_helper->json->sendJson($return);

        } ELSE {
            RETURN $this->defaultResponses('Cannot request Module, module not found', self::REQUEST_ERROR_PRECONDITION);
        }
    }

    /**
     * Detect the parameters from the request for a valid API call
     *
     * Parameter keywords are defined as constant, module and action parameters
     * are stored in $self:$module and self::$action.
     *
     * @param bool $hasModule does the module parameter needs to be present
     * @param bool $hasAction does the action parameter needs to be present
     * @return bool
     * @access private
     */
    private function detectCallParameters($hasModule = TRUE, $hasAction = FALSE)
    {
        #@todo remove var_dump($this->getRequest()->getParams());
        $module = $this->getRequest()->getParam(self::CALL_PARAM_MODULE_IDENT, NULL);
        $action = $this->getRequest()->getParam(self::CALL_PARAM_ACTION_IDENT, NULL);

        IF($hasModule === TRUE && is_string($module) && $module !== '') {
            $this->module = (string) $module;

            // check if we need an action and if its present
            IF($hasAction === TRUE) {
                IF(is_string($action) && $action !== '') {
                    $this->action = (string) $action;
                } ELSE {
                    RETURN FALSE;
                }
            }

            RETURN TRUE;
        }

        RETURN FALSE;
    }

    /**
     * method to return the default responses
     * 
     * @param string $msg
     * @param int $code
     * @access private
     * @todo The response system need more attention:
     *       Request can be dropped in the App_Plugin_Acl and here. Plus the
     *       frontend changed and it is not working smooth between Front-/Backend
     *       Rewrite the whole error handling and response to the frontend.
     *          - app.js for global Ajax Calls
     *          - new design of ExtJs4 error handling in Components (Ext.data.Error?)
     */
    private function defaultResponses($type = 'invalid', $code = self::REQUEST_ERROR_PRECONDITION)
    {
        $this->_helper->json->sendJson(array(
            'success' => FALSE,
            'code' => $code,
            'msg'   => $type
        ));

    }

}
