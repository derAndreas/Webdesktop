/**
 * Form for Controller Edit
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
 * @class Webdesktop.view.administration.controllers.Edit
 * @extends Ext.form.Panel
 * @alias administration_controlleredit
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.controllers.Edit', {
    extend : 'Ext.form.Panel',
    alias  : 'widget.administration_controlleredit',
    initComponent:function() {
        var me = this;

        Ext.apply(me, {
            closable      : true, //FIXME: see class comment, bug
            border        : false, //FIXME: see class comment, bug
            bodyStyle     :'padding:15px',
            defaultType   : 'displayfield',
            fieldDefaults : {
                msgTarget  : 'side',
                labelWidth : 120
            },
            defaults : {
                anchor : '60%'
            },
            items : [{
                xtype : 'hiddenfield',
                name  : 'id'
            },{
                name       : 'moduleName',
                fieldLabel : 'Module Name'
            }, {
                name       : 'controllerName',
                fieldLabel : 'Controller Name'
            }, {
                xtype          : 'checkbox',
                name           : 'enabled',
                fieldLabel     : 'Enabled',
                uncheckedValue : 'off'
            }, {
                xtype      : 'textareafield',
                name       : 'description',
                fieldLabel : 'Description',
                allowBlank : false
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
    }
});