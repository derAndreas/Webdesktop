<?php

class App_Model_Acl_Controller_Hook_Webdesktop
{
    private $path;
    private $skip = array();

    public function __construct(Zend_Config $config)
    {
        $this->path = $config->path;
        $this->skip = isset($config->skip) ? $config->skip->toArray() : array();
    }

    public function getControllers()
    {
        $resources = array();
        $directory = new DirectoryIterator($this->path);
        $CcFilter = new Zend_Filter_Word_CamelCaseToDash;
        WHILE($directory->valid()) {
            IF($directory->isFile() && !in_array($directory->getFilename(), $this->skip, TRUE)) {
                REQUIRE_ONCE($directory->getPathname());
                $reflect = new Zend_Reflection_File($directory->getPathName());
                $name = substr($reflect->getClass()->getName(), strrpos($reflect->getClass()->getName(), "_") + 1);
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

    public function getActions($modul, $controller)
    {
        $className = ucfirst($modul) . '_Model_Modules_' . ucfirst($controller);
        $classFile = ucfirst($controller) . '.php';
        $actions = array();
        IF(file_exists($this->path . $classFile)) {
            REQUIRE_ONCE($this->path . $classFile);
            $reflect  = new Zend_Reflection_Class($className);
            $actions = array();

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
                        'actionName' => $name,
                        'description' => $actionComment
                    ));
                }
            }
        }

        RETURN $actions;
    }

}

?>
