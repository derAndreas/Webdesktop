<?php
/**
 * Model for the webdesktop
 *
 * Couples the current requesting user to the backend
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
 * @class Webdesktop_Model_DbTable_Launchers
 * @todo some other classes can be merged to this model to reduce having code for the
 *       same request type spreading over several models.
 */
class Webdesktop_Model_Webdesktop
{
    /**
     * the current user
     * 
     * @var App_User
     */
    protected $user;
    /**
     * Configuration for webdesktop
     * 
     * @var Zend_Config
     */
    protected $config;
    /**
     * The userstyle model
     * 
     * @var Webdesktop_Model_Userstyle
     */
    protected $userStyleModel;
    /**
     *
     * @var Webdesktop_Model_Modules
     */
    protected $modulesModel;
    /**
     *
     * @var Webdesktop_Model_Modules_ModuleSet
     */
    protected $userModules;

    /**
     * Create an instance of this model
     *
     * @access public
     * @todo refactor while merging this model with several others
     */
    public function __construct(App_User $user, $config)
    {
        $this->user = $user;
        $this->config = $config;
        $this->userStyleModel = new Webdesktop_Model_Userstyle($this->user);
        $this->userStyleModel->setConfig($config);
        $this->acl            = new Webdesktop_Model_Acl;
        $this->acl->setUser($this->user);
        $this->modulesModel   = new Webdesktop_Model_Modules($this->acl);

        $this->userModules = $this->modulesModel->getAllUserModules();
    }

    /**
     * Returns the user style model
     *
     * @return Webdesktop_Model_Userstyle
     * @access public
     * @deprecated when this class is refactored, the userstyle model is gone
     */
    public function getUserStyleModel()
    {
        RETURN $this->userStyleModel;
    }

    /**
     * Get the initial config for starting the webdesktop
     *
     * @return array
     * @access public
     */
    public function getInitConfig()
    {
        $data = array();
        $data['userStyle'] = Zend_Json_Encoder::encode($this->userStyleModel->prepareJson());
        $data['modules'] = $this->userModules->getInitScripts();
        $data['launchers'] = Zend_Json_Encoder::encode($this->modulesModel->getUserLaunchers());

        RETURN Zend_Json_Encoder::encode(array(
            'apiUrl' => $this->config->path->requests,
            'wpUrl' =>  $this->config->path->wallpapers,
            'themesUrl' =>  $this->config->path->themes,
            'themePreviewUrl' =>  $this->config->path->themepreview,
            'modules' => $this->userModules->getInitScripts(),
            'launchers' => $this->modulesModel->getUserLaunchers(),
            'style' => $this->userStyleModel->prepareJson()
        ));

    }

    /**
     * ???
     * 
     * @return
     */
    public function getInitCssFiles()
    {
        RETURN $this->userModules->getInitCss();
    }
}
?>
