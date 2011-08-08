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
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop_Model_DbRow_Theme
 * @extends App_Model_DbRow_Abstract
 */
class Webdesktop_Model_DbRow_Theme extends App_Model_DbRow_Abstract {
    protected $id;
    protected $name;
    protected $src;
    protected $preview;
    /**
     * Maps the Theme Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
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
