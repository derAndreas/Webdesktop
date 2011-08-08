<?php
/**
 * Definition of a controller in the administration context
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
 * @class Admin_Model_DbRow_Controller
 * @extends App_Model_DbRow_Abstract
 * @todo see App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Controller extends App_Model_DbRow_Abstract {
    protected $id;
    protected $moduleName;
    protected $controllerName;
    protected $enabled;
    protected $virtual;
    protected $status;
    protected $description;
    /**
     * Maps the Controller Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array(
        'id'             => 'uamc_id',
        'moduleName'     => 'uamc_module',
        'controllerName' => 'uamc_controller',
        'enabled'        => 'uamc_activated',
        'virtual'        => 'uamc_virtual',
        'description'    => 'uamc_description',
        'status'         => '_dummy_'
    );

    protected $defaultDbColumns = array('moduleName', 'controllerName', 'enabled', 'description');
    protected $defaultJsonColumns = array('id', 'moduleName', 'controllerName', 'enabled', 'virtual', 'status', 'description');
}
?>
