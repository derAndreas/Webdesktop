<?php
/**
 * Hook to scan for controllers and actions for Webdesktop Modules.
 *
 * Because Webdesktop Modules are "virtual" and not located in the controller
 * directory of the Zend Framework structure, this Hook scans the files
 * that are in the path (path defined in application.ini) and look for
 *
 * Controllers:
 *  - currently no validations on the name
 *  - see todo: add tests so that filenames are in the class name
 *      - example: File: Administration.php, ClassName: Webdesktop_Model_Modules_Administration => good
 *      - example: File: WillFail.php,       ClassName: Webdesktop_Model_Modules_Foobar         => bad
 *
 * Actions:
 *  - name of the action must be "[name]Action"
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Model_Acl
 * @namespace App_Model_Acl_Controller_Hook
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Model_Acl_Controller_Hook_Webdesktop
 */
class App_Model_Acl_Controller_Hook_Webdesktop
{
    /**
     * Path where to look for Webdesktop Controllers
     * @var string
     */
    private $path;
    /**
     * Array with skip values
     * @var array
     */
    private $skip = array();

    /**
     * Constructor to setup the Hook
     */
    public function __construct(Zend_Config $config)
    {
        $this->path = $config->path;
        $this->skip = isset($config->skip) ? $config->skip->toArray() : array();
    }

    /**
     * Scan the files in the configured path for controllers
     *
     * To dynamically scan controllers from the source files
     * use PHP Reflection to find the controllers.
     *
     * The returning result is an array of Admin_Model_DbRow_Controller elements
     *
     * @return array
     */
    public function getControllers()
    {
        $resources = array();
        $directory = new DirectoryIterator($this->path);
        $CcFilter  = new Zend_Filter_Word_CamelCaseToDash;

        WHILE($directory->valid()) {
            IF($directory->isFile() && !in_array($directory->getFilename(), $this->skip, TRUE)) {
                // load the file
                REQUIRE_ONCE($directory->getPathname());

                $reflect = new Zend_Reflection_File($directory->getPathName());
                $name    = substr(
                            $reflect->getClass()->getName(),
                            strrpos($reflect->getClass()->getName(), "_") + 1
                           );

                $controller = new Admin_Model_DbRow_Controller(array(
                    'moduleName'     => 'webdesktop',
                    'controllerName' => strtolower($name),
                    'virtual'        => 1
                ));

                $resources[] = $controller;
            }
            $directory->next();
        }
        RETURN $resources;
    }

    /**
     * Get all defined actions for a module/controller combination from the sources
     *
     * Scan the Controller in a module for actions.
     * The scanning is pure on the source and the nameing convention, which means
     * only methods in the controller that fit the format
     *          [name]Action
     * will be used as action
     *
     * For a little bit more comfort, the DocString will be used as description
     * of the Action.
     *
     * Returning result is an array of Admin_Model_DbRow_Action elements
     *
     * @return array
     */
    public function getActions($modul, $controller)
    {
        $actions   = array();
        $className = ucfirst($modul) . '_Model_Modules_' . ucfirst($controller);
        $classFile = ucfirst($controller) . '.php';
        
        IF(file_exists($this->path . $classFile)) {
            // load the file
            REQUIRE_ONCE($this->path . $classFile);

            $reflect  = new Zend_Reflection_Class($className);

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
                        'actionName'  => $name,
                        'description' => $actionComment
                    ));
                }
            }
        }

        RETURN $actions;
    }

}
?>
