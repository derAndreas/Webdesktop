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
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Model_DbRow_Role
 * @extends App_Model_DbRow_Abstract
 * @todo see App_Model_DbRow_Abstract
 */
class Admin_Model_DbRow_User extends App_Model_DbRow_Abstract {
    protected $id;
    protected $groupid;
    protected $username;
    protected $name;
    protected $email;
    protected $passwort;
    protected $enabled;
    protected $deleted;
    /**
     * Maps the User Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array(
        'id'       => 'uu_id',
        'groupid'  => 'uu_ug_id',
        'username' => 'uu_username',
        'password' => 'uu_password',
        'name'     => 'uu_name',
        'email'    => 'uu_email',
        'enabled'  => 'uu_active'
    );

    protected $defaultDbColumns = array('groupid', 'username', 'name', 'email', 'enabled');
    protected $defaultJsonColumns = array('id', 'groupid', 'username', 'name', 'email', 'enabled');
}
?>
