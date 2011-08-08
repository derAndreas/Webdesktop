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
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_DbRow_Permission
 * @extends App_Model_DbRow_Abstract
 * @todo see App_Model_DbRow_Abstract
 * @todo maybe rename to Admin_Model_DbRow_Rule to fit the Db Layout
 */
class Admin_Model_DbRow_Permission extends App_Model_DbRow_Abstract {
    protected $id;
    protected $mcId;
    protected $aId;
    protected $roleId;
    protected $rule;
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
        'roleName'       => '_dummy_'
    );

    protected $defaultDbColumns = array('mcId', 'aId', 'roleId', 'rule');
    protected $defaultJsonColumns = array('aId', 'roleId', 'rule', 'roleName');
}
?>
