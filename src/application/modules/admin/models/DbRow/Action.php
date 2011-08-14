<?php
/**
 * Definition of an action in the administration context
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
 * @class Admin_Model_DbRow_Action
 * @extends App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Action extends App_Model_DbRow_Abstract
{
    /**
     * Id of the action
     * @var int
     */
    protected $id;
    /**
     * Id of the controller for that action
     * @var int
     */
    protected $controllerId;
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
     * name of the action
     * @var string
     */
    protected $actionName;
    /**
     * flag if action is en/disabled
     * @var int
     */
    protected $enabled;
    /**
     * discovering status between file and DB (is action in db?)
     * @var int
     */
    protected $status;
    /**
     * Description of the action
     * @var string
     */
    protected $description;

    protected $_transformColumnMap = array(
        'id'             => 'uaa_id',
        'mcId'           => 'uaa_uamc_id',
        'actionName'     => 'uaa_action',
        'enabled'        => 'uaa_activated',
        'description'    => 'uaa_description',
        'moduleName'     => self::ROW_DUMMY,
        'controllerName' => self::ROW_DUMMY,
        'status'         => self::ROW_DUMMY
    );

    protected $defaultDbColumns = array('mcId', 'actionName', 'enabled', 'description');
    protected $defaultJsonColumns = array('id', 'mcId', 'moduleName', 'controllerName', 'actionName', 'enabled', 'status', 'description');
}
?>
