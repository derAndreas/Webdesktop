/**
 * Store for Actions in the Webdesktop Environment
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.store.administration
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.store.administration.Actions
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 */
Ext.define('Webdesktop.store.administration.Actions', {
    extend   : 'Ext.ux.data.Store',
    model    : 'Webdesktop.model.administration.Action',
    autoLoad : false,    // cannot autoload, because we need the controller id as param
    sorters  : ['actionName'],
    proxy    : {
        type   : 'ajax',
        api    : {
            read : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/loadControllerActions'
        },
        reader : {
            type : 'json',
            root : 'actions'
        }
    }
});