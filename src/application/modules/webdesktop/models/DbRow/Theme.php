<?php
/**
 * Definition of a Theme in SettingsModule
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
 * @class Webdesktop_Model_DbRow_Theme
 * @extends App_Model_DbRow_Abstract
 */
class Webdesktop_Model_DbRow_Theme extends App_Model_DbRow_Abstract {
    /**
     * ID of the Theme
     * @var int
     */
    protected $id;
    /**
     * Name of the Theme
     * @var string
     */
    protected $name;
    /**
     * name of the css file
     * @var string
     */
    protected $src;
    /**
     * name of the image file for a little preview
     * @var string
     */
    protected $preview;

    protected $_transformColumnMap = array(
        'id'       => 'sth_id',
        'name'     => 'sth_name',
        'preview'  => 'sth_preview',
        'src'      => 'sth_file'
    );

    protected $defaultDbColumns   = array('name', 'preview', 'src');
    protected $defaultJsonColumns = array('id', 'name', 'preview', 'src');
}
?>
