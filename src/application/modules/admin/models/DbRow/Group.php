<?php
/**
 * Definition of a group in the administration context
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
 * @class Admin_Model_DbRow_Group
 * @extends App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Group extends App_Model_DbRow_Abstract
{
    /**
     * If of the group
     * @var int
     */
    protected $id;
    /**
     * Name of the groups
     * @var string
     */
    protected $name;
    /**
     * Description of the group
     * @var string
     */
    protected $description;
    /**
     * members assigned to this group
     * @var int
     */
    protected $memberscount;

    protected $_transformColumnMap = array(
        'id'          => 'ug_id',
        'name'        => 'ug_name',
        'description' => 'ug_description',
        'memberscount'=> self::ROW_DUMMY
    );

    protected $defaultDbColumns = array('name', 'description');
    protected $defaultJsonColumns = array('id', 'name', 'description', 'memberscount');
}
?>
