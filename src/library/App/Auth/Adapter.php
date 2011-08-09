<?php
/**
 * Definition of a ressource in the application
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Auth
 * @namespace App_Auth
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Auth_Adapter
 */
class App_Auth_Adapter implements Zend_Auth_Adapter_Interface {

    /**
     * @var Zend_Controller_Request_Http
     */
    public $request;
    /**
     *
     * @var Zend_Controller_Response_Http
     */
    public $response;

    /**
     * Available authentication strategies
     * @var array
     */
    protected $strategies = array();
    /**
     * The configured strategy configured in the app ini
     * @var string
     */
    protected $strategy;
    /**
     * The user performing the request
     * @var App_User
     */
    protected $user;

    /**
     * Constructor to setup the strategy
     */
    public function __construct(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        $this->strategies = new Zend_Loader_PluginLoader(array(
            'App_Auth_Strategy' => 'App/Auth/Strategy'
        ));

        $auth           = Zend_Auth::getInstance();
        $this->user     = $auth->getIdentity();
        $this->request  = $request;
        $this->response = $response;

        $this->setStrategy($this->request->getParam('strategy', 'db'));
    }

    /**
     * Authenticate the user trough the configured strategy
     *
     * @return bool
     */
    public function authenticate()
    {
        IF($this->strategy !== NULL) {
            RETURN $this->strategy->authenticate();
        }

        RETURN FALSE;
    }

    /**
     * Get the user that is going to be authenticated
     *
     * @return App_User
     */
    public function getUser()
    {
        RETURN $this->user;
    }

    /**
     * Set the stategy for authentication
     *
     * @return App_Auth_Adapter
     */
    public function setStrategy($strategy)
    {
        $sClass = $this->strategies->load(ucfirst($strategy));
        $this->strategy = new $sClass();
        $this->strategy->setAdapter($this);

        RETURN $this;
    }
    /**
     * Get the form for the current authentication
     *
     * @return App_Form_Auth_LoginDb
     */
    public function getForm()
    {
        RETURN $this->strategy->getForm();
    }
}
?>
