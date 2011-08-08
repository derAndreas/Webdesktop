<?php
/**
 * Description of Acl
 *
 * @author Andreas
 */
class App_Acl extends Zend_Acl {
    /**
     * stores the db model
     *
     * @var App_Model_DbTable_Acl
     * @access private
     */
    private $aclDbModel;
    /**
     * stores the loaded roles
     * 
     * @var array
     * @access private
     */
    private $roles = array();
    /**
     * stores the loaded resources
     * 
     * @var array
     * @access private
     */
    private $resources = array();

    /**
     *
     * @return
     * @access public
     */
    public function buildResourceRules($module, $controller, $action, App_User $user, $loadVirtual = NULL)
    {
        IF($this->aclDbModel === NULL) {
            $this->aclDbModel = new App_Model_DbTable_Acl;
        }
        $resourceName = $module . '/' . $controller;
        $roles = $this->loadRoles($user->getRoles());
        $rid = $this->loadResource($module, $controller, $resourceName, $loadVirtual);
        $rules = $this->aclDbModel->getRules($rid, $roles);
        #da($rid);
        #da($roles);
        #da($rules);
        #exit;
        FOREACH($rules AS $rule) {
            #da("\$this->{$rule['uaru_rule']}({$rule['uaru_uar_id']}, {$resourceName}, {$rule['uaa_action']})");
            $this->{$rule['uaru_rule']}($rule['uaru_uar_id'], $resourceName, $rule['uaa_action']);
        }
        #exit;
    }

    /**
     * Load a resource into the ACL
     *
     * If the resource is not already loaded, load the resource into the ACL
     * and retrieve the Id of this resource from the database.
     *
     * @return null|int
     * @access private
     * @todo: what if resource does not exists? break here?
     */
    private function loadResource($module, $controller, $resourceName, $loadVirtual = NULL)
    {
        $resourceId   = $this->aclDbModel->getResourceId($module, $controller, $loadVirtual);
        
        IF($this->has($resourceName)) {
            RETURN $resourceId;
        }
        
        $resource = new App_Acl_Resource($resourceId, $resourceName);
        $this->add($resource);
        RETURN $resourceId;
    }

    /**
     * load the roles into the ACL
     *
     * @param array $roles
     * @return array with loaded App_Acl_Role
     * @access private
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
     *
     * @return
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
     *
     * @return
     * @access public
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
