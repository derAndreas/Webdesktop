<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var Zend_Application_Module_Autoloader
     */
    public $frontController;

    /**
     * @var Zend_Application_Module_Autoloader
     */
    protected $_resourceLoader;
    /**
     * The App Config
     * 
     * @var Zend_Config
     */
    protected $appConfig;


    /**
     * Init the application config
     *
     * @access public
     */
    public function _initAppConfig()
    {
        $path = APPLICATION_PATH . '/configs/';
        $this->appConfig = new Zend_Config_Ini($path . 'application.ini');
        Zend_Registry::set('appConfigPath', $path);

    }
    /**
     * Setup the logger
     *
     * @todo read path from config file
     */
    protected function _initLogger()
    {
        $file = APPLICATION_PATH . '/../data/logs/log';
        IF(!file_exists($file)) {
            $f = fopen($file, 'w');
            fclose($f);
        }
        $logger = new Zend_Log(new Zend_Log_Writer_Stream($file));
        Zend_Registry::set('log', $logger);

    }
    protected function _initSessionNamespace()
    {
        $this->bootstrap('session');
    }

    /**
     * Set the DocType for the Layout
     */
    protected function _initLayoutDocType()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_TRANSITIONAL');
    }

    /**
     * Make the dispatcher prefix the default module
     */
    protected function _initFrontControllerSettings()
    {
        $this->bootstrap('frontController');
        $this->frontController->setResponse(new Zend_Controller_Response_Http());
        $this->frontController->setRequest(new Zend_Controller_Request_Http());

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('App_');
        
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new App_Plugin_Acl($front->getRequest()));
        $front->registerPlugin(new App_Controller_Plugin_Layout_Module($front->getRequest()));

    }

   /**
     * Configure the default modules autoloading, here we first create
     * a new module autoloader specifiying the base path and namespace
     * for our default module. This will automatically add the default
     * resource types for us. We also add two custom resources for Services
     * and Model Resources.
     */
    protected function _initDefaultModuleAutoloader()
    {
//        $this->bootstrap('frontController');
        $front = $this->frontController;
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $modules = $front->getControllerDirectory();
        $default = $front->getDefaultModule();
        FOREACH(array_keys($modules) as $module) {
            IF($module === $default) {
                continue;
            }

            $autoloader->pushAutoloader(new Zend_Application_Module_Autoloader(array(
                'namespace' => ucwords($module),
                'basePath' => $front->getModuleDirectory($module)
            )));
        }
    }



    /**
     * Init the view Ressource
     * 
     * @return Zend_View
     */
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('Noc Project');
        $view->env = APPLICATION_ENV;
 
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
 
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

    /**
     * Init the Cache
     *
     * Caches are used in the system for
     *  - ACL Caching of roles and rules
     *
     * @todo read cache dir from config file 
     */
    public function _initCache()
    {
        $frontendOptions = array(
           'lifetime' => 120, // cache lifetime of 1 minute
           'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => 'src/data/cache' // Directory where to put the cache files
        );

        // getting a Zend_Cache_Core object
        $cache = Zend_Cache::factory('Core',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);
        Zend_Registry::set('Cache_Acl', $cache);
    }


    /**
     * Setup locale
     */
    protected function _initLocale()
    {
        $locale = new Zend_Locale('de_DE');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    /**
     * Some some variables from the config in the Registry
     */
    protected function _initRegisterAppConfig()
    {
        $appConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV );
        Zend_Registry::set('global_app_config', $appConfig);
        Zend_Registry::set('password_salt', $appConfig->resources->db->password_salt);
        Zend_Registry::set('acl_default_role_id', $this->appConfig->production->acl->role->default->id);
        Zend_Registry::set('acl_default_role_name', $this->appConfig->production->acl->role->default->name);
    }

}

