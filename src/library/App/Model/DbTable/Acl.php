<?php
/**
 * Description of Acl
 *
 * @author Godfather
 */
class App_Model_DbTable_Acl extends Zend_Db_Table_Abstract {

    // use some acl tablename to get the table model work
    // easier for handling than fetching default db adapter from zend_registry
    protected $_name = 'user_acl_rules';

    /**
     * Get the Resources
     *
     * @return Zend_Db_Rowset
     * @access public
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
     * Get the parent role
     *
     * @param int $roleId
     * @return array
     * @access public
     * @todo remove uar_inherit from the roles. legacy code, when only 1 role inheritance was possible!
     *       Also delete this from the Db (user_acl_roles)
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
     * @access public
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
        #var_dump($stmt->__toString());
        RETURN $result;
    }

    /**
     *
     * @return
     * @access public
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
