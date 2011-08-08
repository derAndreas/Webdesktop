/**
 * Store for Permissions in the Webdesktop Environment
 *
 * This store is shared between Action/Controller Permission.
 * To share this, the URLs need to be dynamicly created within the controller,
 * which creates the store.
 * For easy using this, set the base URL in the API config and with the setProxyAPI()
 * function set the correct API URLs.
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
 * @class Webdesktop.store.administration.Permissions
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 */
Ext.define('Webdesktop.store.administration.Permissions', {
    extend   : 'Ext.ux.data.Store',
    model    : 'Webdesktop.model.administration.Permission',
    autoLoad : false,    // cannot autoload, because we need the controller id as param
    sorters  : ['roleName'],  //FIXME: fix right sorter
    proxy    : {
        type   : 'ajax',
        api    : {
            // This is more like a workaround: need a base url and after we initialise the permission store, we set the right
            // backend MVC action. This store can be used for controller and action permissions
            base : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/'
        },
        reader : {
            type : 'json',
            root : 'permissions'
        },
        writer : {
            type   : 'json',
            root   : 'permissions',
            encode : true
        }
    },
    /**
     * Set the correct API Urls
     * 
     * @param {Object} api
     */
    setProxyApi: function(api) {
        this.getProxy().api = api;
    }
});