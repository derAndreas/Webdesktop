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
    /**
     * ID of the user
     * @var int
     */
    protected $id;
    /**
     * ID of the group the user is assigned to
     * @var int
     */
    protected $groupid;
    /**
     * username
     * @var string
     */
    protected $username;
    /**
     * Realname of the user
     * @var string
     */
    protected $name;
    /**
     * Mailaddress of the user
     * @var string
     */
    protected $email;
    /**
     * salted Password hash
     * @var string
     */
    protected $passwort;
    /**
     * flag if user is en/disabled
     * @var int
     */
    protected $enabled;
    /**
     * If the user is "deleted" the timestamp in the db will be set
     * cannot really delete a user, because of reference to the id for
     * archiving data
     * @var string
     */
    protected $deleted;

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
