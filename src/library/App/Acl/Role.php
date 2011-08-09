<?php
/**
 * Definition of a Role in the application
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Acl
 * @namespace App_Acl
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Acl_Role
 */
class App_Acl_Role implements Zend_Acl_Role_Interface {

    /**
     * Id of the Role
     * @var int
     */
    protected $id;

    /**
     * Name of the role
     *
     * @var string
     */
    protected $name;

    /**
     * Parent roles of the current role
     *
     * @var array
     */
    protected $parentRoles = array();

    /**
     * construct a new App_Acl_Role object
     *
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = (string) $name;
    }

    /**
     * add a parent Role
     *
     * @param App_Acl_Role $role
     * @return App_Acl_Role $this
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
     */
    public function getParentRole()
    {
        RETURN $this->parentRoles;
    }

    /**
     * Return bool if Role has parent roles
     *
     * @return bool
     */
    public function hasParentRole()
    {
        RETURN count($this->parentRoles) > 0 ? FALSE : TRUE;
    }

    /**
     * Return the role id
     * Interface implementation
     * 
     * @return string
     */
    public function getRoleId()
    {
        RETURN $this->id;
    }

    /**
     * Get the role name
     *
     * @return string
     */
    public function getName()
    {
        RETURN $this->name;
    }

    /**
     * Set the role name
     *
     * @param string $name
     * @return App_Acl_Role $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        RETURN $this;
    }

}
?>
