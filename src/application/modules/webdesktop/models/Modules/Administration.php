<?php
/**
 * WebDesktop Administration Module
 *
 * Manage the Users, Groups, Modules and Actions in the ACL
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
 * @class Webdesktop_Model_Modules_Administration
 * @extends Webdesktop_Model_Modules_Abstract
 */
class Webdesktop_Model_Modules_Administration extends Webdesktop_Model_Modules_Abstract
{
    protected $name              = 'Administration Module';
    protected $id                = 'administration';
    protected $description       = 'Module to change administrative settings and install new modules';
    protected $version           = 0.1;
    protected $className         = 'administration.Administration';
    protected $startmenupath     = Webdesktop_Model_Modules_Abstract::MENUPATH_PROGRAMS;
    protected $iconClass         = 'administration-icon';
    protected $shortcutIconClass = 'administration-shortcut';
    protected $about = array(
        'author'      => 'Andreas Mairhofer',
        'url'         => 'http://www.example.org',
        'email'       => 'demo@example.org',
        'description' => 'Administration of the WebDesktop'
    );
    /**
     * @todo Remove comments after everything is implemented
     * @var array
     */
    public $actions = array(
        'index',
        'loadUserUsers',                # used
        'saveNewUser',                  # used
        'saveEditUser',                 # used
        'saveDeleteUser',               # used
        'saveEditUserPw',               # used

        'loadGroupGroups',              # used
        'saveNewGroup',                 # used
        'saveEditGroup',                # used
        'saveDeleteGroup',              # used

        'loadRoleRoles',                # used
        'saveNewRole',                  # used
        'saveEditRole',                 # used
        'saveDeleteRole',               # used

        'loadControllerControllers',    # used
        'saveAddController',            # used
        'saveEditController',           # used
        'saveDeleteController',         # not implemented
        'changeControllerStatus',       # used
        'loadControllerPermissions',    # used
        'saveControllerPermissions',    # used

        'loadControllerActions',        # used
        'saveAddAction',                # used
        'saveEditAction',               # used
        'saveDeleteAction',             # used
        'changeActionStatus',           # used
        'loadActionPermissions',        # used
        'saveActionPermissions'         # used
    );

    protected $files = array(
        'css' => array(
            'style.css'
        ),
        'js' => array(
            'administration.js'
        )
    );

    /**
     * Load the Groups in the UserManagement Context
     *
     * Return only the Id and name for the group as an array
     *
     * @return array
     * @todo neeeded? I think can be removed
     * @deprecated
     */
    public function loadUserGroupsAction()
    {
        $groupdModel = new Admin_Model_DbTable_Groups;
        $return      = array();

        FOREACH($groupdModel->fetchAll() AS $group) {
            $return[] = array(
                'id'          => $group->ug_id,
                'name'        => $group->ug_name,
                'description' => $group->ug_description
            );
        }

        RETURN $return;
    }
    /**
     * Load all existing Users for the user listing
     *
     * @return array
     * @todo Add a column for enabled/deleted users (front/backend)
     */
    public function loadUserUsersAction()
    {
        $userModel  = new Admin_Model_DbTable_Users;
        $groupModel = new Admin_Model_DbTable_Groups;
        $return     = array();

        FOREACH($userModel->fetchAll() AS $userRow) {
            $user  = new Admin_Model_DbRow_User($userRow);
            $group = new Admin_Model_DbRow_Group($groupModel->find($user->get('groupid')));
            $return[] = array_merge($user->toJsonArray(), array('groupname' => $group->get('name')));
        }

        RETURN $this->responseSuccess(array(
            'users' => $return
        ));
    }
    /**
     * Create the new user in the Database
     *
     * Method does some checks before inserting the user to be sure
     * having valid data
     *
     * @return array
     */
    public function saveNewUserAction()
    {
        // create the validator map
        $validatorMap = array(
            'name' => array(
                'validators' => array(),
                'message' => 'Fullname cannot be emtpy'
            ),
            'username' => array(
                'validators' => array(),
                'message' => 'Username cannot be emtpy'
            ),
            'email' => array(
                'validators' => array(
                    'EmailAddress'
                ),
                'message' => 'E-Mailaddress is empty or not valid'
            ),
            'groupid' => array(
                'validators' => array(),
                'message' => 'Please select a group'
            )
        );

        $validate    = new App_Validate_Ajax;
        $groupModel  = new Admin_Model_DbTable_Groups;
        $userModel   = new Admin_Model_DbTable_Users;
        $groupRowSet = $groupModel->find($this->request->getParam('groupid', 0));
        $userRow     = $userModel->fetchRowByUserName($this->request->getParam('username', ''));

        IF($validate->isValid($this->request->getParams(), $validatorMap, TRUE) == FALSE || $groupRowSet->count() === 0 || $userRow) {
            $errors = $validate->getMessages();

            IF($groupRowSet->count() === 0) { $errors[] = 'No Group selected'; }
            IF($userRow)                    { $errors[] = 'Username already exists'; }

            RETURN $this->responseFailure('Failed Saving informations', $errors);

        } ELSE {
            $user = new Admin_Model_DbRow_User($this->request->getParams());
            $userModel->insert($user->toDbArray(array('groupid', 'username', 'password', 'name', 'email', 'enabled')));
            $user->fromArray($userModel->find($userModel->getAdapter()->lastInsertId()));
            RETURN $this->responseSuccess(array());
        }
    }
    /**
     * Update the User informations in the Database
     *
     * @return array
     */
    public function saveEditUserAction()
    {
        $validate   = new App_Validate_Ajax;
        $groupModel = new Admin_Model_DbTable_Groups;
        $userModel  = new Admin_Model_DbTable_Users;
        $groupRow   = new Admin_Model_DbRow_Group($groupModel->find($this->request->getParam('groupid', 0)));
        $userRow    = new Admin_Model_DbRow_User($userModel->find($this->request->getParam('id', 0)));
        $errors     = array();
        // create the validator map
        $validatorMap = array(
            'name' => array(
                'validators' => array(),
                'message' => 'Fullname cannot be emtpy'
            ),
            'username' => array(
                'validators' => array(),
                'message' => 'Username cannot be emtpy'
            ),
            'email' => array(
                'validators' => array(
                    'EmailAddress'
                ),
                'message' => 'E-Mailaddress is empty or not valid'
            ),
            'group' => array(
                'validators' => array(),
                'message' => 'Please select a group'
            )
        );



        IF(!$userRow->get('id')) {
            $errors[] = 'Unknown UserId';
        } ELSE {
            IF($userRow->get('username') !== $this->request->getParam('username', '')) {
                $dubUserName = $userModel->fetchRowByUserName($this->request->getParam('username', ''));
                IF($dubUserName) {
                    $errors[] = 'Username already exists';
                }
            }
        }
        IF(!$groupRow->get('id')) {
            $errors[] = 'Unknown or no group selected';
        }

        IF($validate->isValid($this->request->getParams(), $validatorMap, TRUE) == FALSE || count($errors) >  0) {
            RETURN $this->responseFailure('Failed Saving informations', array_merge($errors, $validate->getMessages()));
        } ELSE {
            $user = new Admin_Model_DbRow_User($this->request->getParams());
            $userModel->update($user->toDbArray(), $user->get('id'));
            RETURN $this->responseSuccess(array('users' => array($user->toJsonArray())));
        }
    }

    /**
     * Delete a user from the database
     *
     * @return array
     * @todo delete old acl role/rules that are bound to the deleted user
     */
    public function saveDeleteUserAction()
    {
        $userModel   = new Admin_Model_DbTable_Users;
        $userRow     = $userModel->find($this->request->getParam('id', 0));

        IF($userRow->count() !== 1) {
            RETURN $this->responseFailure('Failed Saving informations', 'Invalid Call. No User Id provided. Please reload and try again');
        } ELSE {
            $row = $userRow->current();
            $user = new Admin_Model_DbRow_User($row);
            $row->delete();
            RETURN $this->responseSuccess(array('users' => array($user->toJsonArray())));
        }
    }

    /**
     * Change the Password for the selected user
     *
     * Password is a salted hash, salt is defined in app config
     *
     * @return array
     */
    public function saveEditUserPwAction()
    {
        $userModel = new Admin_Model_DbTable_Users();
        $userRow   = new Admin_Model_DbRow_User($userModel->find($this->request->getParam('id')));

        IF($this->request->getParam('password_input', 1) === $this->request->getParam('password_confirm', 2) && $userRow->get('id')) {
            // the if uses differnt default values for getParam() so that null or '' cannot be set, if fields are not present
            $validate = new Zend_Validate();
            $validate->addValidator(
                new Zend_Validate_NotEmpty(),
                new Zend_Validate_StringLength(8)
            );

            IF($validate->isValid($this->request->getParam('password_input'))) {
                $userModel->updatePassword(md5($this->request->getParam('password_input') . Zend_Registry::get('password_salt')), $userRow->get('id'));

                RETURN $this->responseSuccess();

            } ELSE {
                $error = $validate->getMessages();
            }
        } ELSE {
            $error = array('Passwords are not the same or unkown user');
        }
        RETURN $this->responseFailure('Failed Saving informations', $error);

    }
    /**
     * Save a new created group in the Database
     *
     * @return array
     */
    public function saveNewGroupAction()
    {
        
        $params     = Zend_Json_Decoder::decode($this->request->getParam('groups'));
        $groupModel = new Admin_Model_DbTable_Groups;
        $groupRow   = $groupModel->fetchRowByGroupName(strtolower($params['name']));

        IF(!$groupRow) {
            $groupModel->insert($params['name'], $params['description']);
            RETURN $this->responseSuccess();
        }
        RETURN $this->responseFailure('Failed saving informations', 'The group name is already used');
    }
    /**
     * Load all groups for the group listing in the groups
     *
     * @return array
     */
    public function loadGroupGroupsAction()
    {
        $groupModel = new Admin_Model_DbTable_Groups;
        $groups     = $groupModel->fetchGroupsWithUserCount();
        $result     = array();

        FOREACH($groups AS $group) {
            $g = new Admin_Model_DbRow_Group($group);
            $result[] = $g->toJsonArray();
        }

        RETURN $this->responseSuccess(array(
            'groups' => $result
        ));
    }
    /**
     * Update the group informations in the database
     * a
     * @return array
     */
    public function saveEditGroupAction()
    {
        $params     = Zend_Json_Decoder::decode($this->request->getParam('groups'));
        $groupModel = new Admin_Model_DbTable_Groups;
        $groupRow   = new Admin_Model_DbRow_Group($groupModel->find($params['id']));
        $errors     = array();

        IF(strtolower($params['name']) !== strtolower($groupRow->get('name'))) {
            $dubGroupRow = $groupModel->fetchRowByGroupName($params['name']);
            IF($dubGroupRow) {
                $errors[] = 'The group already exists';
            }
        }

        IF($groupRow->get('id') && count($errors) === 0) {
            $groupRow->fromArray($params);
            $groupModel->update($groupRow->toDbArray(), $groupRow->get('id'));
            RETURN $this->responseSuccess(array(
                $groupRow->toDbArray()
            ));
        } 

        RETURN $this->responseFailure('Error editing the group', $errors);

    }

    /**
     * Delete a group from the database
     *
     * @return array
     * @todo if removing a group, check the role/rules and delete old role inherits / acl rules!
     */
    public function saveDeleteGroupAction()
    {
        $groupModel = new Admin_Model_DbTable_Groups;
        $groupRow   = $groupModel->find($this->request->getParam('id', 0));

        IF($groupRow->count() !== 1) {
            RETURN $this->responseFailure('Failed Saving informations', 'Invalid Call. No Group Id provided. Please reload and try again');
        } ELSE {
            $row = $groupRow->current();
            $row->delete();
            RETURN $this->responseSuccess();
        }
    }

    /**
     * Save a new Role in the database, if all tests passed
     *
     * @return array
     */
    public function saveNewRoleAction()
    {
        $pUsers       = Zend_Json_Decoder::decode($this->request->getParam('users', array()));
        $pGroups      = Zend_Json_Decoder::decode($this->request->getParam('groups', array()));
        $pRoles       = Zend_Json_Decoder::decode($this->request->getParam('roles', array()));
        $roleModel    = new Admin_Model_DbTable_Acl_Role;
        $roleMembers  = new Admin_Model_DbTable_Acl_RoleMember;
        $roleInherits = new Admin_Model_DbTable_Acl_RoleInherit;

        $roleRow      = $roleModel->fetchAll(
            $roleModel->select()->where('LOWER(uar_name) = ?', strtolower($this->request->getParam('name')))
        );

        IF($roleRow->count() === 0) {
            $roleRow = new Admin_Model_DbRow_Role(array(
                'name'        => $this->request->getParam('name', ''),
                'enabled'     => $this->request->getParam('enabled', 'false') == 'true' ? 1 : 0,
                'description' => $this->request->getParam('description', '')
            ));
            $roleModel->insert($roleRow->toDbArray());

            $lastInsertId = $roleModel->getAdapter()->lastInsertId();

            FOREACH($pGroups AS $group) {
                $roleMembers->insert($lastInsertId, $group, Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_GROUP);
            }

            FOREACH($pUsers AS $user) {
                $roleMembers->insert($lastInsertId, $user, Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_USER);
            }

            FOREACH($pRoles AS $inheritRole) {
                $roleInherits->insert($lastInsertId, $inheritRole);
            }

            RETURN $this->responseSuccess();
        }
        RETURN $this->responseFailure('Failed saving informations', 'This Rolename is already in use');
    }
    /**
     * Load all roles for roles listing
     *
     * @return array
     */
    public function loadRoleRolesAction()
    {
        $return           = array();
        $roleModel        = new Admin_Model_DbTable_Acl_Role;
        $roleMemberModel  = new Admin_Model_DbTable_Acl_RoleMember;
        $roleInheritModel = new Admin_Model_DbTable_Acl_RoleInherit;
        $roles            = $roleModel->fetchAll();

        FOREACH($roles AS $roleRow) {
            $users = $groups = $inherits = array();
            $role  = new Admin_Model_DbRow_Role($roleRow);

            FOREACH($roleMemberModel->getRoleUsers($roleRow['uar_id']) AS $userRow) {
                $user = new Admin_Model_DbRow_User($userRow);
                $users[] = $user->toJsonArray();
            }
            FOREACH($roleMemberModel->getRoleGroups($roleRow['uar_id']) AS $groupRow) {
                $group = new Admin_Model_DbRow_Group($groupRow);
                $groups[] = $group->toJsonArray();
            }
            FOREACH($roleInheritModel->getRoleInheritance($roleRow['uar_id']) AS $inheritRow) {
                $inherit = new Admin_Model_DbRow_Role($inheritRow);
                $inherits[] = $inherit->toJsonArray();
            }

            $return[] = array_merge($role->toJsonArray(), array(
                'users'    => $users,
                'groups'   => $groups,
                'inherits' => $inherits
            ));
        }

        RETURN $this->responseSuccess(array(
            'roles' => $return
        ));
    }
    /**
     * Update the informations for a selected role
     *
     * @return array
     */
    public function saveEditRoleAction()
    {
        $roleModel    = new Admin_Model_DbTable_Acl_Role;
        $roleMembers  = new Admin_Model_DbTable_Acl_RoleMember;
        $roleInherits = new Admin_Model_DbTable_Acl_RoleInherit;
        $userModel    = new Admin_Model_DbTable_Users;
        $groupModel   = new Admin_Model_DbTable_Groups;
        $roleRow      = new Admin_Model_DbRow_Role($roleModel->find($this->request->getParam('id', 0)));
        $pUsers       = Zend_Json_Decoder::decode($this->request->getParam('users', ''));
        $pGroups      = Zend_Json_Decoder::decode($this->request->getParam('groups', ''));
        $pRoles       = Zend_Json_Decoder::decode($this->request->getParam('roles', ''));
        $errors       = array();

        IF(strtolower($this->request->getParam('name', '')) !== strtolower($roleRow->get('name'))) {
            IF($roleModel->fetchRowByRoleName($this->request->getParam('name', ''))) {
                RETURN $this->responseFailure('Error saving informations', 'The role name is already used');
            }
        }

        // validate the posted users, groups and inherited roles
        IF(is_array($pUsers)) {
            $tmp = array();
            FOREACH($pUsers AS $user) {
                $u = $userModel->find($user);
                IF($u->count() === 1) {
                    $tmp[] = new Admin_Model_DbRow_User($u);
                }
            }
            $pUsers = $tmp;
        }

        IF(is_array($pGroups)) {
            $tmp = array();
            FOREACH($pGroups AS $group) {
                $g = $groupModel->find($group);
                IF($g->count() === 1) {
                    $tmp[] = new Admin_Model_DbRow_Group($g);
                }
            }
            $pGroups = $tmp;
        }

        IF(is_array($pRoles)) {
            $tmp = array();
            FOREACH($pRoles AS $role) {
                $r = $roleModel->find($role);
                IF($r->count() === 1) {
                    $tmp[] = new Admin_Model_DbRow_Role($r);
                }
            }
            $pRoles = $tmp;
        }


        IF($roleRow->get('id')) {
            $roleRow->fromArray(array(
                'name' => $this->request->getParam('name'),
                'description' => $this->request->getParam('description', ''),
                'enabled' => $this->request->getParam('enabled', 'false') == 'true' ? 1 : 0  //FIXME: somehow checkboxfield ignoriering inputValue, check for "TRUE"
                //  complex thing! Change in js role model type to string from int and the grid does not show the correct value
                // if js role model the field is int, then the form does not show the right state of the status
            ));
            $roleModel->update($roleRow->toDbArray(), $roleRow->get('id'));
            $roleMembers->deleteWithRoleId($roleRow->get('id'));
            $roleInherits->deleteWithRoleId($roleRow->get('id'));


            FOREACH($pGroups AS $group) {
                $roleMembers->insert($roleRow->get('id'), $group->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_GROUP);
            }

            FOREACH($pUsers AS $user) {
                $roleMembers->insert($roleRow->get('id'), $user->get('id'), Admin_Model_DbTable_Acl_RoleMember::MEMBER_TYPE_USER);
            }

            FOREACH($pRoles AS $inheritRole) {
                // do not add self row als inherit role, could cause loop in acl
                IF($inheritRole != $roleRow->get('id')) {
                    $roleInherits->insert($roleRow->get('id'), $inheritRole->get('id'));
                }
            }

            RETURN $this->responseSuccess();

        }

        RETURN $this->responseFailure('Error saving informations', 'Unknown Role ID. Editing not possible');
    }
    /**
     * Delete a role
     *
     * @return array
     */
    public function saveDeleteRoleAction()
    {
        $roleModel    = new Admin_Model_DbTable_Acl_Role;
        $roleMembers  = new Admin_Model_DbTable_Acl_RoleMember;
        $roleInherits = new Admin_Model_DbTable_Acl_RoleInherit;
        $ruleModel    = new Admin_Model_DbTable_Acl_Rule;

        $roleRow   = $roleModel->find($this->request->getParam('id', 0));
        IF($roleRow->count() !== 1) {
            RETURN $this->responseFailure('Failed saving informations', 'Invalid call. No valid role id provided');
        } ELSE {
            $row = $roleRow->current();
            $roleRow = new Admin_Model_DbRow_Role($roleRow->current());
            $row->delete();

            // delete the entries from other tables that refer to this role
            // members, inherited roles, ACL Rules!
            $roleMembers->deleteWithRoleId($roleRow->get('id'));
            $roleInherits->deleteWithRoleId($roleRow->get('id'));
            $ruleModel->deleteWithRoleId($roleRow->get('id'));

            RETURN $this->responseSuccess();
        }
    }
    /**
     * Load all Controllers and informations for each controller for a listing grid
     *
     * @return array
     */
    public function loadControllerControllersAction()
    {
        $controllerModel   = new Admin_Model_Acl_ControllersActions;
        $controllerDbModel = new Admin_Model_DbTable_Acl_ModuleController;
        $return            = array();
        $dbControllers     = array();

        FOREACH($controllerDbModel->fetchAll() AS $controllerRow) {
            $dbControllers[] = new Admin_Model_DbRow_Controller($controllerRow);
        }

        $scannedControllers = $controllerModel->getControllers();
        $newControllers     = $controllerDbModel->filterExistingControllers($scannedControllers);
        $deletedControllers = $controllerModel->filterVanishedControllers($dbControllers, $scannedControllers);

        FOREACH($dbControllers AS $controller) {
            $return[] = $controller->set('status', 0)->toJsonArray();
        }

        FOREACH($newControllers AS $controller) {
            $controller->fromArray(array(
                'enabled'     => 0,
                'status'      => 1,
                'description' => $controller->get('description', '')
            ));
            $return[] = $controller->toJsonArray();
        }
        
        FOREACH($deletedControllers AS $controller) {
            $controller->fromArray(array(
                'status'      => 2,
                'description' => $controller->get('description', '')
            ));
            $return[] = $controller->toJsonArray();
        }

        RETURN $this->responseSuccess(array(
            'controllers' => $return
        ));
    }
    /**
     * Update the status for a selected controller (enable/disable)
     *
     * @return array
     */
    public function changeControllerStatusAction()
    {
        $modContrModel = new Admin_Model_DbTable_Acl_ModuleController;

        $controllerRow = new Admin_Model_DbRow_Controller($modContrModel->find($this->request->getParam('id', 0)));
        IF($controllerRow->get('id')) {
            $modContrModel->updateActivated($controllerRow->get('enabled') == 0 ? 1 : 0, $controllerRow->get('id'));

            RETURN $this->responseSuccess(array(
                'controllers' => array(
                    $controllerRow->toJsonArray()
                )
            ));
        }
        RETURN $this->responseFailure('Failed saving information', 'No valid id provided');
    }

    /**
     * Save a new Controller to the database
     *
     * @return array
     */
    public function saveAddControllerAction()
    {
        $row = new Admin_Model_DbRow_Controller(array(
            'moduleName'     => $this->request->getParam('moduleName', ''),
            'controllerName' => $this->request->getParam('controllerName', ''),
            'enabled'        => $this->request->getParam('enabled', 'off') == 'on' ? 1 : 0,
            'virtual'        => $this->request->getParam('virtual', '0') == 1 ? 1 : 0,
            'description'    => $this->request->getParam('description', '')
        ));

        $modContrModel = new Admin_Model_DbTable_Acl_ModuleController;
        $modContrRow   = $modContrModel->findbyName($row->get('moduleName', ''), $row->get('controllerName', ''));

        IF($modContrRow->count() === 0) {
            $modContrModel->insert($row->toDbArray(array('moduleName', 'controllerName', 'enabled', 'virtual', 'description')));
            RETURN $this->responseSuccess(array(
                'data' => $row->toDbArray()
            ));
        } ELSE {
            RETURN $this->responseFailure('Failed adding Controller', 'Module/Controller already in the database');
        }
    }
    /**
     * Edit a Controller and save it in the database
     *
     * @return array
     */
    public function saveEditControllerAction()
    {

        $modContrModel = new Admin_Model_DbTable_Acl_ModuleController;
        $modContrRow   = $modContrModel->find($this->request->getParam('id', 0));

        IF($modContrRow->count() === 1) {
            $controllerRow = new Admin_Model_DbRow_Controller($modContrRow);
            $controllerRow->fromArray(array(
                'enabled'     => $this->request->getParam('enabled', 'off') === 'on' ? 1 : 0,
                'description' => $this->request->getParam('description', '')
            ));
            $modContrModel->update($controllerRow->toDbArray(), $controllerRow->get('id'));

            RETURN $this->responseSuccess();
            
        } ELSE {
            RETURN $this->responseFailure('failed saving informations', 'Controller Id is invalid');
        }

    }
    /**
     * Delete a Controller from the Database
     *
     * @return array
     */
    public function saveDeleteControllerAction()
    {
        $modContrModel = new Admin_Model_DbTable_Acl_ModuleController;
        $modContrRow   = $modContrModel->find($this->request->getParam('id', 0));

        IF($modContrRow->count() === 1) {
            // initiate action and role model, because they reference to module/controller
            $actionModel = new Admin_Model_DbTable_Acl_Action;
            $rulesModel  = new Admin_Model_DbTable_Acl_Rule;
            $modContrRow = new Admin_Model_DbRow_Controller($modContrRow->current());
            
            // delete all attached actions and rules
            $modContrModel->deleteById($modContrRow->get('id'));
            $actionModel->deleteByControllerId($modContrRow->get('id'));
            $rulesModel->deleteByControllerId($modContrRow->get('id'));

            RETURN $this->responseSuccess();

        } ELSE {
            RETURN $this->responseFailure('Failed deleting Controller', 'Controller Id is not valid');
        }
    }
    /**
     * Load the Actions for a specific Controller
     *  0 => Everything is fine, controller found as file and is in the Database (OK)
     *  1 => Controller found in file, but is not in database (NEW)
     *  2 => Controller found in database, but file reference vanished (DEL)
     *
     * @return array
     */
    public function loadControllerActionsAction()
    {
        $controllerDbModel  = new Admin_Model_DbTable_Acl_ModuleController;
        $actionDbModel      = new Admin_Model_DbTable_Acl_Action;
        $actionScanModel    = new Admin_Model_Acl_ControllersActions;
        $controllerRow      = new Admin_Model_DbRow_Controller($controllerDbModel->find($this->request->getParam('cid', 0)));

        $return = array();

        IF($controllerRow->get('id')) {
            $dbActions      = array();
            FOREACH($actionDbModel->findActionByControllerId($controllerRow->get('id')) AS $dbAction) {
                $dbActions[] = new Admin_Model_DbRow_Action($dbAction);
            }

            $scannedActions = $actionScanModel->getActions(
                    $controllerRow->get('moduleName'),
                    $controllerRow->get('controllerName'),
                    $controllerRow->get('virtual')
            );
            
            $newActions     = $actionDbModel->filterExistingActions($controllerRow->get('id'), $scannedActions);
            $vanishedActions= $actionScanModel->filterVanishedActions($dbActions, $scannedActions);

            FOREACH($dbActions AS $action) {
                $return[] = $action->fromArray(array(
                    'moduleName'     => $controllerRow->get('moduleName'),
                    'controllerName' => $controllerRow->get('controllerName'),
                    'status'         => '0'
                ))->toJsonArray();
            }

            FOREACH($newActions AS $action) {
                $return[] = $action->fromArray(array(
                    'moduleName'    => $controllerRow->get('moduleName'),
                    'controllerName'=> $controllerRow->get('controllerName'),
                    'enabled'       => 0,
                    'status'        => 1,
                ))->toJsonArray();
            }


            FOREACH($vanishedActions AS $action) {
                $return[] = $action->fromArray(array(
                    'moduleName'    => $controllerRow->get('moduleName'),
                    'controllerName'=> $controllerRow->get('controllerName'),
                    'status'        => 2
                ))->toJsonArray();
            }
        }

        RETURN $this->responseSuccess(array(
            'actions' => $return
        ));
    }
    /**
     * Add a new scanned action to db
     * @return array
     */
    public function saveAddActionAction()
    {
        $modContrModel = new Admin_Model_DbTable_Acl_ModuleController;
        $actionModel   = new Admin_Model_DbTable_Acl_Action;
        $modContrRow   = $modContrModel->findbyName($this->request->getParam('moduleName', ''), $this->request->getParam('controllerName', ''));
        $actionRow     = new Admin_Model_DbRow_Action($this->request->getParams());
        IF($modContrRow->count() === 1 && $actionRow->get('actionName')) {
            $modContrRow = new Admin_Model_DbRow_Controller($modContrRow->current());
            $actionRow->fromArray(array(
                'mcId'        => $modContrRow->get('id'),
                'actionName'  => $this->request->getParam('actionName'),
                'enabled'     => $this->request->getParam('enabled', 'off') == 'on' ? 1 : 0,
                'description' => $this->request->getParam('description', '')
            ));

            $actionModel->insert($actionRow->toDbArray());

            RETURN $this->responseSuccess($actionRow->toDbArray());

        } ELSE {
            RETURN $this->responseFailure('Failed Saving informations', 'Controller is not valid, cannot add action');
        }
    }

    /**
     * Delete an Action from the Database
     *
     * @return array
     * @todo fixme, currently unused
     */
    public function saveDeleteActionAction()
    {
        $actionModel = new Admin_Model_DbTable_Acl_Action;
        $actionRow   = new Admin_Model_DbRow_Action($actionModel->find($this->request->getParam('id', 0)));

        IF($actionRow->get('id')) {
            // delete all rules which are bound to this action
            $rulesModel = new Admin_Model_DbTable_Acl_Rule;
            $rulesModel->deleteByActionId($actionRow->get('id'));
            $actionModel->deleteById($actionRow->get('id'));

            RETURN $this->responseSuccess();

        } ELSE {
            RETURN $this->responseFailure('Failed Saving informations', 'Action Id is not valid');
        }
    }
    /**
     * Load the Permissions for a controller from the database
     *
     * @return array
     */
    public function loadControllerPermissionsAction()
    {
        $ruleModel   = new Admin_Model_DbTable_Acl_Rule;
        $roleModel   = new Admin_Model_DbTable_Acl_Role;
        $contrModel  = new Admin_Model_DbTable_Acl_ModuleController;
        $result      = array();

        $controller  = new Admin_Model_DbRow_Controller($contrModel->find($this->request->getParam('cId', 0)));
        IF($controller->get('id')) {
            FOREACH($roleModel->fetchAll() AS $r) {
                $role = new Admin_Model_DbRow_Role($r);
                $permissions = NULL;
                $permissions = $ruleModel->findRoleRules($role->get('id'), $controller->get('id'));
                $cIdent      = join("_", array($role->get('id'), $controller->get('id'), $controller->get('controllerName')));
                $result[] = array(
                    'ident'    => $cIdent,
                    'mcId'       => $controller->get('id'),
                    'roleName' => $role->get('name'),
                    'roleId'   => $role->get('id'),
                    'rule'     => -1
                );
            }
            RETURN $this->responseSuccess(array(
                'permissions' => $result
            ));
        } ELSE {
            #FIXME: seems broken
            RETURN $this->responseFailure('Failed loading informations', 'Controller Id is invalid');
        }
    }

    /**
     * Save the permissions for all actions of the given module/controller id
     *
     * @return array
     * @todo need some error handling and returning the error to grid
     *       there are threads open in the extjs forums, that no error handling on .sync() is really working
     */
    public function saveControllerPermissionsAction()
    {
        $contrModel = new Admin_Model_DbTable_Acl_ModuleController;
        $ruleModel  = new Admin_Model_DbTable_Acl_Rule;
        $roleModel  = new Admin_Model_DbTable_Acl_Role;
        $actionModel= new Admin_Model_DbTable_Acl_Action;
        $data       = Zend_Json::decode($this->request->getParam('permissions', array()));
        $return     = array();
        
        IF(!is_array($data) || !empty($data['mcId'])) {
            // if we have no array or the controller id is directly in the array
            // we nest the array in an array to get the foreach to work
            // extjs is sending object if only 1 row has changed and an array of object
            // if multiple changes occure
            $data = array($data);
        }
        FOREACH($data AS $el) {
            $role       = $roleModel->find($el['roleId']);
            $controller = $contrModel->find($el['mcId']);
            // not a controller provided or multiple controller found
            IF($controller->count() !== 1) {
                continue;
            }
            // not a roleId provided or multiple roles found
            IF($role->count() !== 1) {
                continue;
            }

            $controller = new Admin_Model_DbRow_Controller($controller->current());
            $role       = new Admin_Model_DbRow_Role($role->current());


            IF($el['rule'] == Admin_Model_DbTable_Acl_Rule::RULE_DENY) {
                $rule = Admin_Model_DbTable_Acl_Rule::RULE_DB_DENY;
            } ELSEIF($el['rule'] == Admin_Model_DbTable_Acl_Rule::RULE_ALLOW) {
                $rule = Admin_Model_DbTable_Acl_Rule::RULE_DB_ALLOW;
            } ELSE {
                $rule = NULL;
            }

            $ruleModel->deleteWithControllerRole($controller->get('id'), $role->get('id'));

            IF($rule !== NULL) {
                // select all actions from this controller, and set the rule
                FOREACH($actionModel->findActionByControllerId($controller->get('id')) AS $actionRow) {
                    $action = new Admin_Model_DbRow_Action($actionRow);
                    $ruleModel->addRule($controller->get('id'), $action->get('id'), $role->get(('id')), $rule);
                }
            }

            $return[] = array(
                'ident'    => join("_", array($role->get('id'), $controller->get('id'), $controller->get('controllerName'))),
                'mcId'     => $controller->get('id'),
                'roleName' => $role->get('name'),
                'roleId'   => $role->get('id'),
                'rule'     => $el['rule']
            );
        }


        RETURN array(
            'success' => TRUE,
            'message' => 'Successfully changed permissions', //FIXME: Test, remove later
            'permissions' => $return
        );
    }

    /**
     * Change the Status for an Action
     * 
     * @return array
     */
    public function changeActionStatusAction()
    {
        $actionModel = new Admin_Model_DbTable_Acl_Action;

        $actionRow = $actionModel->find($this->request->getParam('id', 0));
        IF($actionRow->count() === 1) {
            $actionRow = new Admin_Model_DbRow_Action($actionRow->current());
            $actionRow->fromArray(array(
                'enabled' => $actionRow->get('enabled') == 0 ? 1 : 0
            ));
            $actionModel->update($actionRow->toDbArray(array('enabled')), $actionRow->get('id'));

            RETURN $this->responseSuccess(array(
                'actions' => array(
                    'id' => $actionRow->get('id')
                )
            ));
        }
        RETURN $this->responseFailure('Failed saving informations', 'Could not find Action Id');
    }
    /**
     * Edit an acion of a controller
     *
     * Change status and description
     *
     * @return array
     */
    public function saveEditActionAction()
    {
        $actionModel = new Admin_Model_DbTable_Acl_Action;
        $actionRow   = $actionModel->find($this->request->getParam('id', 0));

        IF($actionRow->count() === 1) {
            $actionRow = new Admin_Model_DbRow_Action($actionRow->current());
            $actionRow->fromArray(array(
                'enabled'     => $this->request->getParam('enabled', 'off') == 'on' ? 1 : 0,
                'description' => $this->request->getParam('description', '')
            ));
            $actionModel->update($actionRow->toDbArray(array('enabled', 'description')), $actionRow->get('id'));

            RETURN $this->responseSuccess();
        } ELSE {
            RETURN $this->responseFailure('Failed saving informations', 'Could not find Action Id');
        }
    }

    /**
     * loadthe Permissions for an action
     *
     * @return array
     */
    public function loadActionPermissionsAction()
    {
        $ruleModel   = new Admin_Model_DbTable_Acl_Rule;
        $actionModel = new Admin_Model_DbTable_Acl_Action;
        $roleModel   = new Admin_Model_DbTable_Acl_Role;
        $result      = array();

        $action  = $actionModel->find($this->request->getParam('actionId', 0));
        IF($action->count() === 1) {
            $actionRow = new Admin_Model_DbRow_Action($action->current());

            FOREACH($roleModel->fetchAll() AS $role) {
                $role = new Admin_Model_DbRow_Role($role);
                $permissions = NULL;
                $permissions = $ruleModel->findRoleRules($role->get('id'), $actionRow->get('mcId'), $actionRow->get('id'));
                $aIdent      = join("_", array($role->get('id'), $actionRow->get('mcId'), $actionRow->get('id')));
                IF($permissions->count() > 0) {
                    $permissions = new Admin_Model_DbRow_Permission($permissions->current());
                    IF($permissions->get('rule') === Admin_Model_DbTable_Acl_Rule::RULE_DB_ALLOW) {
                        $rule = Admin_Model_DbTable_Acl_Rule::RULE_ALLOW;
                    } ELSEIF($permissions->get('rule') === Admin_Model_DbTable_Acl_Rule::RULE_DB_DENY) {
                        $rule = Admin_Model_DbTable_Acl_Rule::RULE_DENY;
                    } ELSE {
                        $rule = -1;
                    }
                    $permissions->set('rule', $rule);
                } ELSE {
                    $permissions = new Admin_Model_DbRow_Permission(array(
                        'mcId'     => $actionRow->get('mcId'),
                        'aId'      => $actionRow->get('id'),
                        'roleId'   => $role->get('id'),
                        'rule'     => 0,
                        'roleName' => $role->get('name')
                    ));
                }
                $result[] = array_merge(array(
                    'ident'    => $aIdent,
                    'roleName' => $role->get('name')
                ), $permissions->toJsonArray());
            }
            
            RETURN $this->responseSuccess(array('permissions' => $result));
        } ELSE {
            #FIXME: seems broken
            RETURN $this->responseFailure('Failed Loading informations', 'Action Id is invalid');
        }
    }

    /**
     * Save the Permission for an action
     *
     * @return array
     */
    public function saveActionPermissionsAction()
    {
        $ruleModel  = new Admin_Model_DbTable_Acl_Rule;
        $roleModel  = new Admin_Model_DbTable_Acl_Role;
        $actionModel= new Admin_Model_DbTable_Acl_Action;
        $data       = Zend_Json::decode($this->request->getParam('permissions', array()));
        $return     = array();

        IF(!is_array($data) || !empty($data['aId'])) {
            // if we have no array or the controller id is directly in the array
            // we nest the array in an array to get the foreach to work
            // extjs is sending object if only 1 row has changed and an array of object
            // if multiple changes occure
            $data = array($data);
        }
        FOREACH($data AS $el) {

            $role   = $roleModel->find($el['roleId']);
            $action = $actionModel->find($el['aId']);
            // not an action provided or multiple controller found
            IF($action->count() !== 1) {
                continue;
            }
            // not a roleId provided or multiple roles found
            IF($role->count() !== 1) {
                continue;
            }

            $action = new Admin_Model_DbRow_Action($action->current());
            $role   = new Admin_Model_DbRow_Role($role->current());


            IF($el['rule'] == Admin_Model_DbTable_Acl_Rule::RULE_DENY) {
                $rule = Admin_Model_DbTable_Acl_Rule::RULE_DB_DENY;
            } ELSEIF($el['rule'] == Admin_Model_DbTable_Acl_Rule::RULE_ALLOW) {
                $rule = Admin_Model_DbTable_Acl_Rule::RULE_DB_ALLOW;
            } ELSE {
                $rule = NULL;
            }

            $ruleModel->deleteWithActionRole($action->get('id'), $role->get('id'));

            IF($rule !== NULL) {
                $permission = new Admin_Model_DbRow_Permission(array(
                    'mcId'   => $action->get('mcId'),
                    'aId'    => $action->get('id'),
                    'roleId' => $role->get('id'),
                    'rule'   => $rule
                ));
                $ruleModel->insert($permission->toDbArray());
            }

            $return[] = array(
                'ident'    => join("_", array($role->get('id'), $action->get('mcId'), $action->get('id'))),
                'mcId'     => $action->get('mcId'),
                'aId'      => $action->get('id'),
                'roleName' => $role->get('name'),
                'roleId'   => $role->get('id'),
                'rule'     => $rule
            );
        }

        RETURN $this->responseSuccess(array('permissions' => $return));
    }
}
?>