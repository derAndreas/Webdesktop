<?php
/**
 * Database Model for the Table "style_themes" containing all themes available
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_DbTable
 * @namespace Webdesktop_Model_DbTable
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop_Model_DbTable_Themes
 * @extends Zend_Db_Table_Abstract
 */
class Webdesktop_Model_DbTable_Themes extends Zend_Db_Table_Abstract
{
    /**
     * primary key
     *
     * @var string
     */
    protected $_primary = 'sth_id';
    /**
     * Table name
     *
     * @var String
     */
    protected $_name    = 'style_themes';

}
?>
