<?php
/**
 * UserController to Add/Edit/Delete User
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @namespace Admin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_UserController
 * @extends Zend_Controller_Action
 */
class Admin_UserController extends Zend_Controller_Action {
     /**
     * Contains the DbTable Model for users
     *
     * @var Admin_Model_DbTable_Users
     * @access protected
     */
    protected $dbUser;
    /**
     * Contains the DbTable Model for groups
     *
     * @var Admin_Model_DbTable_Groups
     * @access protected
     */
    protected $dbGroup;

    /**
     * initial method, load models
     *
     * @access public
     */
    public function init()
    {
        $this->dbUser  = new Admin_Model_DbTable_Users();
        $this->dbGroup = new Admin_Model_DbTable_Groups();
    }
    /**
     * List all available users
     *
     * @view /views/scripts/user/index.phtml
     * @access public
     */
    public function indexAction()
    {
        $users  = array();
        $rUsers = array();

        IF($this->getRequest()->getParam('deleted', '') === 'show') {
            $rUsers = $this->dbUser->fetchAll();
        } ELSE {
            $rUsers = $this->dbUser->findActiveUsers();
        }

        FOREACH($rUsers AS $user) {
            $users[] = new Admin_Model_DbRow_User($user);
        }

        $this->view->users = $users;
    }

    /**
     * Add a user
     *
     * @view /views/scripts/user/add.phtml
     * @access public
     */
    public function addAction()
    {
        $groups = array();

        FOREACH($this->dbGroup->getGroups() AS $group) {
            $groups[] = new Admin_Model_DbRow_Group($group);
        }

        $form  = new Admin_Form_User_User($groups);

        IF($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $userRow = new Admin_Model_DbRow_User(array(
                'groupid'  => $this->getRequest()->getParam('group'),
                'name'     => $this->getRequest()->getParam('name'),
                'username' => $this->getRequest()->getParam('username'),
                'email'    => $this->getRequest()->getParam('mail'),
                'enabled'  => $this->getRequest()->getParam('active')
            ));
            $this->dbUser->insert($userRow->toDbArray());

            $this->_redirect('admin/user/index');

        } ELSEIF($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams()) === FALSE) {
            $form->setDescription($form->getErrorMessages());
        }

        $this->view->form = $form;
    }


    /**
     * Edit a user
     *
     * @view /views/scripts/user/edit.phtml
     * @access public
     */
    public function editAction()
    {
        $userRow = new Admin_Model_DbRow_User($this->dbUser->find($this->checkUserIdParam()));
        $groups  = array();

        FOREACH($this->dbGroup->getGroups() AS $group) {
            $groups[] = new Admin_Model_DbRow_Group($group);
        }
        $form = new Admin_Form_User_User($groups, $userRow, 'edit');

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $userRow->fromArray(array(
                    'groupid'  => $this->getRequest()->getParam('group'),
                    'name'     => $this->getRequest()->getParam('name'),
                    'username' => $this->getRequest()->getParam('username'),
                    'email'    => $this->getRequest()->getParam('mail'),
                    'enabled'  => $this->getRequest()->getParam('active')
                ));
                $this->dbUser->update($userRow->toDbArray(), $userRow->get('id'));

                $this->_redirect('admin/user/index');

            } ELSE {
                $form->setDescription('An unknown error occured');
            }
        }

        $this->view->form = $form;
    }


    /**
     * Delete a user (soft delete)
     *
     * @view /views/scripts/user/delete.phtml
     * @access public
     * @todo FIXME: The App_Form_Delete is missing somehow, this does not work
     * @todo DB Cols in the Controller, remove them through the DbRow_User class
     */
    public function deleteAction()
    {
        $userRow = new Admin_Model_DbRow_User($this->dbUser->find($this->checkUserIdParam()));
        $form = new App_Form_Delete($userRow); //FIXME: this file is missing

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams()) 
            && $form->getElement('del_checkbox')->isChecked() === TRUE) {

                //FIXME: Here we have some DB Columns in the controller
                //       Find a solution with the DbRow_User Class
                $this->dbUser->update(array(
                    'uu_deleted' => date('Y-m-d H:i:s', time())
                ), $this->dbUser->getAdapter()->quoteInto('uu_id = ?', $userRow->get('id'), Zend_Db::PARAM_INT));

                $this->_redirect('admin/user/index');
            } ELSE {
                $form->setDescription('Failed to delete the user');
            }
        }

        $this->view->form = $form;
        $this->view->user = $userRow;
    }

    /**
     * Change the password of an user
     *
     * @view /views/scripts/user/changepassword.phtml
     * @access public
     */
    public function changepasswordAction()
    {
        $userRow = new Admin_Model_DbRow_User($this->dbUser->find($this->checkUserIdParam()));
        $form    = new Admin_Form_Changepassword($userRow);

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams()) 
            && $this->getRequest()->getParam('pass1') === $this->getRequest()->getParam('pass2')) {

                $salt     = Zend_Registry::get('password_salt');
                $hashpass = md5($this->getRequest()->getParam('pass1') . $salt);
                // do the update
                $this->dbUser->updatePassword($hashpass, $userRow->get('id'));

                $this->_redirect('admin/user/index');
            } ELSE {
                $form->setDescription('Please fill both fields and ensure, that both passwords are equal');
            }
        }
        $this->view->form = $form;
    }

    /**
     * check if a valid user Id is given. if not throw an exception
     *
     * @access private
     * @throws Exception if no valid id is given
     * @return integer the Id
     */
    private function checkUserIdParam()
    {
        $id = $this->getRequest()->getParam('id');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbUser->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }
}
?>
