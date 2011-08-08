<?php
/**
 * Description of User
 *
 * @author Andreas
 */
class App_User {
    /**
     * id of the user
     * @var integer
     */
    protected $id;
    /**
     * users name
     * @var string
     */
    protected $name;
    /**
     * users e-mail
     * @var string
     */
    protected $email;
    /**
     * the group integer value for this user
     * @var integer
     */
    protected $group;
    /**
     * the group name
     * @var string
     */
    protected $groupname;
    /**
     * Stores the ACL roles, the user is included
     * @var array
     */
    protected $roles = array();

    /**
     * get the user id
     * @return integer
     * @access public
     */
    public function getId()
    {
        RETURN (int) $this->id;
    }
    /**
     * get the user full name
     * @return string
     * @access public
     */
    public function getName()
    {
        RETURN (string) $this->name;
    }
    /**
     * get the users email address
     * @return string
     * @access public
     */
    public function getEmail()
    {
        RETURN (string) $this->email;
    }
    /**
     * get the user groupd id
     * @return integer
     * @access public
     */
    public function getGroupId()
    {
        RETURN (int) $this->group;
    }
    /**
     * set the user id
     *
     * @param integer $id
     * @return App_User $this
     * @access public
     */
    public function setId($id)
    {
        IF(is_numeric($id)) {
            $this->id = (int) $id;
            RETURN $this;
        }
        throw new Zend_Exception('Cannot set App_User User id, because it is not an integer');
    }
    /**
     * set the user full name
     *
     * @parm string $name
     * @return App_User $this
     * @access public
     */
    public function setName($name)
    {
        IF(is_string($name)) {
            $this->name = (string) $name;
            RETURN $this;
        }
        throw new Zend_Exception('Cannot set App_User User name, because it is not a string');
    }
    /**
     * set the users email address
     *
     * @param string $mail
     * @return App_User $this
     * @access public
     * @todo check the user email format
     */
    public function setEmail($mail)
    {
        IF(is_string($mail)) {
            $this->email = (string) $mail;
            RETURN $this;
        }
        throw new Zend_Exception('Cannot set App_User User email, because it is not a string');
    }
    /**
     * set the user groupd id
     *
     * @param integer $groupid
     * @return App_User $this
     * @access public
     * @todo check if group exists or extend with a ::setGroup(App_Group $group) method
     */
    public function setGroupId($groupid)
    {
        IF(is_numeric($groupid)) {
            $this->group = (int) $groupid;
            RETURN $this;
        }
        throw new Zend_Exception('Cannot set App_User User Group id, because it is not an integer');
    }

    /**
     * set the user groupd name
     *
     * @param string $name
     * @return App_User $this
     * @access public
     */
    public function setGroupName($name)
    {
        IF(is_string($name)) {
            $this->groupname = (string) $name;
            RETURN $this;
        }
        throw new Zend_Exception('Cannot set App_User User Group name, because it is not a string');
    }

    /**
     * update the whole dataset of the user by its id
     *
     * @param integer $id database user id
     * @return App_User $this
     * @access public
     */
    public function update($id)
    {
        $id = (int) $id;
        $userTable = new App_Model_DbTable_User;
        $userSet = $userTable->find($id)->current();
        $groupSet = $userSet->findDependentRowset('App_Model_DbTable_Group')->current();
        $roleArray = $userTable->getRoleBinding($id, (int) $groupSet->ug_id);


        FOREACH($roleArray AS $role) {
            $roles[$role['uar_id']] = $role['uar_name'];
        }

        $this->setId($id)
             ->setName($userSet->uu_name)
             ->setGroupId($userSet->uu_ug_id)
             ->setGroupName($groupSet->ug_name)
             ->setEmail($userSet->uu_email)
             ->setRole($roles);

        RETURN $this;
    }
    /**
     * set the role(s) of this user
     *
     * Parameters are:
     *  a) use both, name ($role) and Id ($id)
     *  b) only $role, which must be an array if array(id => name, id2 => name2)
     *
     * @param string|array $role
     * @param int $id if $role param is a string, we can pass the id
     * @return App_User $this
     * @access public
     */
    public function setRole($role, $id = 0)
    {
        IF(is_string($role)) {
            $this->roles = array($id => $role);
        } ELSEIF(is_array($role)) {
            $this->roles = $role;
        } ELSE {
            throw new Zend_Exception('Invalid Role to set for the user. Must be String or Array');
        }

        RETURN $this;
    }

    /**
     *
     * @return
     * @access public
     */
    public function getRoles()
    {
        RETURN $this->roles;
    }
}
?>
