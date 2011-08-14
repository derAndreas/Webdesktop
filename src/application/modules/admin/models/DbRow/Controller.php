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
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Model_DbRow_Controller
 * @extends App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Controller extends App_Model_DbRow_Abstract
{
    /**
     * Id of the controller
     * @var int
     */
    protected $id;
    /**
     * name of the module
     * @var string
     */
    protected $moduleName;
    /**
     * name of the controller
     * @var string
     */
    protected $controllerName;
    /**
     * flag if controller is en/disabled
     * @var int
     */
    protected $enabled;
    /**
     * flag if the controller is virtual or in the normal ZF path
     * @var int
     */
    protected $virtual;
    /**
     * discovering status between file and DB (is ctrl in db?)
     * @var int
     */
    protected $status;
    /**
     * Description of the controller
     * @var string
     */
    protected $description;

    protected $_transformColumnMap = array(
        'id'             => 'uamc_id',
        'moduleName'     => 'uamc_module',
        'controllerName' => 'uamc_controller',
        'enabled'        => 'uamc_activated',
        'virtual'        => 'uamc_virtual',
        'description'    => 'uamc_description',
        'status'         => self::ROW_DUMMY
    );

    protected $defaultDbColumns = array('moduleName', 'controllerName', 'enabled', 'description');
    protected $defaultJsonColumns = array('id', 'moduleName', 'controllerName', 'enabled', 'virtual', 'status', 'description');
}
?>
