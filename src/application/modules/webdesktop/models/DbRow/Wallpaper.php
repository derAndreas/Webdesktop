<?php
/**
 * Definition of a Wallpaper in SettingsModule
 *
 * Attention: Because Zend_Db::FETCH_INTO is not supported
 *            do not use this class as $_rowClass in the TableAbstraction
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_DbRow
 * @namespace Webdesktop_Model_DbRow
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_Model_DbRow_Wallpaper
 * @extends App_Model_DbRow_Abstract
 */
class Webdesktop_Model_DbRow_Wallpaper extends App_Model_DbRow_Abstract {
    /**
     * ID of the wallpaper
     * @var int
     */
    protected $id;
    /**
     * Name of the wallpaper
     * @var string
     */
    protected $name
    /**
     * Name of the image file
     * @var string
     */
    protected $src;

    protected $_transformColumnMap = array(
        'id'       => 'swp_id',
        'name'     => 'swp_name',
        'src'      => 'swp_file'
    );

    protected $defaultDbColumns   = array('name', 'src');
    protected $defaultJsonColumns = array('id', 'name', 'src');

}
?>
