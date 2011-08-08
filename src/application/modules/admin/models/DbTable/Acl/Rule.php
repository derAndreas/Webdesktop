<?php
/**
 * Database Model for the Table "user_acl_rules" containing defined Rules
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_DbTable_Acl
 * @namespace Admin_Model_DbTable_Acl
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_DbTable_Acl_Rule
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_Rule extends Zend_Db_Table_Abstract {
    /**
     * Constant for the www side to specify the DENY status
     * @var int
     * @todo refactor: dont use a www and DB view on a status. the databse has an ENUM
     *       for rule column. Use a smaller and simpler INT with allow 0 in the rule column
     */
    const RULE_DENY  = 1;
    /**
     * Constant for the www side to specify the ALLOW status
     * @var int
     * @todo refactor: dont use a www and DB view on a status. the databse has an ENUM
     *       for rule column. Use a smaller and simpler INT with allow 0 in the rule column
     */
    const RULE_ALLOW = 2;
    /**
     * Constant for the DB side to specify the DENY status
     * @var string
     * @todo refactor: dont use a www and DB view on a status. the databse has an ENUM
     *       for rule column. Use a smaller and simpler INT with allow 0 in the rule column
     */
    const RULE_DB_DENY  = 'deny';
    /**
     * Constant for the DB side to specify the ALLOW status
     * @var string
     * @todo refactor: dont use a www and DB view on a status. the databse has an ENUM
     *       for rule column. Use a smaller and simpler INT with allow 0 in the rule column
     */
    const RULE_DB_ALLOW = 'allow';
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_rules';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primaray = 'uaru_id';

    /**
     * Return the rules for a given role. It is possible to filter to
     * specific module/controller Id with the second parameter or with the
     * third parameter to a specific action
     *
     * @param int $roleId
     * @param int $controllerId
     * @param int $actionId
     * @return Zend_Db_Table_Rowset
     */
    public function findRoleRules($roleId, $controllerId = NULL, $actionId = NULL)
    {
        $stmt = $this->select()
                     ->where('uaru_uar_id = ?', (int) $roleId);
        IF($controllerId !== NULL) {
            $stmt->where('uaru_uamc_id = ?', (int) $controllerId);
        }
        IF($actionId !== NULL) {
            $stmt->where('uaru_uaa_id = ?', (int) $actionId);
        }

        RETURN $this->fetchAll($stmt);
    }

    /**
     * Get all rules for a specific action
     *
     * @param int $actionId
     * @return Zend_Db_Table_RowSet_Abstract
     */
    public function fetchRulesForAction($actionId)
    {
        RETURN $this->fetchAll($this->getAdapter()->quoteInto('uaru_uaa_id = ?', $actionId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete all rules that are for a specific role
     *
     * @param int $roleId
     * @return int number of deleted rows
     */
    public function deleteWithRoleId($roleId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uaru_uar_id = ?', $roleId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete all rules that are for a specific controller and role
     *
     * @param int $controllerId
     * @param int $roleId
     * @return int number of deleted rows
     */
    public function deleteWithControllerRole($controllerId, $roleId)
    {
        RETURN parent::delete(array(
            $this->getAdapter()->quoteInto('uaru_uamc_id = ?', $controllerId, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto('uaru_uar_id = ?', $roleId, Zend_Db::INT_TYPE)
        ));
    }

    /**
     * Delete all rules that are for a specific action and role
     *
     * @param int $actionId
     * @param int $roleId
     * @return int number of deleted rows
     */
    public function deleteWithActionRole($actionId, $roleId)
    {
        RETURN parent::delete(array(
            $this->getAdapter()->quoteInto('uaru_uaa_id = ?', $actionId, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto('uaru_uar_id = ?', $roleId, Zend_Db::INT_TYPE)
        ));
    }

    /**
     * Delete all rules that are for a specific controller
     *
     * @param int $controllerId
     * @return int number of deleted rows
     */
    public function deleteByControllerId($controllerId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uaru_uamc_id = ?', $controllerId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete all rules that are for a specific action
     *
     * @param int $actionId
     * @return int number of deleted rows
     */
    public function deleteByActionId($actionId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uaru_uaa_id = ?', $actionId, Zend_Db::INT_TYPE));
    }

    /**
     * Add a new rule to the table
     * 
     * @param int $controllerId
     * @param int $actionId
     * @param int $roleId
     * @param string $rule (use class constacts RULE_DB_*)
     * @return int PK ID of inserted record
     */
    public function addRule($controllerId, $actionId, $roleId, $rule)
    {
        RETURN parent::insert(array(
            'uaru_uamc_id' => $controllerId,
            'uaru_uaa_id'  => $actionId,
            'uaru_uar_id'  => $roleId,
            'uaru_rule'    => $rule
        ));
    }
}
?>
