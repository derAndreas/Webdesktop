<?php
/**
 * Database Model for the Table "user_acl_role_inherits" containing defined RolesInheritances
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
 * @class Admin_Model_DbTable_Acl_RoleInherit
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_RoleInherit extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_role_inherits';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uari_id';

    /**
     * Insert a new inherited role to the table
     * 
     * @param int $roleId
     * @param int $inheritRole
     * @return int primary key, that was inserted
     * @todo rename to an independent name, like insertInheritance() or insertRole()
     *       to free the normal ZF method from customization
     */
    public function insert($roleId, $inheritRole)
    {
        RETURN parent::insert(array(
            'uari_uar_id'      => $roleId,
            'uari_uar_inherit' => $inheritRole
        ));
    }

    /**
     * Get the roles ids, that are inherited as an array
     *
     * @param int $roleId
     * @return array of role PK Ids
     * @access public
     * @todo refactor the code: because only the IDs are fetched, write a select
     *       that only gets the IDs and not the complete Row
     */
    public function getInheritedRoles($roleId)
    {
        $return = array();
        $data   = $this->fetchAll($this->getAdapter()->quoteInto('uari_uar_id = ?', $roleId, Zend_Db::INT_TYPE));

        FOREACH($data AS $el) {
            $return[] = $el['uari_uar_inherit'];
        }

        RETURN $return;
    }

    /**
     * return full informations about inherited roles
     *
     * @param int $roleId
     * @return array
     * @access public
     */
    public function getRoleInheritance($roleId)
    {
        $stmt = $this->getAdapter()->select()
                     ->from('user_acl_roles', array('uar_id', 'uar_name', 'uar_description'))
                     ->joinLeft($this->_name, 'uari_uar_inherit = uar_id')
                     ->where('uari_uar_id = ?', $roleId);

        RETURN $this->getAdapter()->fetchAll($stmt);
    }

    /**
     * Delete all inheritances for a specific role
     *
     * If a role is deleted use this method to delete all roles that extend
     * the deleted role
     *
     * @param int $roleId
     * @return int number of deleted rows
     */
    public function deleteWithRoleId($roleId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uari_uar_id = ?', $roleId, Zend_Db::INT_TYPE));
    }
}
?>
