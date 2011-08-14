<?php
/**
 * Definition for a user in the application.
 *
 * Used in the backend for all ACL actions.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @namespace App_
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_User
 * @todo refactored a lot, but needs some polish
 */
class App_User extends App_Model_DbRow_Abstract {
    /**
     * id of the user
     * @var integer
     */
    protected $id;
    /**
     * the group integer value for this user
     * @var integer
     */
    protected $groupid;
    /**
     * the theme id (used for webdesktop)
     * @var integer
     */
    protected $themeid;
    /**
     * the wallpaper id (used for webdesktop)
     * @var integer
     */
    protected $wpid;
    /**
     * users username
     * @var string
     */
    protected $username;
    /**
     * users passwort hash
     * @var string
     */
    protected $password;
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
     * users enabled status
     * @var string
     */
    protected $enabled;
    /**
     * users deleted time
     * @var string
     */
    protected $deleted;
    /**
     * users bgcolor (used for webdesktop)
     * @var string
     */
    protected $bgcolor;
    /**
     * users fgcolor (used for webdesktop)
     * @var string
     */
    protected $fgcolor;
    /**
     * users transparency (used for webdesktop)
     * @var string
     */
    protected $transparency;
    /**
     * users WP position (used for webdesktop)
     * @var string
     */
    protected $wppos;
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


    protected $_transformColumnMap = array(
        'id'       => 'uu_id',
        'groupid'  => 'uu_ug_id',
        'themeid'  => 'uu_sth_id',
        'wpid'     => 'uu_swp_id',
        'username' => 'uu_username',
        'password' => 'uu_passwort',
        'name'     => 'uu_name',
        'email'    => 'uu_email',
        'enabled'  => 'uu_active',
        'deleted'  => 'uu_deleted',
        'bgcolor'  => 'uu_bgcolor',
        'fgcolor'  => 'uu_fgcolor',
        'transparency'  => 'uu_transparency',
        'wppos'    => 'uu_wpos',
        'groupname'=> self::ROW_DUMMY,
        'roles'    => self::ROW_DUMMY
    );
    protected $defaultDbColumns   = array();
    protected $defaultJsonColumns = array();

    /**
     * get the user id
     * @return integer
     */
    public function getId()
    {
        RETURN (int) $this->id;
    }
    /**
     * get the user full name
     * @return string
     */
    public function getName()
    {
        RETURN (string) $this->name;
    }
    /**
     * get the users email address
     * @return string
     */
    public function getEmail()
    {
        RETURN (string) $this->email;
    }
    /**
     * get the user groupd id
     * @return integer
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
     * update the whole dataset of the user by id
     *
     * @param array $data The userrow as an array from the DB
     * @return App_User $this
     */
    public function update($data)
    {
        IF(count($data) === 0) {
            throw new Exception('Could not update App_User, invalid user');
        }
        $dbUser  = new App_Model_DbTable_User;
        $dbGroup = new App_Model_DbTable_Group;
        $this->fromArray($data); // sets the whole userdata

        $groupRow = $dbGroup->find($this->get('groupid'));
        $this->set('groupname', $groupRow->current()->ug_name);

        $roles = array();
        FOREACH($dbUser->getRoleBinding($this->get('id'), $this->get('groupid')) AS $role) {
            $key         = $role['uar_id'];
            $roles[$key] = $role['uar_name'];
        }

        $this->setRole($roles);

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
