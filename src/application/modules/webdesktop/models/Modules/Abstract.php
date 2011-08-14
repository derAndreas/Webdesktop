<?php
/**
 * Abstract Module definition
 *
 * This is the abstract layer for every module in the webdesktop.
 * Because of the coupling between modules and the webdesktop (front to backend)
 * every module, that should be usuable for the users must extend from this
 * abstraction.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_Modules
 * @namespace Webdesktop_Model_Modules
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_Model_Modules_Abstract
 */
abstract class Webdesktop_Model_Modules_Abstract {
    /**
     * Constant, to place the module in the
     * startmenu (toolbar side)
     *
     * @var string
     */
    const MENUPATH_TOOLBAR  = 'toolmenu';
    /**
     * Constant, to place the module in the
     * startmenu under programs
     *
     * @var string
     */
    const MENUPATH_PROGRAMS = 'programs';
    /**
     * Name of the Module
     * The modulename is the name that is in the title of windows,
     * startmenu entry, windowbar,...
     *
     * @var string
     */
    protected $name;
    /**
     * Identifier of the module
     *
     * The identifier used in the front and backend.
     * SHould be string without special chars and unique.
     * Also placed in DbTables modules in the column m_moduuleid
     *
     * @var string
     */
    protected $id;
    /**
     * Description of the module (plain text only!)
     *
     * Will be used on tooltips
     *
     * @var string
     */
    protected $description;
    /**
     * A version identifier of a module
     * @var string
     */
    protected $version;
    /**
     * classname of the js to init the js module
     *
     * @var string
     * @deprecated
     */
    protected $className;
    /**
     * About array, can contain the following elements
     *  - author
     *  - url
     *  - email
     *  - description
     *
     * FUTURE IMPLEMENTATION!
     *
     * @var array
     */
    protected $about = array();
    /**
     * array of actions that are available in the module
     * an action is a param for an AJAX req and is also registered in the
     * ACL Database to check if the user is allowed to do this action
     *
     * Property needs to be public, so that the ACL Scan Script can access
     * this var in the reflection due to unacessibility before php5.3(!?)
     *
     * @var array
     */
    public $actions = array();
    /**
     * active user privilges for the actions above
     *
     * Contain on productional use
     *   key: action => value: bool true/false
     *
     * @var array
     */
    protected $userPriviliges = array();
    /**
     * Startmenu Path in the app, default to programs
     * @var string
     */
    protected $startmenupath = self::MENUPATH_PROGRAMS;
    /**
     * the css icon class for this modules
     * @var string
     */
    protected $iconClass;
    /**
     * the css short icon class for this modules
     * @var string
     */
    protected $shortcutIconClass;
    /**
     * Contains the webdesktop config (webdesktop.ini)
     * 
     * @var Zend_Config
     */
    protected $webdesktopConfig;
    /**
     * the request to handle params
     *
     * @var Zend_Controller_Request_Http
     */
    protected $request;

    /**
     * the response object
     * 
     * @var Zend_Controller_Response_Abstract
     */
    protected $response;
    /**
     * Abstract definition of Init Method
     * can be overwritten to preload some classes
     */
    public function init()
    {

    }
    /**
     * Get the name of the module
     *
     * @return string
     */
    public function getName()
    {
        RETURN (string) $this->name;
    }

    /**
     * Get he uniq Id of the module
     *
     * @return string
     */
    public function getId()
    {
        RETURN (string) $this->id;
    }

    /**
     * Get the available actions from the module
     *
     * @return array
     */
    public function getActions()
    {
        RETURN (array) $this->actions;
    }

    /**
     * Check if a module has the given action
     *
     * @param string $name
     * @return bool true|false
     */
    public function has($name)
    {
        IF(in_array($name, $this->actions, TRUE)) {
            RETURN TRUE;
        }

        RETURN FALSE;
    }

    /**
     * Get the JSON init Script for this module
     *
     * to launch the app we need the basic informations from a module.
     * those basic informations are id, name, launcher informations
     * and tell the app.js where to store the module in the menu and stuff like this.
     *
     * the return is a JSON encoded string to place this in the app startup
     *
     * @return string json encodeed
     */
    public function createInitScript()
    {
        $this->iconClass = (isset($this->iconClass)) ? $this->iconClass : $this->id .'-icon';
        $this->shortcutIconClass = (isset($this->shortcutIconClass)) ? $this->shortcutIconClass : $this->id .'-shortcut';


        RETURN array(
            'moduleId'   => $this->id,
            'controller' => $this->className,
            'menuPath' => $this->startmenupath,
            'launcher'   => array(
                'iconCls'         => $this->iconClass,
                'shortcutIconCls' => $this->shortcutIconClass,
                'text'            => $this->name,
                'tooltip'         => $this->description
            )
        );
    }

    /**
     * Get all Actions that this module has
     *
     * @return array
     * @access public
     */
    public function getModuleActions()
    {
        RETURN $this->actions;
    }

    /**
     * Get the module Id as a string
     *
     * @return string
     */
    public function getModuleId()
    {
        RETURN (string) $this->id;
    }

    /**
     * Set the privilige for a user for an action, which must be defined as action
     *
     * @param string $action
     * @param bool $allowed
     */
    public function setUserPrivilige($action, $allowed = FALSE)
    {
        IF(in_array($action, $this->actions, TRUE)) {
            $this->userPriviliges[$action] = (bool) $allowed;
        }
    }

    /**
     * Set multiple priviliges, see self::setUserPriviligeAction()
     *
     * @param array $priviliges
     * @see self::setUserPriviligeAction()
     */
    public function setUserPriviligesActions(array $priviliges)
    {
        FOREACH($priviliges AS $key => $val) {
            $this->setUserPrivilige($key, $val);
        }
    }

    /**
     * Set the webdesktop config with all needed paths
     *
     * @param Zend_Config $config
     */
    public function setWebDesktopConfig(Zend_Config $config)
    {
        $this->webdesktopConfig = $config;
    }

    /**
     * Set the actual http request from the user to this module to have acces
     * to the params
     *
     * @param Zend_Controller_Request_Http $request
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->request = $request;
    }

    public function setResponse(Zend_Controller_Response_Abstract $response)
    {
        $this->response = $response;
    }

    /**
     * Generate the returning Array for a json response
     *
     * This is just a shorthand to get the sucess property into the
     * array.
     *
     * @param array $data
     * @return array
     */
    public function responseSuccess($data = array())
    {
        IF(!is_array($data)) {
            $data = array($data);
        }
        RETURN array_merge(array('success' => TRUE), $data);
    }

    /**
     * Generate the returning Array for a json response
     *
     * This is just a shorthand to get the sucess property and error fields
     * into the array.
     *
     * @param string $message
     * @param array|String $errors
     * @return array
     */
    public function responseFailure($message, $errors)
    {
        IF(!is_array($errors)) {
            $errors = array($errors);
        }
        RETURN array(
            'success' => TRUE,
            'error'   => $message,
            'errors'  => $errors
        );
    }
}
?>
