<?php
/**
 * Description of Db
 *
 * @author Andreas
 */
class App_Auth_Strategy_Db {
    /**
     * contains the Zend_Form for this strategy
     *
     * @access protected
     * @var Zend_Form
     */
    protected $form;
    /**
     * reference link to the Auth_Adapter
     *
     * @access protected
     * @var App_Auth_Adapter
     */
    protected $adapter;
    /**
     * Authentication method
     *
     * @return Zend_Auth_Result
     * @access public
     */
    public function authenticate()
    {
        $user = $this->adapter->request->getParam('username');
        $pass = $this->adapter->request->getParam('passwort');
        
        $salt = Zend_Registry::get('password_salt');
        
        $internalAdapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter(),
            'user_users',
            'uu_username',
            'uu_passwort',
            'MD5(CONCAT(?, "' . $salt . '"))'
        );

        $internalAdapter->setIdentity($user)
                        ->setCredential($pass);

        $result = $internalAdapter->authenticate();

        IF($result->isValid()) {
            $data = (array) $internalAdapter->getResultRowObject();
            $this->adapter->getUser()->update($data['uu_id']);
        }
        
        RETURN new Zend_Auth_Result($result->getCode(), $this->adapter->getUser(), $result->getMessages());

    }

    /**
     * get the form for this authentication strategy
     *
     * creates the form (from App/Form/Auth/Login<strategy>
     *
     * @return Zend_Form
     * @access public
     */
    public function getForm()
    {
        IF($this->form === NULL && !$this->form instanceof Zend_Form) {
            $this->form = new App_Form_Auth_LoginDb;
        }

        RETURN $this->form;
    }

    /**
     * set the reference link for the App_Auth_Adapter
     *
     * @param App_Auth_Adapter $adapter
     * @return App_Auth_Strategy_Db $this for method chaining
     * @access public
     */
    public function setAdapter(App_Auth_Adapter $adapter)
    {
        $this->adapter = $adapter;
        
        RETURN $this;
    }

}
?>
