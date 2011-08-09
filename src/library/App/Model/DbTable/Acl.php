<?php
/**
 * Database Model for the Table "user_acl_rules" containing defined Users
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
 * @class App_Model_DbTable_Acl
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class App_Model_DbTable_Acl extends Zend_Db_Table_Abstract {

    /**
     * Table name
     * @var string
     */
    protected $_name = 'user_acl_rules';
    /**
     * primary key
     * @var string
     */
    protected $_primaray = 'uaru_id';


    /**
     * Get the ResourceId for a module/controller
     *
     * Get the Id from module/controller combination from the databse
     *
     * @param string $module
     * @param string $controller
     * @param bool $loadVirtual
     * @return int|false
     * @todo refactor: return the row(use fetchRow and not fetchAll),
     *       check where used and change value handling there too
     */
    public function getResourceId($module, $controller, $loadVirtual = NULL)
    {
        $stmt = $this->getAdapter()->select();
        $stmt->from('user_acl_modulecontroller', 'uamc_id')
             ->where('uamc_module = ?', $module)
             ->where('uamc_controller = ?', $controller);
        IF($loadVirtual === TRUE) {
            $stmt->where('uamc_virtual = ?', 1);
        }
        $result = $this->getAdapter()->fetchAll($stmt);

        IF(count($result) === 1) {
            RETURN $result[0]['uamc_id'];
        }

        RETURN FALSE;
    }

    /**
     * Get the parent roles
     *
     * @param int $roleId
     * @return array
     * @todo remove uar_inherit from the roles. legacy code, when only 1 role inheritance was possible!
     *       Also delete this from the Db (user_acl_roles)
     * @todo rewrite
     */
    public function getParentRoles($roleId)
    {
        $stmt = $this->getAdapter()->select();
        $stmt->from('user_acl_role_inherits')
             ->joinLeft('user_acl_roles', 'uar_id = uari_uar_inherit', array('uar_id', 'uar_name'))
             ->where('uari_uar_id = ?', $roleId, Zend_Db::INT_TYPE);

//        $stmt = $this->getAdapter()->select();
//        $stmt->from(array('a' => 'user_acl_roles', array()))
//             ->joinLeft(array('b' => 'user_acl_roles'), 'a.uar_inherit = b.uar_id')
//             ->where('a.uar_id = ?', $roleId, Zend_Db::INT_TYPE)
//             ->where('a.uar_inherit != 0');

        $result = $this->getAdapter()->fetchAll($stmt);
        RETURN $result;
    }


    /**
     * Get the associated rules for the resource and roles
     *
     * @param int $resourceId
     * @param array $roles array with App_Acl_Roles
     * @return array
     * @todo rewrite
     */
    public function getRules($resourceId, $roles)
    {
        IF(is_numeric($resourceId) === FALSE) {
            RETURN array();
        }

        $roleIds = array();

        FOREACH($roles AS $role) {
            IF(in_array($role->getRoleId(), $roleIds, TRUE) === FALSE) {
                $roleIds[] = $role->getRoleId();
            }
        }

        $select = $this->getAdapter()->select();

        $stmt = $select->from('user_acl_rules', array('uaru_id', 'uaru_rule', 'uaru_uar_id'))
                     ->joinLeft('user_acl_actions', 'uaru_uaa_id = uaa_id', array('uaa_id', 'uaa_action'))
                     ->where('uaru_uamc_id = ?', $resourceId)
                     ->where('uaa_activated = 1')
                     ->where('uaru_uar_id IN (' . implode(', ', $roleIds) . ')');

        $result = $this->getAdapter()->fetchAll($stmt);
        RETURN $result;
    }

    /**
     * Get a role by the name
     *
     * @return
     * @deprecated
     */
    public function getRoleByName($name)
    {
        $stmt = $this->getAdapter()->select();
        $stmt->from('user_acl_roles')
             ->where('uar_name = ?', $name);

        RETURN $this->getAdapter()->fetchAll($stmt);
    }


}
?>
