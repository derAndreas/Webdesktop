<?php
/**
 * Database Model for the Table "user_groups" containing defined Users
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage Model_DbTable
 * @namespace App_Model_DbTable
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Model_DbTable_Group
 * @extends Zend_Db_Table_Abstract
 * @todo refactor for consistency in the variable and method names
 *          - $stmt / $select for SQL Statements
 *          - find/fetch/get Method names for retrieving informations from the Db
 * @todo duplicate code with Admin_Model_DbTable_Group
 * @todo check if referenceMap is used
 */
class App_Model_DbTable_Group extends Zend_Db_Table_Abstract
{
    /**
     * Table name
     * @var string
     */
    protected $_name = 'user_groups';
    /**
     * primary key
     * @var string
     */
    protected $_primaray = 'ug_id';

    /**
     * Tables that depend on this table
     * @var array
     * @todo check if table reference and depending is used
     */
    protected $_dependentTables = array('App_Model_DbTable_User');
    /**
     * Reference to an other table
     * @var array
     * @todo check if table reference and depending is used
     */
    protected $_referenceMap = array(
        'GroupsUser' => array(
            'columns'           => array('ug_id'),
            'refTableClass'     => 'App_Model_DbTable_User',
            'refColumns'        => array('uu_ug_id')
        )
    );
}
?>
