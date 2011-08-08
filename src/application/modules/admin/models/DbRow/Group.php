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
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_DbRow_Group
 * @extends App_Model_DbRow_Abstract
 * @todo see App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_Group extends App_Model_DbRow_Abstract {
    protected $id;
    protected $name;
    protected $description;
    protected $memberscount;
    /**
     * Maps the User Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array(
        'id'          => 'ug_id',
        'name'        => 'ug_name',
        'description' => 'ug_description',
        'memberscount'=> '_dummy_'
    );

    protected $defaultDbColumns = array('name', 'description');
    protected $defaultJsonColumns = array('id', 'name', 'description', 'memberscount');
}
?>
