/**
 * Store for Groups in the Webdesktop Environment
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
 * @class Webdesktop.store.administration.Groups
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 */
Ext.define('Webdesktop.store.administration.Groups', {
    extend   : 'Ext.ux.data.Store',
    model    : 'Webdesktop.model.administration.Group',
    autoLoad : true,
    proxy    : {
        type   : 'ajax',
        api    : {
            read   : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/loadGroupGroups',
            create : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/saveNewGroup',
            update : GLOBAL_USER_PROFILE.apiUrl + '_module/administration/_action/saveEditGroup'
        },
        reader : {
            type : 'json',
            root : 'groups'
        },
        writer : {
            type   : 'json',
            root   : 'groups',
            encode : true
        }
    },
    /**
     * To filter items with the isValidNew() method with the group model
     * 
     * @override
     */
    filterNew : function(item) {
        // only want phantom records that are valid
        return item.phantom === true && item.isValidNew();
    }
});