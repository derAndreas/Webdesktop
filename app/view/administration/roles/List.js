/**
 * Listing for all roles in the Webdesktop Application
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration.controllers
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.roles.List
 * @extends Ext.grid.Panel
 * @alias administration_rolelist
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.roles.List', {
    extend : 'Ext.grid.Panel',
    alias  : 'widget.administration_rolelist',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : 'List of Roles',
            store       : 'Webdesktop.store.administration.Roles',
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'right',
                items : [{
                    text    : '',
                    iconCls : 'ux-icon-group-add',
                    tooltip : 'Add a new Role'
                }, {
                    text    : '',
                    iconCls : 'ux-icon-group-edit',
                    tooltip : 'Edit the selected Role'
                }, {
                    text    : '',
                    iconCls : 'ux-icon-group-delete',
                    tooltip : 'Delete the selected Role'
                }, '-',{
                    text    : '',
                    iconCls : 'ux-icon-reload',
                    tooltip : 'Reload the list of roles'
                }]
            }],
            columns: [{
                xtype : 'templatecolumn',
                text  : 'Name',
                flex  : 1,
                tpl   :'<b>{name}</b><br/>{description}'
            },{
                // FIXME: Complex problem, see Webdesktop.model.administration.Role for details
                xtype    : 'checkcolumn',
                dataIndex: 'enabled',
                header   : 'Enabled',
                width    : 60
            },{
                text     : 'Assigned to this role',
                defaults : {
                    align : 'center'
                },
                columns  : [{
                    text      : 'Users',
                    dataIndex : 'users',
                    width     : 60,
                    renderer  : function(value, metaData, model) {
                        return model.users().count();
                    }
                },{
                    text      : 'Groups',
                    dataIndex : 'groups',
                    width     : 60,
                    renderer  : function(value, metaData, model) {
                        return model.groups().count();
                    }
                },{
                    text      : 'Roles',
                    dataIndex : 'inherits',
                    width     : 60,
                    renderer  : function(value, metaData, model) {
                        return model.inherits().count();
                    }
                }]
            }]
        });
        
        me.callParent();
    }
});