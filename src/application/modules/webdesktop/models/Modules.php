<?php
/**
 * A collection of webdesktop modules
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_DbTable
 * @namespace Webdesktop_Model_DbTable
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_Model_ModuleSet
 * @deprecated
 * @todo old legacy code, dont know why this was implemented
 *       this can directly be handled in the Webdesktop Model (Webdesktop_Model_Webdesktop)
 *       Thats why deprecated (but not yet merged!)
 */
class Webdesktop_Model_Modules
{
    /**
     * Reference to Modules DbTable
     *
     * @var Webdesktop_Model_DbTable_Modules
     */
    protected $dbModules;
    /**
     * Reference to Launcher DbTable
     *
     * @var Webdesktop_Model_DbTable_Launchers
     */
    protected $dbLaunchers;
    /**
     * Reference to the Webdesktop ACL
     *
     * @var Webdesktop_Model_Acl
     */
    protected $acl;

    /**
     * Create an instance of this model
     *
     * @access public
     */
    public function __construct(Webdesktop_Model_Acl $acl)
    {
        $this->acl         = $acl;
        $this->dbModules   = new Webdesktop_Model_DbTable_Modules;
        $this->dbLaunchers = new Webdesktop_Model_DbTable_Launchers;
    }

    /**
     * get all modules, that are enabled for the user
     *
     * we use the ACL here instead of an DB internal table mapping, because
     * we need the acl and the acl rules later, if the user uses the module.
     * So the rules are stored in the cached ACL object if user calls an action
     * of a module and then we don't need to query to DB again.
     *
     * It's a little bit overhead here to work through all modules and check if
     * there is one avail. privilige, but we just have this load one time and
     * later only the cached object is used.
     *
     * @return Webdesktop_Model_Modules_ModuleSet
     * @access public
     */
    public function getAllUserModules()
    {
        $userModules = new Webdesktop_Model_ModuleSet;
        FOREACH($this->getAllModules(TRUE) AS $obj) {
            $rights = array();

            FOREACH($obj->getModuleActions() AS $action) {
                $rights[$action] = $this->acl->isAllowed($obj->getModuleId(), $action) === TRUE ? TRUE : FALSE;
            }

            IF(in_array(TRUE, $rights, TRUE)) {
                $obj->setUserPriviligesActions($rights);
                $userModules->add($obj);
            }

        }

        RETURN $userModules;
    }

    /**
     * Get all Modules from the database, which are enabled
     *
     * @param bool $onlyEnabled (default to TRUE)
     * @return Webdesktop_Model_ModuleSet
     */
    public function getAllModules($onlyEnabled = TRUE) 
    {
        $modules = new Webdesktop_Model_ModuleSet();
        $where = $onlyEnabled === TRUE ? 'm_enabled = 1' : NULL;

        FOREACH($this->dbModules->fetchAll($where) AS $module) {
            $name = $module->m_moduleId;
            $modules->add(new $module->m_classname);
        }
        RETURN $modules;
    }

    /**
     * Get all the launchers for the user that is registered in the ACL
     *
     * The launchers will be stored in the returning array with fixed configured
     * indexes. Can move the indexes to constants (constants through the whole
     * application). But this class seems to much overhead and should be merged
     * with the webdesktop model.
     *
     * @return array
     */
    public function getUserLaunchers()
    {
        $return = array(
            'autorun'     => array(),
            'contextmenu' => array(),
            'quickstart'  => array(),
            'shortcut'    => array()
        );

        FOREACH($this->dbLaunchers->findByUserId($this->acl->getUser()->getId()) AS $row) {
            SWITCH($row['l_type']) {
                CASE Webdesktop_Model_DbTable_Launchers::LAUNCHER_AUTORUN:
                    $return['autorun'][]     = $row['m_moduleId'];
                BREAK;
                CASE Webdesktop_Model_DbTable_Launchers::LAUNCHER_CONTEXT:
                    $return['contextmenu'][] = $row['m_moduleId'];
                BREAK;
                CASE Webdesktop_Model_DbTable_Launchers::LAUNCHER_QUICKSTART:
                    $return['quickstart'][]  = $row['m_moduleId'];
                BREAK;
                CASE Webdesktop_Model_DbTable_Launchers::LAUNCHER_SHORTCUT:
                    $return['shortcut'][]    = $row['m_moduleId'];
                BREAK;
            }
        }

        RETURN $return;
    }

    /**
     * prepare the module data for JSON output
     *
     * @return array
     * @access public
     * @deprecated
     * @todo removed, unused
     */
    public function prepareJson($what = FALSE)
    {
        SWITCH($what) {
            CASE 'initUserModules':
                RETURN $this->prepareJsonInitConfig($this->getAllUserModules());
            BREAK;
        }
    }

    /**
     * ???
     *
     * @param <type> $modules
     * @return string
     * @deprecated
     * @todo removed, unused
     */
    private function prepareJsonInitConfig($modules)
    {
        $return = array();
        FOREACH($modules AS $module) {
            $return[] = 'new WebDesk.' . $module->getId() . '()';
        }

        RETURN $return;
    }
}
?>
