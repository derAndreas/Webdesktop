<?php
/**
 * Definition of a user in the administration context
 *
 * Attention: Because Zend_Db::FETCH_INTO is not supported
 *            do not use this class as $_rowClass in the TableAbstraction
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_Model_DbRow
 * @namespace Admin_Model_DbRow
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_DbRow_Action
 * @extends App_Model_DbRow_Abstract
 * @todo see App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Action extends App_Model_DbRow_Abstract {
    protected $id;
    protected $controllerId;
    protected $moduleName;
    protected $controllerName;
    protected $actionName;
    protected $enabled;
    protected $status;
    protected $description;
    /**
     * Maps the Controller Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    
    protected $_transformColumnMap = array(
        'id'             => 'uaa_id',
        'mcId'           => 'uaa_uamc_id',
        'actionName'     => 'uaa_action',
        'enabled'        => 'uaa_activated',
        'description'    => 'uaa_description',
        'moduleName'     => '_dummy_',
        'controllerName' => '_dummy_',
        'status'         => '_dummy_'
    );

    protected $defaultDbColumns = array('mcId', 'actionName', 'enabled', 'description');
    protected $defaultJsonColumns = array('id', 'mcId', 'moduleName', 'controllerName', 'actionName', 'enabled', 'status', 'description');
}
?>
