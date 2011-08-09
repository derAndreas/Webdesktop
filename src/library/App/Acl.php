<?php
/**
 * Main ACL to load the ressources and validate a request against the ACL
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @namespace App_Acl
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Acl
 * @extends Zend_Acl
 */
class App_Acl extends Zend_Acl {
    /**
     * Db Model to the ACL Table
     *
     * @var App_Model_DbTable_Acl
     */
    private $aclDbModel;
    /**
     * array for loaded roles
     * 
     * @var array
     */
    private $roles = array();
    /**
     * array the loaded resources
     * 
     * @var array
     */
    private $resources = array();

    /**
     * Build the ressource Rules for  module/controller/action combination
     *
     * Function load all needed informations and build the ACL (Zend_Acl))
     * for a validation. the generation is for a module/controller/action/user
     * combination, so not all rules for a controller or all users are loaded.
     *
     * This means, that the acl will get bigger for each user that is using the system,
     * but the ACL cache is a file-per-user system, so it should be fast if event
     * hundreds of rules for a user are available
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param App_User $user
     * @param bool $loadVirtual
     */
    public function buildResourceRules($module, $controller, $action, App_User $user, $loadVirtual = NULL)
    {
        IF($this->aclDbModel === NULL) {
            $this->aclDbModel = new App_Model_DbTable_Acl;
        }
        $rName = $module . '/' . $controller;
        $roles = $this->loadRoles($user->getRoles());
        $rid = $this->loadResource($module, $controller, $rName, $loadVirtual);
        $rules = $this->aclDbModel->getRules($rid, $roles);
        FOREACH($rules AS $rule) {
            $this->{$rule['uaru_rule']}($rule['uaru_uar_id'], $rName, $rule['uaa_action']);
        }
    }

    /**
     * Load a resource into the ACL
     *
     * If the resource is not already loaded, load the resource into the ACL
     * and retrieve the Id of this resource from the database.
     *
     * @return null|int
     * @todo: what if resource does not exists? break here?
     */
    private function loadResource($module, $controller, $resourceName, $loadVirtual = NULL)
    {
        $resourceId   = $this->aclDbModel->getResourceId($module, $controller, $loadVirtual);
        
        IF($this->has($resourceName) === FALSE) {
            $resource = new App_Acl_Resource($resourceId, $resourceName);
            $this->add($resource);
        }
        RETURN $resourceId;
    }

    /**
     * load the roles into the ACL
     *
     * @param array $roles
     * @return array with loaded App_Acl_Role
     * @todo check if storing the Zend_Acl_Roles in an extra class array is needed
     */
    private function loadRoles($roles)
    {
        FOREACH($roles AS $key => $val) {
            $roleSet = $this->aclDbModel->getParentRoles($key);
            
            $parent  = NULL;
            $role    = new App_Acl_Role($key, $val);
            
            $this->roles[$key] = $role; // @todo: needed?
            $roles[$key] = $role;
            IF(count($roleSet) > 0) {
                FOREACH($roleSet AS $row) {
                    $parent = new App_Acl_Role($row['uar_id'], $row['uar_name']);
                    $role->addParentRole($parent);
                    IF($this->hasRole($parent) === FALSE) {
                        $this->addRole($parent);
                    }
                    $this->roles[$row['uar_id']] = $parent; // @todo: needed?
                    $roles[$row['uar_id']] = $parent;
                }
            }
            IF($this->hasRole($role) === FALSE) {
                $this->addRole($role, $parent);
            }
        }
        RETURN (array) $roles;
    }

    /**
     * Get roles bound to a role
     *
     * @param App_User $user
     * @return array
     * @access public
     */
    public function getUserBoundRoles(App_User $user)
    {
        $roles = array();
        FOREACH($user->getRoles() AS $id => $name) {
            IF($this->hasRole($id)) {
                $roles[$id] = $name;
                IF($this->getRole($id)->hasParentRole()) {
                    FOREACH($this->getRole($id)->getParentRoles() AS $parent) {
                        $pId = $parent->getRoleId();
                        $pName = $parent->getName();
                        $roles[$pId] = $pName;
                    }
                }
            }
        }
        
        RETURN $roles;
    }

    /**
     * Find a role by name
     *
     * @return ??
     * @todo check if used and does this really makes sense?
     */
    public function findRoleByName($name)
    {
        IF($this->aclDbModel === NULL) {
            $this->aclDbModel = new App_Model_DbTable_Acl;
        }

        RETURN $this->aclDbModel->getRoleByName($name);
    }
}
?>
