<?php
/**
 * RoleController to manage the permissions
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @namespace Admin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_RoleController
 * @extends Zend_Controller_Action
 */
class Admin_RoleController extends Zend_Controller_Action {
    /**
     * contains the DbTable Role Model
     *
     * @var Admin_Model_DbTable_Acl_Role
     * @access protected
     */
    protected $dbRole;
    /**
     * contains the DbTable Role - Member Model relation table
     *
     * @var Admin_Model_DbTable_Acl_RoleMember
     * @access protected
     */
    protected $dbRoleMember;
    /**
     * contains the DbTable Role Inherit Model
     *
     * @var Admin_Model_DbTable_Acl_RoleInherit
     * @access protected
     */
    protected $dbRoleInherit;
    /**
     * contains the DbTable Users Model
     *
     * @var Admin_Model_DbTable_Users
     * @access protected
     */
    protected $dbUser;
    /**
     * contains the DbTable Group Model
     *
     * @var Admin_Model_DbTable_Groups
     * @access protected
     */
    protected $dbGroup;

    /**
     * init method, load models
     * 
     * @access public
     */
    public function init()
    {
        $this->dbRole        = new Admin_Model_DbTable_Acl_Role();
        $this->dbRoleMember  = new Admin_Model_DbTable_Acl_RoleMember();
        $this->dbRoleInherit = new Admin_Model_DbTable_Acl_RoleInherit();
        $this->dbUser        = new Admin_Model_DbTable_Users();
        $this->dbGroup       = new Admin_Model_DbTable_Groups();
    }

    /**
     * List all defined roles in the application
     *
     * @view /views/scripts/role/index.phtml
     * @access public
     */
    public function indexAction()
    {
        $roles = array();

        FOREACH($this->dbRole->fetchAll() AS $row) {
            $role = new Admin_Model_DbRow_Role($row);
            // Info: the following columns are not defined in Admin_Model_DbRow_Role, directly add to object
            $role->users  = count($this->dbRoleMember->getRoleBindingToId($role->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_USER));
            $role->groups = count($this->dbRoleMember->getRoleBindingToId($role->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_GROUP));
            $role->roles  = count($this->dbRoleInherit->getInheritedRoles($role->get('id')));
            $roles[] = $role;
        }
        $this->view->roles = $roles;
    }

    /**
     * Create a new Role
     *
     * After role saved into database, forward to editRole to add users/groups/roles
     *
     * @view /views/scripts/role/add.phtml
     * @access public
     */
    public function addAction()
    {

        $form = new Admin_Form_Role_Add;

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $role = new Admin_Model_DbRow_Role(array(
                    'name'        => $this->getRequest()->getParam('name'),
                    'description' => $this->getRequest()->getParam('description'),
                    'enabled'     => 0
                ));
                $this->dbRole->insert($role->toDbArray());
                // get the last insert id for redirect to edit mode
                $role->set('id', $this->dbRole->getAdapter()->lastInsertId());

                $this->_redirect('admin/role/edit/id/' . $role->get('id'));
            } ELSE {
                $form->addError('Please check if you filled the form correctly.');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edit a role and assign users and groups to this role
     *
     * @view /views/scripts/role/edit.phtml
     * @access public
     */
    public function editAction()
    {
        $roleRow   = new Admin_Model_DbRow_Role($this->dbRole->find($this->checkRoleIdParam()));
        $groups    = array();
        $users     = array();
        $inhterits = array();

        FOREACH($this->dbGroup->fetchAll() AS $row) {
            $groups[] = new Admin_Model_DbRow_Group($row);
        }

        FOREACH($this->dbUser->fetchAll() AS $row) {
            $users[] = new Admin_Model_DbRow_User($row);
        }

        FOREACH($this->dbRole->fetchAll() AS $row) {
            $inherit = new Admin_Model_DbRow_Role($row);
            IF($inherit->get('id') !== $roleRow->get('id')) {
                $inhterits[] = $inherit;
            }
        }
        $form     = new Admin_Form_Role_Edit($roleRow, $groups, $users, $inhterits);
        
        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $selectedGroups    = $form->getValue('groups');
                $selectedUsers     = $form->getValue('users');
                $roleInheritance   = $form->getValue('inherit');

                $this->dbRole->update(
                        $roleRow->toDbArray(array('name', 'description')),
                        $roleRow->get('id')
                );

                // delete current settings
                $this->dbRoleInherit->deleteWithRoleId($roleRow->get('id'));
                $this->dbRoleMember->deleteWithRoleId($roleRow->get('id'));

                // add the new setting

                FOREACH($roleInheritance AS $inherit) {
                    // dont insert "no inheritance" in the database or self as inheritance
                    IF($inherit == 0 || $inherit == $roleRow->get('id')) {
                        continue;
                    }
                    $this->dbRoleInherit->insert($roleRow->get('id'), $inherit);
                }

                FOREACH($selectedGroups AS $group) {
                    $this->dbRoleMember->insert($roleRow->get('id'), $group, Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_GROUP);
                }

                FOREACH($selectedUsers AS $user) {
                    $this->dbRoleMember->insert($roleRow->get('id'), $user, Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_USER);
                }
                $this->_redirect('admin/role/index');
            }
        }
        $form->getElement('groups')->setValue($this->dbRoleMember->getRoleBindingToId($roleRow->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_GROUP));
        $form->getElement('users')->setValue($this->dbRoleMember->getRoleBindingToId($roleRow->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_USER));
        $form->getElement('inherit')->setValue($this->dbRoleInherit->getInheritedRoles($roleRow->get('id')));
        $this->view->form = $form;
    }

    /**
     * change the status of this role (enable/disable)
     *
     * @view no view
     * @access public
     */
    public function statusAction()
    {
        $roleRow = new Admin_Model_DbRow_Role($this->dbRole->find($this->checkRoleIdParam()));

        IF($roleRow->get('id')) {
            $roleRow->set('enabled', $roleRow->get('enabled', 0) == 1 ? 0 : 1);
            $this->dbRole->update($roleRow->toDbArray(array('enabled')), $roleRow->get('id'));
        }
        
        $this->_redirect('admin/role/index');
    }

    /**
     * info card for this role
     *
     * @view /views/scripts/role/info.phtml
     * @access public
     * @todo show permissions to ressources
     */
    public function infoAction()
    {
        $roleRow = new Admin_Model_DbRow_Role($this->dbRole->find($this->checkRoleIdParam()));
        $groups   = array();
        $users    = array();
        $inherits = array();

        FOREACH($this->dbRoleMember->getRoleBindingTo($roleRow->get('id'), 'group') AS $row) {
            $groups[] = new Admin_Model_DbRow_Group($row);
        }
        FOREACH($this->dbRoleMember->getRoleBindingTo($roleRow->get('id'), 'user') AS $row) {
            $users[] = new Admin_Model_DbRow_User($row);
        }
        
        FOREACH($this->dbRoleInherit->getRoleInheritance($roleRow->get('id')) AS $row) {
            $inherits[] = new Admin_Model_DbRow_Role($row);
        }
        $this->view->role     = $roleRow;
        $this->view->groups   = $groups;
        $this->view->users    = $users;
        $this->view->inherits = $inherits;
    }

    /**
     * check if a valid role Id is given. if not throw an exception
     *
     * @access private
     * @throws Exception if no valid id is given
     * @return integer the Id
     */
    private function checkRoleIdParam()
    {
        $id = $this->getRequest()->getParam('id');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbRole->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }
}
?>
