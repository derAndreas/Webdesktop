<?php
/**
 * Database Model for the Table "user_groups" containing defined Groups
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_DbTable
 * @namespace Admin_Model_DbTable
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_DbTable_Groups
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Groups extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name    = 'user_groups';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'ug_id';

    /**
     * Maps the Group Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     * @todo check if used, seems like old stuff before Admin_Model_DbRow_* impementation
     * @deprecated
     */
    protected $cMap = array(
        'id'          => 'ug_id',
        'name'        => 'ug_name',
        'description' => 'ug_description'
    );

    /**
     * Get all groups with ID and Name Column
     *
     * @access public
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getGroups()
    {
        $select = $this->select()
                       ->from($this->_name, array('ug_id', 'ug_name'));
        
        RETURN $this->fetchAll($select);
    }

    /**
     * Get all Groups defined in the DB and for each group count the
     * users assigned to the group.
     *
     * @access public
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchGroupsWithUserCount()
    {
        $select = $this->getAdapter()->select()
                       ->from($this->_name, array('ug_id', 'ug_name', 'ug_description', 'count(uu_id) AS memberscount'))
                       ->joinLeft('user_users', 'uu_ug_id = ug_id', array())
                       ->group('ug_id');

        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Get all users that are assigned to a specific group
     * Return only the user IDs in the result not the complete user row
     *
     * @param int $groupId
     * @return Zend_Db_Table_Rowset_Abstract
     * @access public
     */
    public function fetchUsersAssignedToGroup($groupId)
    {
        $select = $this->getAdapter()->select()
                       ->from($this->_name, array())
                       ->joinLeft('user_users', 'uu_ug_id = ug_id', array('uu_id'))
                       ->where('uu_ug_id = ?', $groupId, zend_db::PARAM_INT);
        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Get one group by the name of the group
     *
     * This is primarly to ensure that not a groupname is used twice
     * in the system. Uses LOWER()
     *
     * @param string $name
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchRowByGroupName($name)
    {
        $select = $this->select()
                       ->from($this->_name, array('ug_id'))
                       ->where('LOWER(ug_name) = ?', strtolower($name));
        RETURN $this->fetchRow($select);
    }

    /**
     * Add a new group to the database
     *
     * @param string $name
     * @param string $description
     * @return int PK of inserted row
     * @todo rename to an independent name, like addGroup() or insertGroup()
     *       to free the normal ZF method from customization
     */
    public function insert($name, $description)
    {
        RETURN parent::insert(array(
            'ug_name'        => $name,
            'ug_description' => $description
        ));
    }

    /**
     * Update a group
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param array $data
     * @param int $groupId
     * @return int number of affected rows
     * @overwrite Zend_Db_Table_Abstract::update()
     * @todo rename to an independent name, like updateById() to free the normal ZF method
     *       from customization
     */
    public function update($data, $groupId)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('ug_id = ?', $groupId, Zend_Db::INT_TYPE));
    }
}
?>
