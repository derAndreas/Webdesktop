<?php
/**
 * Database Model for the Table "user_acl_roles" containing defined Roles
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
 * @class Admin_Model_DbTable_Acl_Role
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_Role extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_roles';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uar_id';


    /**
     * Get one role by the name of the role
     *
     * This is primarly to ensure that not a rolename is used twice
     * in the system. Uses LOWER()
     *
     * @param string $name
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function fetchRowByRoleName($name)
    {
        $select = $this->select()
                       ->from($this->_name, array('uar_id'))
                       ->where('LOWER(uar_name) = ?', strtolower($name));
        RETURN $this->fetchRow($select);
    }

    /**
     * Update a Role
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param array $data
     * @param int $roleId
     * @return int number of affected rows
     * @overwrite Zend_Db_Table_Abstract::update()
     * @todo rename to an independent name, like updateById() to free the normal ZF method
     *       from customization
     */
    public function update($data, $roleId)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('uar_id = ?', $roleId, Zend_Db::INT_TYPE));
    }

    /**
     * Fetch all roles that are marked active in the database
     *
     * @return Zend_Db_Table_RowSet_Abstract
     */
    public function fetchActiveRoles()
    {
        RETURN $this->fetchAll($this->getAdapter()->quoteInto('uar_activated = ?', 1, Zend_Db::INT_TYPE));
    }

}
?>