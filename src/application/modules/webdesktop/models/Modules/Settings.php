<?php
/**
 * WebDesktop Settings Module
 *
 * Users can change the look and feel of their webdesktop
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @version 0.1
 * @package Webdesktop
 * @subpackage Model_Modules
 * @namespace Webdesktop_Model_Modules
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_Model_Modules_Settings
 * @extends Webdesktop_Model_Modules_Abstract
 */
class Webdesktop_Model_Modules_Settings extends Webdesktop_Model_Modules_Abstract
{
    protected $name              = 'Settings Module';
    protected $id                = 'settings';
    protected $description       = 'Module to change the user preferences';
    protected $version           = 0.1;
    protected $className         = 'settings.Settings';
    protected $startmenupath     = Webdesktop_Model_Modules_Abstract::MENUPATH_TOOLBAR;
    protected $iconClass         = 'settings-icon';
    protected $shortcutIconClass = 'settings-shortcut';
    protected $about = array(
        'author'      => 'Andreas Mairhofer',
        'url'         => 'http://www.example.org',
        'email'       => 'demo@example.org',
        'description' => 'User Settings of the WebDesktop'
    );
    public $actions = array(
        'index',
        'loadWallpapers',
        'changeWallpaper',
        'loadThemes',
        'changeTheme',
        'changeShortcuts',
        'changeAutorun',
        'changeQuickstart'
    );
    /**
     * @todo Dummy index action, can be removed (from ACL too)
     */
    public function indexAction()
    {
    }

    /**
     * Load all available wallpapers from the database
     *
     * @return array
     */
    public function loadWallpapersAction()
    {
        $wpModel = new Webdesktop_Model_DbTable_Wallpapers;
        $wallpapers = array();

        FOREACH($wpModel->fetchAll() AS $wp) {
            $w = new Webdesktop_Model_DbRow_Wallpaper($wp);
            $wallpapers[] = $w->toJsonArray();
        }
        RETURN $this->responseSuccess(array(
            'wallpapers' => $wallpapers
        ));
    }

    /**
     * Change the User selected Wallpapaer
     *
     * @return array
     * @todo check that wallpaper id is a valid one
     * @todo refresh ACL User, because if user hits Reload the cached ACL User is used
     */
    public function changeWallpaperAction()
    {
        $usModel = new App_Model_DbTable_User;
        $auth    = Zend_Auth::getInstance();
        $user    = $auth->getIdentity();
        $wId     = $this->request->getParam('id', null);
        $stretch = $this->request->getParam('stretch', 0);

        IF(is_numeric($wId) && $user) {
            $usModel->setWallpaper($wId, $stretch, $user->getId());
            RETURN $this->responseSuccess();
        }
        RETURN $this->responseFailure('Could not save', 'Could not save wallpaper, are you looged in?');
    }

    /**
     * Load all available themes from the database
     *
     * @return array
     */
    public function loadThemesAction()
    {
        $model  = new Webdesktop_Model_DbTable_Themes;
        $themes = array();

        FOREACH($model->fetchAll() AS $row) {
            $theme = new Webdesktop_Model_DbRow_Theme($row);
            $themes[] = $theme->toJsonArray();
        }
        RETURN $this->responseSuccess(array(
            'themes' => $themes
        ));
    }
    /**
     * Change the user selected theme
     *
     * @return array
     * @todo refresh ACL User, because if user hits Reload the cached ACL User is used
     */
    public function changeThemeAction()
    {
        $usModel = new App_Model_DbTable_User;
        $auth    = Zend_Auth::getInstance();
        $user    = $auth->getIdentity();
        $tId     = $this->request->getParam('id', null);

        IF(is_numeric($tId) && $user) {
            $usModel->setTheme($tId, $user->getId());
            RETURN $this->responseSuccess();
        }
        RETURN $this->responseFailure('Could not save', 'Could not save theme, are you looged in?');
    }

    /**
     * Save the shortcuts that should be visible on the desktop
     * after the user logins or refreshes the page
     *
     * @return array
     */
    public function changeShortcutsAction()
    {
        $launcher   = new Webdesktop_Model_DbTable_Launchers;
        $modulesMod = new Webdesktop_Model_DbTable_Modules;
        $user       = Zend_Auth::getInstance()->getIdentity();
        $modules    = Zend_Json::decode($this->request->getParam('modules', array()));

        $launcher->deleteByUserType($user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_SHORTCUT);

        IF(count($modules)) {
            // find the module id
            FOREACH($modules AS $module) {
                $module = $modulesMod->findModuleById($module);
                IF($module->count() == 0) {
                    continue;
                }
                $launcher->insertLauncher($module->current()->m_id, $user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_SHORTCUT);
            }
        }

        RETURN $this->responseSuccess();
    }
    /**
     * Save the autorun modules that should startup
     * after the user logins or refreshes the page
     *
     * @return array
     */
    public function changeAutorunAction()
    {
        $launcher   = new Webdesktop_Model_DbTable_Launchers;
        $modulesMod = new Webdesktop_Model_DbTable_Modules;
        $user       = Zend_Auth::getInstance()->getIdentity();
        $modules    = Zend_Json::decode($this->request->getParam('modules', array()));

        $launcher->deleteByUserType($user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_AUTORUN);

        IF(count($modules)) {
            // find the module id
            FOREACH($modules AS $module) {
                $module = $modulesMod->findModuleById($module);
                IF($module->count() == 0) {
                    continue;
                }
                $launcher->insertLauncher($module->current()->m_id, $user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_AUTORUN);
            }
        }

        RETURN $this->responseSuccess();
    }

    /**
     * Save the autorun modules that should startup
     * after the user logins or refreshes the page
     *
     * @return array
     */
    public function changeQuickstartAction()
    {
        $launcher   = new Webdesktop_Model_DbTable_Launchers;
        $modulesMod = new Webdesktop_Model_DbTable_Modules;
        $user       = Zend_Auth::getInstance()->getIdentity();
        $modules    = Zend_Json::decode($this->request->getParam('modules', array()));

        $launcher->deleteByUserType($user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_QUICKSTART);

        IF(count($modules)) {
            // find the module id
            FOREACH($modules AS $module) {
                $module = $modulesMod->findModuleById($module);
                IF($module->count() == 0) {
                    continue;
                }
                $launcher->insertLauncher($module->current()->m_id, $user->getId(), Webdesktop_Model_DbTable_Launchers::LAUNCHER_QUICKSTART);
            }
        }

        RETURN $this->responseSuccess();
    }
}