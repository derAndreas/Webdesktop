/**
 * Store for Users in the Webdesktop Environment
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
 * @class Webdesktop.store.administration.Users
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 * @todo remove zF Proxy or rewrite ZF Proxy
 */
Ext.define('Webdesktop.store.administration.Users', {
    extend   : 'Ext.ux.data.Store',
    autoLoad : true,
    model    : 'Webdesktop.model.administration.User',
    proxy    : {
        type        : 'zf',
        url         : GLOBAL_USER_PROFILE.apiUrl,
        extraParams : {
            _module : 'administration',
            _action : 'loadUserUsers'
        },
        reader      : {
            type : 'json',
            root : 'users'
        }
    }
});