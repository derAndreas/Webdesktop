<?php
/**
 * Description of Role
 *
 * @author Andreas
 */
class App_Acl_Role implements Zend_Acl_Role_Interface {

    protected $roleId;
    protected $roleName;
    protected $parentRoles = array();

    /**
     * construct a new App_Acl_Role object
     *
     * @param int $id
     * @param string $name
     * @access public
     */
    public function __construct($id, $name)
    {
        $this->roleId = $id;
        $this->roleName = (string) $name;
    }

    /**
     * add a parent Role
     *
     * @param App_Acl_Role $role
     * @return App_Acl_Role $this
     * @access public
     */
    public function addParentRole(App_Acl_Role $role)
    {
        $this->parentRoles[$role->getRoleId()] = $role;

        RETURN $this;
    }

    /**
     * Get the Parent Role objects
     *
     * @return array
     * @access public
     */
    public function getParentRole()
    {
        RETURN (array) $this->parentRoles;
    }

    /**
     *
     * @return
     * @access public
     */
    public function hasParentRole()
    {
        RETURN (bool) count($this->parentRoles) > 0 ? FALSE : TRUE;
    }

    /**
     * Return the role id
     *
     * @return string
     * @access public
     */
    public function getRoleId()
    {
        RETURN (string) $this->roleId;
        #RETURN (string) $this->roleName;
    }

    /**
     * Get the role name
     *
     * @return string
     * @access public
     */
    public function getName()
    {
        RETURN (string) $this->roleName;
    }

    /**
     * Set the role name
     *
     * @param string $name
     * @return App_Acl_Role $this
     * @access public
     */
    public function setName($name)
    {
        $this->roleName = (string) $name;
        RETURN $this;
    }

}
?>
