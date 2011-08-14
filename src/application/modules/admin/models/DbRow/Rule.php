<?php
/**
 * Definition of a permission rule in the administration context
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
 * @class Admin_Model_DbRow_Rule
 * @extends App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Rule extends App_Model_DbRow_Abstract
{
    /**
     * Id of the rule
     * @var int
     */
    protected $id;
    /**
     * Module/Controller ID that rule is for
     * @var int
     */
    protected $mcId;
    /**
     * Action ID that rule is for
     * @var int
     */
    protected $aId;
    /**
     * Role Id
     * @var int
     */
    protected $roleId;
    /**
     * Allow/Deny rule
     * see Admin_Model_DbTable_Acl_Rule constants
     * @var String
     */
    protected $rule;
    /**
     * Name of the role (dummy)
     * @var string
     */
    protected $roleName;
    /**
     * Maps the Controller Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array(
        'id'             => 'uaru_id',
        'mcId'           => 'uaru_uamc_id',
        'aId'            => 'uaru_uaa_id',
        'roleId'         => 'uaru_uar_id',
        'rule'           => 'uaru_rule',
        'roleName'       => self::ROW_DUMMY
    );

    protected $defaultDbColumns = array('mcId', 'aId', 'roleId', 'rule');
    protected $defaultJsonColumns = array('aId', 'roleId', 'rule', 'roleName');
}
?>
