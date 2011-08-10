<?php
/**
 * Database Model for the Table "user_launchers" containing User Launchers
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
 * @class Webdesktop_Model_Userstyle
 * @todo remove and merge to webdesktop model or user model?
 *       Userstyle will be merged on the Db Side with the user, should be done
 *       on the model side too!
 * @todo docs are missing, because class will be removed in the next releases
 */
class Webdesktop_Model_Userstyle
{

    protected $dbUserStyle;
    protected $user;
    protected $userStyle;
    protected $userWallpaper;
    protected $userTheme;

    /**
     *
     */
    public function __construct(App_User $user)
    {
        $this->user = $user;
        $this->dbUserStyle = new Webdesktop_Model_DbTable_Userstyle;
    }

    /**
     * Set the Webdesktop config object
     * 
     * @param Zend_Config $config
     * @return Webdesktop_Model_Userstyle
     */
    public function setConfig(Zend_Config $config)
    {
        $this->config = $config;
        RETURN $this;
    }

    /**
     *
     * @return
     * @access public
     */
    public function getUserStyle($userId = NULL)
    {
        $userId = !is_null($userId) ? $userId : $this->user->getId();
        $data = $this->dbUserStyle->fetchRow('us_uu_id = ' . $this->user->getId());
        $return = array();

        IF($data) {
            $return['backgroundcolor']  = $data->us_backgroundcolor;
            $return['fontcolor']        = $data->us_fontcolor;
            $return['transparency']     = $data->us_transparency;
            IF($userId === $this->user->getId()) {
                $this->userStyle = $return;
            }
        }

        RETURN $return;

    }

    /**
     *
     * @return
     * @access public
     */
    public function getUserWallpaper($userId = NULL)
    {
        $userId = !is_null($userId) ? $userId : $this->user->getId();
        $data = $this->dbUserStyle->getUserWallpaper($this->user->getId());
        $return = array();
        IF($data) {
            $return['id']       = $data['swp_id'];
            $return['name']     = $data['swp_name'];
            $return['file']     = $this->config->path->wallpapers . $data['swp_file'];
            $return['position'] = $data['us_wallpaperpos'];

            IF($this->user->getId() === $userId) {
                $this->userWallpaper = $return;
            }
        }

        RETURN $return;

    }
    /**
     *
     * @return
     * @access public
     */
    public function getUserTheme($userId = NULL)
    {
        $userId = !is_null($userId) ? $userId : $this->user->getId();
        $data = $this->dbUserStyle->getUserTheme($this->user->getId());
        $return = array();

        IF($data) {
            $return['id']   = $data['sth_id'];
            $return['name'] = $data['sth_name'];
            $return['src']  = $data['sth_file'];
        } else {
            $return['id']   = $data['sth_id'];
            $return['name'] = $data['sth_name'];
            $return['src']  = $this->config->default->theme;
        }

        IF($this->user->getId() === $userId) {
            $this->userTheme = $return;
        }

        RETURN $return;

    }


    /**
     *
     * @return
     * @access public
     */
    public function prepareJson()
    {
        $return  = $this->getUserStyle();
        $return['theme']     = !is_null($this->userTheme) ? $this->userTheme : $this->getUserTheme();
        $return['wallpaper'] = !is_null($this->userWallpaper) ? $this->userWallpaper : $this->getUserWallpaper();

        RETURN $return;
    }

}
?>
