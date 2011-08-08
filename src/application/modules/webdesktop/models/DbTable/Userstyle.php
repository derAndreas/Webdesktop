<?php
/**
 * Database Model for the Table "user_styles" containing User Launchers
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
 * @class Webdesktop_Model_DbTable_Userstyle
 * @extends Zend_Db_Table_Abstract
 * @todo remove this dbTable and merge it into the user_users table
 *       Currently adding a user does not add a userstyle row, so this isn't working at all
 */
class Webdesktop_Model_DbTable_Userstyle extends Zend_Db_Table_Abstract
{
    /**
     * primary key
     *
     * @var string
     */
    protected $_primary = 'us_id';
    /**
     * Table name
     *
     * @var String
     */
    protected $_name = 'user_styles';

    /**
     * Get the user wallpaper settings
     *
     * @param int $userId
     * @return Zend_Db_Table_Rowset_Abstract
     * @access public
     */
    public function getUserWallpaper($userId)
    {
        $select = $this->getAdapter()->select()
                  ->from('user_styles')
                  ->joinLeft('style_wallpapers', 'swp_id = us_swp_id', array('swp_id', 'swp_name', 'swp_file_thumb', 'swp_file'))
                  ->where('us_uu_id = ?', $userId);
        
        RETURN $this->getAdapter()->fetchRow($select);
    }

    /**
     * Set (update) a wallpaper for a user
     *
     * @param int $wpId
     * @param string $stretch
     * @param int $userId
     * @return int number of affected rows
     */
    public function setWallpaper($wpId, $stretch, $userId)
    {
        RETURN parent::update(
            array(
                'us_swp_id' => $wpId,
                'us_wallpaperpos' => $stretch
            ),
            $this->getAdapter()->quoteInto('us_uu_id = ?', $userId, Zend_Db::INT_TYPE)
        );
    }

    /**
     * get the user theme settings
     *
     * @param int $userId
     * @return Zend_Db_Table_Rowset_Abstract
     * @access public
     */
    public function getUserTheme($userId)
    {
        $select = $this->getAdapter()->select()
                  ->from('style_themes')
                  ->joinLeft('user_styles', 'sth_id = us_sth_id', array())
                  ->where('us_uu_id = ?', $userId);

        RETURN $this->getAdapter()->fetchRow($select);
    }

    /**
     * Set (update) a theme for a user
     *
     * @param int $themeId
     * @param int $userId
     * @return int number of affected rows
     */
    public function setTheme($themeId, $userId)
    {
        RETURN parent::update(
            array(
                'us_sth_id' => $themeId
            ),
            $this->getAdapter()->quoteInto('us_uu_id = ?', $userId, Zend_Db::INT_TYPE)
        );
    }
}
?>
