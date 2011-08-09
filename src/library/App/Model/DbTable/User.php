<?php
/**
 * Database Model for the Table "user_users" containing defined Users
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage Model_DbTable
 * @namespace App_Model_DbTable
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Model_DbTable_User
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 * @todo duplicate code with Admin_Model_DbTable_User!
 * @todo check if referenceMap is used
 */
class App_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    /**
     * Table name
     * @var string
     */
    protected $_name = 'user_users';
    /**
     * primary key
     * @var string
     */
    protected $_primaray = 'uu_id';
    /**
     * Tables that depend on this table
     * @var array
     * @todo check if table reference and depending is used
     */
    protected $_dependentTables = array('App_Model_DbTable_Group');
    /**
     * Reference to an other table
     * @var array
     * @todo check if table reference and depending is used
     */
    protected $_referenceMap = array(
        'UserGroups' => array(
            'columns'           => array('uu_ug_id'),
            'refTableClass'     => 'App_Model_DbTable_Group',
            'refColumns'        => array('ug_id')
        )
    );


    /**
     * get the role id and name, where the user is member
     *
     * @param int $userId
     * @param int $groupId
     * @return array
     * @todo refactor, SQL String instead of Zend_Db_Statement
     */
    public function getRoleBinding($userId, $groupId)
    {
        $stmt = 'SELECT
                    uar_id,
                    uar_name
                 FROM
                    user_acl_role_member
                 LEFT JOIN user_acl_roles ON uarm_uar_id = uar_id
                 WHERE ((
                    uarm_type = "user"
                    AND
                    uarm_member_id = '.$userId.'
                 )
                 OR (
                    uarm_type = "group"
                    AND
                    uarm_member_id = '.$groupId.'
                 ))
                 GROUP BY uar_id

        ';

        RETURN $this->getAdapter()->fetchAll($stmt);
    }

    /**
     * Find a UserRow by by its username
     *
     * The username is search with a wildcard(?!, see Todo) => $username%
     *
     * @param string $name
     * @param int $start
     * @param int $limit
     *
     * @return Zend_Db_Table_Rowset_Abstract
     * @todo check if this method is used
     * @todo using wildcard to search for user seems odd. check if this is a proper
     *       way to do this in the db model!
     */
    public function findUsersByName($name, $start = 0, $limit = 10)
    {
        $query = strtolower($name . '%');
        $select = $this->getAdapter()->select()
                  ->from($this->_name, array('uu_id AS id', 'uu_name AS uname', 'uu_email AS mail'))
                  ->joinLeft('user_groups', 'uu_ug_id = ug_id', array('ug_name AS gname'))
                  ->where($this->getAdapter()->quoteInto('LOWER(uu_name) LIKE ?', $query))
                  ->order('uu_name ASC');
        RETURN $this->getAdapter()->fetchAll($select);
    }

}
?>
