/**
 * Store for Themes in Webdesktop Settings
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Settings
 * @namespace Webdesktop.store.settings
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.store.administration.Themes
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 */
Ext.define('Webdesktop.store.settings.Themes', {
    extend   : 'Ext.ux.data.Store',
    model    : 'Webdesktop.model.settings.Theme',
    autoLoad : true,
    proxy    : {
        type   : 'ajax',
        api    : {
            read : GLOBAL_USER_PROFILE.apiUrl + '_module/settings/_action/loadThemes'
        },
        reader : {
            type : 'json',
            root : 'themes'
        }
    }
});