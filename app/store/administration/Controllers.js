/**
 * Store for Controllers in the Webdesktop Environment
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
 * @class Webdesktop.store.administration.Controllers
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 */
Ext.define('Webdesktop.store.administration.Controllers', {
    extend     : 'Ext.ux.data.Store',
    model      : 'Webdesktop.model.administration.Controller',
    autoLoad   : true,
    sorters    : ['moduleName','controllerName'],
    groupField : 'moduleName',
    proxy      : {
        type   : 'ajax',
        api    : {
            read : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/loadControllerControllers'
        },
        reader : {
            type : 'json',
            root : 'controllers'
        }
    }
});