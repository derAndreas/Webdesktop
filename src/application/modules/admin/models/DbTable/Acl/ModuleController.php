<?php
/**
 * Database Model for the Table "user_acl_modulecontroller" containing
 * defined module/controller combinations
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
 * @class Admin_Model_DbTable_Acl_ModuleController
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 */
class Admin_Model_DbTable_Acl_ModuleController extends Zend_Db_Table_Abstract {
    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'user_acl_modulecontroller';
    /**
     * primary key name
     *
     * @var string
     */
    protected $_primary = 'uamc_id';

    /**
     * Fetch all rows but ordered by module / controller
     *
     * This is only to keep the column names from the Application Controllers
     *
     * @param String $dirModule (default to 'ASC')
     * @param String $dirCtrl  (default to 'ASC')
     * @return Zend_Db_Table_Rowset
     */
    public function fetchAllOrderByModuleController($dirModule = 'ASC', $dirCtrl = 'ASC')
    {
        $stmt = $this->select()
                ->order('uamc_module ' . $dirModule)
                ->order('uamc_controller ' . $dirCtrl);
        RETURN $this->fetchAll($stmt);
    }

    /**
     * Find the pair modul/controller in th database
     *
     * @param string $modul
     * @param string $controllerName
     * @return Zend_Db_Table_Rowset
     * @access public
     */
    public function findbyName($moduleName, $controllerName)
    {
        $stmt = $this->select();
        $stmt->where('uamc_module = ?', $moduleName)
             ->where('uamc_controller = ?', $controllerName);
        RETURN $this->fetchAll($stmt);
    }

    /**
     * Filter an array of controllers against the databse and return just the new ones
     *
     * @param array $controllers Array of Admin_Model_DbRow_Controller Objects
     * @return array
     * @access public
     */
    public function filterExistingControllers(array $controllers)
    {
        $resources = array();

        FOREACH($controllers AS $controller) {
            IF($this->findbyName($controller->get('moduleName'), $controller->get('controllerName'))->count() === 0) {
                $resources[] = $controller;
            }
        }
        RETURN $resources;
    }

    /**
     * Update the activated column for a specific controller
     *
     * @param int $status
     * @param int $controllerId
     * @return int
     */
    public function updateActivated($status, $controllerId)
    {
        RETURN parent::update(
            array('uamc_activated' => $status),
            $this->getAdapter()->quoteInto('uamc_id = ?', $controllerId, Zend_Db::INT_TYPE)
        );
    }

    /**
     * Update controller
     *
     * Method overwritten so that quoting the ID Where clause in the ZF way.
     *
     * @param array $data
     * @param int $controllerId
     * @return int number of affected rows
     * @overwrite Zend_Db_Table_Abstract::update()
     * @todo rename to an independent name, like updateById() to free the normal ZF method
     *       from customization
     */
    public function update($data, $controllerId)
    {
        RETURN parent::update($data, $this->getAdapter()->quoteInto('uamc_id = ?', $controllerId, Zend_Db::INT_TYPE));
    }

    /**
     * Delete a specific row by its Id
     *
     * @param int $controllerId
     * @return int number of affected rows
     */
    public function deleteById($controllerId)
    {
        RETURN parent::delete($this->getAdapter()->quoteInto('uamc_id = ?', $controllerId, Zend_Db::INT_TYPE));
    }

    
}
?>
