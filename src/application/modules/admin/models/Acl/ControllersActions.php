<?php
/**
 * Model for Controllers and Actions
 *
 * This model is for scanning and detecting new or vanished controller/actions
 * in the application, which is the basis of the whole ACL system.
 * For the Webdesktop part, there are virtual Controllers, that are detected and
 * covered by Hooks.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Model_Acl
 * @namespace Admin_Model_Acl
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Model_Acl_ControllersActions
 * @todo performance optimizations possible here. very often iterating over arrays and check for object properties
 *       A way could be to create something like a array collection, that are bound to the *DbRow* classes
 */
class Admin_Model_Acl_ControllersActions {

    /**
     * Store all loaded hooks
     * @var array
     */
    private $hooks = array();

    /**
     * Constructor to load and init all hooks used
     */
    public function __construct()
    {
        $appConfig  = new Zend_Config_Ini(Zend_Registry::get('appConfigPath') . 'application.ini');
        
        IF(isset($appConfig->production->acl->controller->hooks)) {
            $hookConfig = $appConfig->production->acl->controller->hooks;
            $this->loadInitHooks($hookConfig);
        }
    }

    /**
     * Get all Controllers of all Modules in this application
     *
     * @return array
     * @access public
     * @todo refactoring needed, multiple nested iterations
     */
    public function getControllers()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $resources       = array();

        FOREACH($frontController->getControllerDirectory() AS $cDirectory) {
            $directory = new DirectoryIterator($cDirectory);
            $module = basename(dirname($cDirectory));

            IF($module === 'default' || $module === 'application') {
                $module = 'default';
            }

            WHILE($directory->valid()) {
                IF(preg_match('/^([A-Za-z0-9]+)Controller.php$/', $directory->getFilename(), $match)) {
                    $controller = new Admin_Model_DbRow_Controller(array(
                        'moduleName'     => $module,
                        'controllerName' => strtolower($match[1]),
                        'virtual'        => 0
                    ));
                    $resources[] = $controller;
                }
                $directory->next();
            }
        }
        // get the controllers from the hooks
        // //FIXME this can be very expensive on performance, multiple nesting of iterations
        FOREACH($this->hooks AS $hook) {
            /**
             * We need to setup a tmp array to store the hook controllers, because
             * we iterate each hookcontroller over reflectcontroller and this would end
             * in duplicates entries.
             * Store the hookcontroller if matches the expressions with a key in the tmp
             * array and later merge the tmp array with the ressources array.
             * so we do not have duplicate entries
             */
            $hookResources = array();
            FOREACH($hook->getControllers() AS $hookController) {
                FOREACH($resources AS $reflectController) {
                    $key                = $hookController->get('moduleName') . '/' . $hookController->get('controllerName');
                    $notSameModule      = $hookController->get('moduleName') !== $reflectController->get('moduleName');
                    $notSameController  = $hookController->get('controllerName') !== $reflectController->get('controllerName');
                    IF($notSameModule && $notSameController && !array_keys($hookResources, $key, TRUE)) {
                        $hookResources[$key] = $hookController;
                    }
                }
            }
            $resources = array_merge_recursive($resources, array_values($hookResources));
        }
        RETURN $resources;
    }

    /**
     * Get all Actions for a modul / controller
     *
     * Method scans modul/controller for actions, which have the Zend Framework
     * action method namespacing, like "indexAction".
     *
     * @param string $modul
     * @param string $controller
     * @return array
     * @access public
     */
    public function getActions($modul, $controller, $virtual)
    {
        $actions = array();
        IF($virtual == 0) {
            $frontController = Zend_Controller_Front::getInstance();
            $className = ucfirst($modul) . '_' . ucfirst($controller) . 'Controller';
            $classFile = ucfirst($controller) . 'Controller.php';

            $directory = $frontController->getControllerDirectory($modul);
            IF(file_exists($directory . '/' . $classFile) === FALSE) {
                throw new Admin_Model_Acl_Exception('Controller file could not be found');
            }

            REQUIRE_ONCE($directory . '/' . $classFile);

            $reflect  = new Zend_Reflection_Class($className);
            $CcFilter = new Zend_Filter_Word_CamelCaseToDash;

            FOREACH($reflect->getMethods() AS $method) {
                IF(preg_match('/(\w+)Action$/', $method->getName(), $match)) {
                    $actionComment = $method->getName();
                    $name          = $match[1];
                    $docComment    = $method->getDocComment();

                    IF(is_string($docComment)) {
                        $commentRows   = explode("\n", $docComment);
                        $actionComment = preg_replace("/^\s*\*\s*(.*)\s*/", '$1', $commentRows[1]);
                        IF($actionComment === "") {
                            $actionComment = $docComment;
                        }
                    }
                    $actions[] = new Admin_Model_DbRow_Action(array(
                        'actionName' => strtolower($CcFilter->filter($name)),
                        'description' => $actionComment
                    ));
                }
            }
        } ELSE {
            FOREACH($this->hooks AS $hook) {
                FOREACH($hook->getActions($modul, $controller) AS $hAction) {
                    $actions[] = $hAction;
                }
                //$actions = array_merge_recursive($actions, $hook->getActions($modul, $controller));
            }
        }
        
        RETURN $actions;
    }

    /**
     * Check if registered module/controller pair does not exist in the controller files any more
     *
     * @param Zend_Db_Table_Rowset $controllers
     * @return array
     * @access public
     */
    public function filterVanishedControllers(array $controllers, $matchAgainst = NULL)
    {
        $resource = array();
        $matchAgainst = $matchAgainst === NULL ? $this->getControllers() : $matchAgainst;
        
        FOREACH($controllers AS $dbController) {
            $found = FALSE;
            FOREACH($matchAgainst AS $mController) {
                IF($dbController->get('moduleName') === $mController->get('moduleName') && $dbController->get('controllerName') === $mController->get('controllerName')) {
                    $found = TRUE;
                }
            }
            IF($found !== TRUE) {
                $resource[] = $dbController;
            }
        }
        RETURN $resource;
    }

    /**
     * Filter deleted or renamed actions
     *
     * Method compares to arrays of actions to check if an action is gone.
     * Reason why this actions could be gone is reaming or deleting
     *
     * @param array $actions
     * @param array $matchAgainst
     * @return
     * @access public
     */
    public function filterVanishedActions($actions, $matchAgainst)
    {
        $resource = array();
        $compareTo = array();
        FOREACH($matchAgainst AS $mAction) {
            $compareTo[] = strtolower($mAction->get('actionName'));
        }
        FOREACH($actions AS $action) {
            //FIXME: Check if not always an array of Admin_Model_DbRow_Action instances is passed, remove the Db Column
            $actionName = $action instanceof Admin_Model_DbRow_Action ? $action->get('actionName') : $action->uaa_action;
            IF(in_array(strtolower($actionName), $compareTo, TRUE) === FALSE) {
                $resource[] = $action;
            }
        }

        RETURN $resource;
    }

    /**
     * Load and init the hooks
     *
     * @param array $config Hooks defined in the config
     */
    private function loadInitHooks($config)
    {
        $this->hooks = array();

        FOREACH($config AS $key => $hook) {
            IF(class_exists($hook->name, TRUE)) {
                // create new hook instance and pass $hook config into the hook
                $this->hooks[] = new $hook->name($hook);
            }
        }
    }
}
?>
