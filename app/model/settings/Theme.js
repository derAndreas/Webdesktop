/**
 * Define the Theme Model
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Settings
 * @namespace Webdesktop.model.settings
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */
/**
 * @class Webdesktop.model.settings.Theme
 * @extends Ext.data.Model
 */
Ext.define('Webdesktop.model.settings.Theme', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id',      type: 'int'},
        { name: 'src',     type: 'string'},
        { name: 'name',    type: 'string'},
        { name: 'preview', type: 'string', defaultValue: Ext.BLANK_IMAGE_URL}
    ]
});
