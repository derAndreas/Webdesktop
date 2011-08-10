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
 */
class Webdesktop_Model_Webdesktop
{
    /**
     * the current user
     * @var App_User
     */
    protected $user;
    /**
     * Configuration for webdesktop
     * @var Zend_Config
     */
    protected $config;
    /**
     * Modules Model
     * @var Webdesktop_Model_Modules
     */
    protected $modulesModel;
    /**
     * User allowed modules
     * @var Webdesktop_Model_Modules_ModuleSet
     */
    protected $userModules;

    /**
     * Create an instance of this model
     *
     * @access public
     */
    public function __construct(App_User $user, $config)
    {
        $this->user         = $user;
        $this->config       = $config;
        $this->acl          = new Webdesktop_Model_Acl($this->user);
        $this->modulesModel = new Webdesktop_Model_Modules($this->acl);
        $this->userModules  = $this->modulesModel->getAllUserModules();

    }

    /**
     * Get the initial config for starting the webdesktop
     *
     * @return array
     */
    public function getInitConfig()
    {
        RETURN Zend_Json_Encoder::encode(array(
            'apiUrl'            => $this->config->path->requests,
            'wpUrl'             => $this->config->path->wallpapers,
            'themesUrl'         => $this->config->path->themes,
            'themePreviewUrl'   => $this->config->path->themepreview,
            'modules'           => $this->userModules->getInitScripts(),
            'launchers'         => $this->modulesModel->getUserLaunchers(),
            'style'             => $this->getUserStyle()
        ));

    }

    /**
     * Get the user style to run the webdesktop
     * 
     * @return array
     */
    public function getUserStyle()
    {
        $dbThemes    = new Webdesktop_Model_DbTable_Themes;
        $dbWallpaper = new Webdesktop_Model_DbTable_Wallpapers;

        $theme = $dbThemes->find($this->user->get('themeid'));
        $wp    = $dbWallpaper->find($this->user->get('wpid'));

        $result = array(
            'backgroundcolor' => $this->user->get('bgcolor'),
            'fontcolor'       => $this->user->get('fgcolor'),
            'transparency'    => $this->user->get('transparency'),
            'theme' => array(
                'id'   => $theme->current()->sth_id,
                'name' => $theme->current()->sth_name,
                'src'  => $theme->current()->sth_file
            ),
            'wallpaper' => array(
                'id'       => $wp->current()->swp_id,
                'name'     => $wp->current()->swp_name,
                'file'     => sprintf('%s%s', $this->config->path->wallpapers, $wp->current()->swp_file),
                'position' => $this->user->get('wppos')
            )
        );
        RETURN $result;
    }
}
?>
