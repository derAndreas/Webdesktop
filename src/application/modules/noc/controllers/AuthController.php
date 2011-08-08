<?php
/**
 * Authentication Controller for the NOC
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Noc
 * @namespace Noc
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Noc_AuthController
 * @extends Zend_Controller_Action
 * @todo logout action
 */
class Noc_AuthController extends Zend_Controller_Action
{

    /**
     * Index actions forwards to LoginAction
     * @see self::loginAction()
     */
    public function indexAction()
    {
        $this->_forward('login');
    }

    /**
     * Action to login a user
     *
     * @return Mixed
     * @access public
     */
    public function loginAction()
    {
        $auth        = Zend_Auth::getInstance();
        $authAdapter = new App_Auth_Adapter($this->getRequest(), $this->getResponse());

        $form = $authAdapter->getForm();
        
        IF($this->getRequest()->isPost()) {
            $result = $auth->authenticate($authAdapter);
            
            IF($form->isValid($this->getRequest()->getParams()) && $result->isValid()) {
                $auth->getStorage()->write($authAdapter->getUser());
                $this->_redirect('member/index');
            } ELSE {
                $form->setDescription('The credentials you provided are not valid. Please check your input');
            }

        }

        $this->view->form = $form;
        RETURN $this->render('login');
    }

    /**
     * @todo implement logoutAction
     */
    public function logoutAction()
    {

    }


}



