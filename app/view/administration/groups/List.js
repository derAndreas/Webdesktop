/**
 * Listing for all groups in the Webdesktop Application
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
 * @class Webdesktop.view.administration.groups.List
 * @extends Ext.grid.Panel
 * @alias administration_grouplist
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.groups.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.administration_grouplist',
    initComponent: function() {
        var me = this;

        var rowEditing = Ext.create('Ext.ux.grid.plugin.RowEditing', {
            clicksToMoveEditor : 1,
            autoCancel         : false,
            pluginId           : 'RowEditorGroupList'
        });

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : 'List of groups',
            store       : 'Webdesktop.store.administration.Groups',
            plugins     : [rowEditing],
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'right',
                items : [{
                    text    : '',
                    iconCls : 'ux-icon-group-add',
                    tooltip : 'Add a new Group'
                }, {
                    text    : '',
                    iconCls : 'ux-icon-group-edit',
                    tooltip : 'Edit the selected Group'
                }, {
                    text    : '',
                    iconCls : 'ux-icon-group-delete',
                    tooltip : 'Delete the selected group'
                }, '-',{
                    text    : '',
                    iconCls : 'ux-icon-reload',
                    tooltip : 'Reload the list of groups'
                }]
            }],
            columns: [{
                text      : 'Name',
                dataIndex : 'name',
                flex      : 1,
                editor    : {
                    allowBlank : false
                }
            },{
                text      : 'Description',
                dataIndex : 'description',
                flex      : 1,
                editor    : {
                    allowBlank : false,
                    maxLength  : 200
                }
            },{
                text      : 'Members in Group',
                dataIndex : 'memberscount',
                flex      : 1
            }]
        });

        me.callParent();
    }
});