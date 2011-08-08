<?php
/**
 * Database Model for the Table "user_acl_role_member" containing defined RoleMembers
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_DbTable_Acl
 * @namespace Admin_Model_DbTable_Acl
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Model_DbTable_Acl_RoleMember
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_RoleMember extends Zend_Db_Table_Abstract {
    /**
     * Constant that defines the member type user
     *
     * In the database the membertype is a string. To ensure
     * that nothing else is inserted to the databse except the two
     * defined member types (user/group) always reference to the typ
     * with those constants
     *
     * @var string
     * @todo use integers instead of strings, needs a larger inspection of code where it is used
     */
    const MEMBER_TYPE_USER  = 'user';
    /**
     * Constant that defines the member type group
     *
     * In the database the membertype is a string. To ensure
     * that nothing else is inserted to the databse except the two
     * defined member types (user/group) always reference to the typ
     * with those constants
     *
     * @var string
     * @todo use integers instead of strings, needs a larger inspection of code where it is used
     */
    const MEMBER_TYPE_GROUP = 'group';
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_role_member';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uarm_id';

    /**
     * Add a new row to the table
     *
     * @param int $roleId
     * @param int $memberId
     * @param string $type use the type definition with the class constants MEMBER_TYPE_*
     * @return int PK Id of inserted record
     * @todo rename to an independent name, like insertMember()
     *       to free the normal ZF method from customization
     */
    public function insert($roleId, $memberId, $type)
    {
        RETURN parent::insert(array(
            'uarm_uar_id'    => $roleId,
            'uarm_member_id' => $memberId,
            'uarm_type'      => $type
        ));
    }

    /**
     * Return all Members of the type 'user' that are assigned to specific role
     *
     * @param int $roleId
     * @return Zend_Db_Table_RowSet_Abstract
     */
    public function getRoleUsers($roleId)
    {
        $stmt = $this->getAdapter()->select()
                ->from($this->_name)
                ->joinLeft('user_users', 'uarm_member_id = uu_id')
                ->where('uarm_type ="user"') //FIXME: quote with class constant
                ->where('uarm_uar_id = ?', $roleId, Zend_db::INT_TYPE);
        RETURN $this->getAdapter()->fetchAll($stmt);
    }

    /**
     * Return all Members of the type 'group' that are assigned to specific role
     *
     * @param int $roleId
     * @return Zend_Db_Table_RowSet_Abstract
     */
    public function getRoleGroups($roleId)
    {
        $stmt = $this->getAdapter()->select()
                ->from($this->_name)
                ->joinLeft('user_groups', 'uarm_member_id = ug_id')
                ->where('uarm_type ="group"') //FIXME: quote with class constant
                ->where('uarm_uar_id = ?', $roleId, Zend_db::INT_TYPE);
        RETURN $this->getAdapter()->fetchAll($stmt);
    }


    /**
     * return an array with all IDs, who are bound to a role id for a specfic type
     *
     * @param int $roleId id of the role
     * @param string $type user or group as type to get the correct table
     * @return array
     * @access public
     * @todo dont fetch the complete rows, because only the IDs is used
     */
    public function getRoleBindingToId($roleId, $type)
    {
        $result = array();
        $stmt = $this->select();
        $stmt->from($this->_name)
             ->where('uarm_uar_id = ?', $roleId)
             ->where('uarm_type = ?', $type);

        FOREACH($this->fetchAll($stmt) AS $row) {
            $result[] = $row->uarm_member_id;
        }

        RETURN $result;
    }

    /**
     * return an array of "types", which are bound to a role
     *
     * @param int $roleId id of the role
     * @param string $type "user" or "group" as type to get the correct table
     * @return array
     * @access public
     * @todo the type is staticly configured, refactor to use the class constant
     */
    public function getRoleBindingTo($roleId, $type)
    {
        IF($type === 'group') {
            $joinTable = 'user_groups';
            $joinColumn = 'ug_id';
        } ELSEIF($type === 'user') {
            $joinTable = 'user_users';
            $joinColumn = 'uu_id';
        }

        $stmt = $this->getAdapter()->select();
        $stmt->from($this->_name)
             ->where('uarm_uar_id = ?', $roleId)
             ->where('uarm_type = ?', $type)
             ->joinLeft($joinTable, 'uarm_member_id = ' . $joinColumn);

        RETURN $this->getAdapter()->fetchAll($stmt);
    }

    /**
     * Get all Roles for a specific user he is assigned to
     *
     * @param int $userId
     * @return array
     */
    public function fetchBindedRolesToUser($userId)
    {
        $select = $this->getAdapter()->select()
                       ->from($this->_name)
                       ->joinLeft('user_acl_roles', 'uarm_uar_id = uar_id')
                       ->where('uarm_member_id = ?', $userId);

        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Delete all rows that are assigned to a specific role
     *
     * @param int $roleId
     * @return int number of affected rows
     */
    public function deleteWithRoleId($roleId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uarm_uar_id = ?', $roleId, Zend_Db::INT_TYPE));
    }
}
?>
