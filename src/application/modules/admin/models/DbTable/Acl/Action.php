<?php
/**
 * Database Model for the Table "user_acl_actions" containing defined Actions
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
 * @class Admin_Model_Acl_ControllersActions
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_Action extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_actions';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uaa_id';

    /**
     * Find an action by the module/controller id and action name
     *
     * @param int $modControllId Module/Controller Id from table user_acl_modulecontroller
     * @param string $name the name of the action
     * @return Zend_Db_Table_Rowset
     * @access public
     */
    public function findByName($modControllId, $name)
    {
        $stmt = $this->select();
        $stmt->where('uaa_uamc_id = ?', $modControllId, Zend_Db::INT_TYPE)
             ->where('uaa_action = ?', $name); //?? Zend_Db Class Constant for String Type??
        RETURN $this->fetchAll($stmt);
    }

    /**
     * Return all action Ids for a controller id
     * 
     * @param int $id
     * @return Zend_Db_Table_Rowset
     */
    public function findActionByControllerId($id)
    {
        $select = $this->select()
                       ->from($this->_name)
                       ->where('uaa_uamc_id = ?', $id, Zend_Db::PARAM_INT);
        RETURN $this->fetchAll($select);
    }

    /**
     * Filter an array of actions against the database
     * for a specific module/controller and just return the new ones
     * 
     * @param int $modContrId
     * @param array $actions
     * @return array
     */
    public function filterExistingActions($modContrId, array $actions)
    {
        $resources = array();
        FOREACH($actions AS $action) {
            IF($this->findbyName($modContrId, $action->get('actionName'))->count() === 0) {
                $resources[] = $action;
            }
        }
        RETURN $resources;
    }

    /**
     * Update an Action
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param array $data
     * @param int $id
     * @return int number of affected rows
     */
    public function updateById($data, $id)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('uaa_id = ?', $id, Zend_Db::INT_TYPE));
    }

    /**
     * Update all actions from a specific controller
     *
     * The method updates all actions that reference in the column
     * 'uaa_uamc_id' to a specific controller.
     *
     * @param array $data
     * @param int $controllerId
     * @return int number of affected rows
     */
    public function updateWithControllerId($data, $controllerId)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('uaa_uamc_id = ?', $controllerId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete all actions for a specific controller
     *
     * The method deletes all actions that reference in the column
     * 'uaa_uamc_id' to a specific controller.
     *
     * @param int $controllerId
     * @return int number of affected rows
     */
    public function deleteByControllerId($controllerId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uaa_uamc_id = ?', $controllerId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete a specific row by its Id
     *
     * @param int $actionId
     * @return int number of affected rows
     */
    public function deleteById($actionId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uaa_id = ?', $actionId, Zend_Db::INT_TYPE));
    }
}
?>
