<?php
/**
 * Database Model for the Table "user_users" containing defined Users
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_DbTable
 * @namespace Admin_Model_DbTable
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Model_DbTable_Users
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Users extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_users';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uu_id';

    /**
     * get all active users
     *
     * Users are active users, when there is no "deleted" date in the column
     * "uu_deleted". In the result exclude the password column.
     *
     * @return Zend_Db_Table_Rowset
     * @access public
     */
    public function findActiveUsers()
    {
        $select = $this->select();
        $select->from($this->_name,
                      array(
                        'uu_id',
                        'uu_ug_id',
                        'uu_username',
                        'uu_name',
                        'uu_email',
                        'uu_active',
                        'uu_deleted' //FIXME: needed? in where clause it is defined as 000000
                      )
                  )->where('uu_deleted = ?', '0000-00-00 00:00:00');

        RETURN $this->fetchAll($select);
    }

    /**
     * Get all users with their group
     *
     * Joins the table "user_groups"
     *
     * @return Zend_Db_Table_Rowset
     * @access public
     */
    public function fetchUsersWithGroup()
    {
        $select = $this->getAdapter()
                        ->select()
                        ->from($this->_name)
                        ->joinLeft('user_groups', 'uu_ug_id = ug_id');

        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Get for a specific user the row with group informations
     *
     * Joins "user_groups"
     *
     * @param int $userId
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function fetchUserWithGroup($userId)
    {
        $select = $this->getAdapter()->select()
                       ->from($this->_name)
                       ->joinLeft('user_groups', 'uu_ug_id = ug_id')
                       ->where('uu_id = ?', $userId);

        RETURN $this->getAdapter()->fetchRow($select);
    }

    /**
     * Fetch a user by its username
     *
     * Primarly used to check for twice used usernames.
     * Return only the id of the existing username row
     *
     * @param String $username
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function fetchRowByUserName($username)
    {
        $select = $this->select()
                       ->from($this->_name, array('uu_id'))
                       ->where('uu_username = ?', $username);
        RETURN $this->fetchRow($select);
    }

    /**
     * Update a group
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param array $data
     * @param int $userId
     * @return int number of affected rows
     * @overwrite Zend_Db_Table_Abstract::update()
     * @todo rename to an independent name, like updateById() to free the normal ZF method
     *       from customization
     */
    public function update($data, $userId)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('uu_id = ?', $userId, Zend_Db::INT_TYPE));
    }

    /**
     * Update a users password
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param string $password
     * @param int $userId
     * @return int number of affected rows
     * @overwrite Zend_Db_Table_Abstract::update()
     * @todo rename to an independent name, like updateById() to free the normal ZF method
     *       from customization
     */
    public function updatePassword($password, $userId)
    {
        RETURN parent::update(
            array(
                'uu_passwort' => $password
            ),
            $this->getAdapter()->quoteInto('uu_id = ?', $userId, Zend_Db::INT_TYPE)
        );
    }

}
?>
