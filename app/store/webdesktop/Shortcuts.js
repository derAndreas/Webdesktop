/**
 * Store for Shortcuts in the Webdesktop
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @namespace Webdesktop.store.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.store.webdesktop.Shortcuts
 * @extends Ext.ux.data.Store
 * @todo Find a way to remove the global variable on the store base url
 * @todo field mapping does not work here, could be caused by a faulty
 *       Webdesktop.model.webdesktop.Module Model. After fixing the model
 *       review this code here. Could also be a problem of mixing model and fields.
 */
Ext.define('Webdesktop.store.webdesktop.Shortcuts', {
    extend     : 'Ext.data.Store',
    model      : 'Webdesktop.model.webdesktop.Module',
    idProperty : 'moduleId',
    fields     : [
        {
            name: 'moduleId'
        }, {
            //FIXME: Don't get mapping to work, used convert as workaround
            name    : 'name',
            mapping : 'launcher.text',
            convert : function(v, record) {
                return (record.data && record.data.launcher) ? record.data.launcher.text : '';
            }
        }, {
            //FIXME: Don't get mapping to work, used convert as workaround
            name    : 'iconCls',
            mapping : 'launcher.shortcutIconCls',
            convert : function(v, record) {
                return (record.data && record.data.launcher) ? record.data.launcher.shortcutIconCls : '';
            }
        }
    ],
    proxy      : {
        type   : 'memory',
        reader : {
            type : 'json'
        }
    }
});