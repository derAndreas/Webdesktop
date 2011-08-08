<?php
/**
 * Webdesktop Model Gateway to the App ACL.
 *
 * This is legacy code and needs to be merged to the App_Acl!
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_DbTable
 * @namespace Webdesktop_Model_DbTable
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop_Model_Acl
 * @todo Remove this Class and merge the functionality into App_Acl
 */
class Webdesktop_Model_Acl
{
    /**
     * Reference to App_Acl
     *
     * @var App_Acl
     */
    protected $acl;
    /**
     * Current User
     *
     * @var App_User
     */
    protected $user;

    /**
     * Construct the Webdesktop ACL
     *
     * @constructor
     */
    public function __construct()
    {
        $cache = Zend_Registry::get('Cache_Acl');
        $this->acl = $cache->load('acl_object');
        IF(!$this->acl) {
            $this->acl = new App_Acl;
        }
        $cache->save($this->acl, 'acl_object');
    }

    /**
     * Set the user for the ACL
     * 
     * @param App_User $user
     */
    public function setUser(App_User $user)
    {
        $this->user = $user;
    }
    
    /**
     * Get the user, which is bind to the acl (current user)
     * 
     * @return App_User
     */
    public function getUser()
    {
        RETURN $this->user;
    }

    /**
     * Check if the current user (self::$user) is allowed to
     * use the $module/$action
     *
     * @param string $module
     * @param string $action
     * @return bool
     */
    public function isAllowed($module, $action)
    {
        $resource = 'webdesktop/' . $module;

        // build rules on every call?
        $this->acl->buildResourceRules('webdesktop', $module, $action, $this->user, TRUE);
        $cache = Zend_Registry::get('Cache_Acl');
        $cache->save($this->acl, 'acl_object');

        FOREACH($this->user->getRoles() AS $roleId => $roleName) {
            IF($this->acl->isAllowed($roleId, $resource, $action)) {
                RETURN TRUE;
            }
            FOREACH($this->acl->getRole($roleId)->getParentRole() AS $roleId => $roleName) {
                IF($this->acl->isAllowed($roleId, $resource, $action)) {
                    RETURN TRUE;
                }
            }
        }

        RETURN FALSE;
    }
}
?>
