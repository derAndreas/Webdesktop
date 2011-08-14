<?php
/**
 * Definition of a role in the administration context
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
 * @class Admin_Model_DbRow_Role
 * @extends App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Role extends App_Model_DbRow_Abstract {
    /**
     * ID of the role
     * @var int
     */
    protected $id;
    /**
     * Name of the role
     * @var string
     */
    protected $name;
    /**
     * Flag if role is en/disabled
     * @var int
     */
    protected $enabled;
    /**
     * Description of the role
     * @var string
     */
    protected $description;

    protected $_transformColumnMap = array(
        'id'          => 'uar_id',
        'name'        => 'uar_name',
        'enabled'     => 'uar_activated',
        'description' => 'uar_description'
    );

    protected $defaultDbColumns = array('name', 'enabled', 'description');
    protected $defaultJsonColumns = array('id', 'name', 'enabled', 'description');
}
?>
