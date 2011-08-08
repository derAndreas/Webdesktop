<?php
/**
 * Description of adapter
 *
 * @author Andreas
 */
class App_Auth_Adapter implements Zend_Auth_Adapter_Interface {
    
    public $request;
    public $response;
    protected $strategies = array();
    protected $strategy;
    protected $user;

    /**
     *
     */
    public function __construct(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        $this->strategies = new Zend_Loader_PluginLoader(array(
            'App_Auth_Strategy' => 'App/Auth/Strategy'
        ));

        $this->request  = $request;
        $this->response = $response;

        $this->setStrategy($this->request->getParam('strategy', 'db'));
        $auth = Zend_Auth::getInstance();
        $this->user = $auth->getIdentity();
    }

    /**
     *
     * @return
     * @access public
     */
    public function authenticate()
    {
        IF($this->strategy !== NULL) {
            RETURN $this->strategy->authenticate();
        }

        RETURN FALSE;
    }

    /**
     *
     * @return
     * @access public
     */
    public function getUser()
    {
        RETURN $this->user;
    }

    /**
     *
     * @return
     * @access public
     */
    public function setStrategy($strategy)
    {
        $sClass = $this->strategies->load(ucfirst($strategy));
        $this->strategy = new $sClass();
        $this->strategy->setAdapter($this);

        RETURN $this;
    }
    /**
     *
     * @return
     * @access public
     */
    public function getForm()
    {
        RETURN $this->strategy->getForm();
    }
}
?>
