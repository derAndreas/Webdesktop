/**
 * Grid to change the permission for a store for multuple roles
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration.actions
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.actions.Permission
 * @extends Ext.grid.Panel
 * @alias administration_actionpermission
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.actions.Permission', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.administration_actionpermission',
    initComponent:function() {
        var me = this,
            store;

        store = Ext.create('Webdesktop.store.administration.Permissions', {
            // need custom store id or same store will be used on multiple tabs
            storeId: 'Webdesktop.store.administration.Permission-action-' + me.record.get('id')
        });
        // set the proxy API: custom implementation, see comments in the permission store
        store.setProxyApi({
            read: store.getProxy().api.base + 'loadActionPermissions',
            update: store.getProxy().api.base + 'saveActionPermissions'
        });

        store.load({
            params: {
                actionId: me.record.get('id')
            }
        });

        Ext.apply(me, {
            closable : true, //FIXME: see class comment, bug
            border   : false, //FIXME: see class comment, bug
            title    : me.title || 'Permissions',
            store    : store,
            plugins  : [
                Ext.create('Ext.grid.plugin.CellEditing', { clicksToEdit: 1 })
            ],
            columns  : [{
                    text      : 'Rolename',
                    dataIndex : 'roleName',
                    width     : 180
                },{
                    text      : me.record.get('actionName'),
                    dataIndex : 'rule',
                    renderer  : me.ruleRenderer,
                    width     : 180,
                    field     : {
                        xtype    : 'combobox',
                        editable : false,
                        store    : [[ 0, 'None'], [1, 'Deny'], [2, 'Allow']]
                    }
            }],
            dockedItems: [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Save',
                    iconCls : 'ux-icon-accept'
                },{
                    text    : 'Cancel',
                    iconCls : 'ux-icon-cancel'
                }]
            }]
        });

        me.callParent();
    },
    /**
     * Custom Column Renderer to render the rule
     *
     * Mapping:
     *    INPUT   |  OUTPUT
     *    -----------------
     *      0     |   None
     *      1     |   Deny
     *      2     |   Allow
     *      ""    |   <Select>
     *
     * @param {Integer} value
     * @return {String}
     */
    ruleRenderer: function(value) {
        if(value == 1) {
            return 'Deny';
        } else if(value == 2) {
            return 'Allow';
        } else if(value == 0) {
            return 'None';
        }
        return "&lt;Select&gt;";
    }
});