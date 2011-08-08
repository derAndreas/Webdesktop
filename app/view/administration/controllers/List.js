/**
 * Listing for all controllers in the Webdesktop Application
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
 * @class Webdesktop.view.administration.controllers.List
 * @extends Ext.grid.Panel
 * @alias administration_controllerlist
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.controllers.List', {
    extend : 'Ext.grid.Panel',
    alias  : 'widget.administration_controllerlist',
    initComponent: function() {
        var me = this;

        var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
            groupHeaderTpl : 'Module: {[Ext.String.capitalize(values.name)]} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
            startCollapsed : true
        });

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : 'List of Modules/Controllers',
            store       : 'Webdesktop.store.administration.Controllers',
            features    : [groupingFeature],
            selType     : 'rowmodel',
            dockedItems : [{
                xtype    : 'toolbar',
                dock     : 'top',
                defaults : {
                    hidden : true
                },
                items : [{
                    text         : 'Show New/Deleted Controllers',
                    actionType   : 'showNewDeletedController',
                    tooltip      : 'Scan for added/removed Module/Controllers',
                    enableToggle : true,
                    hidden       : false
                }, {
                    text       : 'Edit Controller',
                    actionType : 'editController',
                    tooltip    : 'Edit the selected Controller'
                }, {
                    text       : 'Add Controller',
                    actionType : 'addController',
                    tooltip    : 'Add the selected Controller'
                }, {
                    text       : 'Delete Controller',
                    actionType : 'deleteController',
                    tooltip    : 'Delete the selected Controller'
                }, {
                    text       : 'Change Enabled',
                    actionType : 'statusController',
                    tooltip    : '(De-)Active the selected Controller'
                }, '-',{
                    text       : 'Permissions',
                    actionType : 'permissionsController',
                    tooltip    : 'Edit the Permissions of Controller'
                },'-', {
                    text       : 'List Actions',
                    actionType : 'listActions',
                    tooltip    : 'List all actions of the selected Controller'
                },'-',{
                    text       : '',
                    iconCls    : 'ux-icon-reload',
                    tooltip    : 'Reload the list of Controllers',
                    hidden     : false
                }]
            }],
            columns : [{
                text      : 'Module',
                dataIndex : 'moduleName',
                width     : 120
            },{
                text      : 'Controller',
                dataIndex : 'controllerName',
                width     : 120
            },{
                xtype     : 'booleancolumn',
                dataIndex : 'enabled',
                text      : 'Enabled',
                width     : 60,
                trueText  : 'Yes',
                falseText : 'No'
                
            },{
                xtype     : 'booleancolumn',
                text      : 'Virtual',
                dataIndex : 'virtual',
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
     * After first load, filter only existing and working controllers
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
     * Custom Column Renderer to render the Status of an controller
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