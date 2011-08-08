/**
 * Listing for all actions in the Webdesktop Application
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
 * @class Webdesktop.view.administration.actions.List
 * @extends Ext.grid.Panel
 * @alias administration_actionlist
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.actions.List', {
    extend : 'Ext.grid.Panel',
    alias  : 'widget.administration_actionlist',
    initComponent: function() {
        var me = this,
            store;

        store = Ext.create('Webdesktop.store.administration.Actions', {
            // need custom store id or same store will be used on multiple tabs
            storeId: 'Webdesktop.store.administration.Actions-' + me.cId
        }),

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : me.title || 'List of Actions',
            store       : store,
            selType     : 'rowmodel',
            dockedItems : [{
                xtype    : 'toolbar',
                dock     : 'top',
                defaults : {
                    hidden : true
                },
                items    : [{
                    text         : 'Show New/Deleted Actions',
                    actionType   : 'showNewDeletedAction',
                    tooltip      : 'Scan for added/removed Actions in this Controller',
                    enableToggle : true,
                    hidden       : false
                }, {
                    text       : 'Edit Action',
                    actionType : 'editAction',
                    tooltip    : 'Edit the selected Action'
                }, {
                    text       : 'Add Action',
                    actionType : 'addAction',
                    tooltip    : 'Add the selected Action'
                }, {
                    text       : 'Delete Action',
                    actionType : 'deleteAction',
                    tooltip    : 'Delete the selected Action'
                }, {
                    text       : 'Change Enabled',
                    actionType : 'statusAction',
                    tooltip    : '(De-)Active the selected Action'
                }, '-', {
                    text       : 'Permissions',
                    actionType : 'permissionsAction',
                    tooltip    : 'Edit the Permissions of Action'
                },'-', {
                    text: '',
                    iconCls    : 'ux-icon-reload',
                    tooltip    : 'Reload the list of Actions',
                    hidden     : false
                }]
            }],
            columns: [{
                text      : 'Name',
                dataIndex : 'actionName',
                width     : 180
            },{
                xtype     : 'booleancolumn',
                text      : 'Enabled',
                dataIndex : 'enabled',
                width     : 60,
                trueText  : 'Yes',
                falseText : 'No'
            },{
                text      : 'Status',
                dataIndex : 'status',
                renderer  : me.statusRenderer,
                width     : 90
            },{
                text      : 'Description',
                dataIndex : 'description',
                flex      : 1
            }]
        });

        me.callParent();
    },

    /**
     * After first load, filter only existing and working actions
     */
    afterRender: function() {
        var me = this;
        me.callParent();
        me.getStore().filter(
            new Ext.util.Filter({
                filterFn: function(item) {
                    return item.data.status == 0;
                }
            })
        );
    },
    /**
     * Custom Column Renderer to render the Status of an action
     *
     * Mapping:
     *    INPUT   |  OUTPUT
     *    -----------------
     *      0     |   OK
     *      1     |   NEW
     *      2     |   DEL
     *      ""    |   ''
     *
     * @param {Integer} value
     * @return {String}
     */
    statusRenderer: function(value) {
        if(value == 0) {
            return 'OK'
        } else if(value == 1) {
            return 'NEW';
        } else if (value == 2) {
            return 'DEL'
        }
        return ''; // should not be reached
    }
});