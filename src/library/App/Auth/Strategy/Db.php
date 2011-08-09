<?php
/**
 * Strategy to authenticate a user against the database
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Auth
 * @namespace App_Auth_Strategy
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Auth_Strategy_Db
 */
class App_Auth_Strategy_Db {
    /**
     * Zend_Form for this strategy
     * @var Zend_Form
     */
    protected $form;
    /**
     * reference link to the Auth_Adapter
     * @var App_Auth_Adapter
     */
    protected $adapter;

    /**
     * Authentication method
     *
     * @return Zend_Auth_Result
     * @todo Db Col in code, use something like Admin_Model_DbRow_User, but this needs
     *       to be rewritten to be in the App_ namespace
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

        $result = $internalAdapter->setIdentity($user)
                        ->setCredential($pass)
                        ->authenticate();
        
        IF($result->isValid()) {
            $data = (array) $internalAdapter->getResultRowObject();
            $this->adapter->getUser()->update($data['uu_id']);
        }
        
        RETURN new Zend_Auth_Result(
                $result->getCode(),
                $this->adapter->getUser(),
                $result->getMessages()
        );
    }

    /**
     * get the form for this authentication strategy
     *
     * creates the form (from App/Form/Auth/Login<strategy>
     *
     * @return Zend_Form
     */
    public function getForm()
    {
        IF($this->form === NULL && !$this->form instanceof Zend_Form) {
            $this->form = new App_Form_Auth_LoginDb;
        }

        RETURN $this->form;
    }

    /**
     * set the reference for the App_Auth_Adapter
     *
     * @param App_Auth_Adapter $adapter
     * @return App_Auth_Strategy_Db $this
     */
    public function setAdapter(App_Auth_Adapter $adapter)
    {
        $this->adapter = $adapter;
        RETURN $this;
    }
}
?>
