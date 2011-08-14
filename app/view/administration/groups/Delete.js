/**
 * Form for Group Delete
 *
 * Could be done with the rowEditor / action column, but admin should have
 * the change to verify that the delete of a group is correct.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration.groups
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.groups.Delete
 * @extends Ext.form.Panel
 * @alias administration_groupdelete
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 * @todo Add more informations to the page, so that the admin can better verify that delete of the selected groups is right
 *       maybe add a grid and list all users in the selected group
 */
Ext.define('Webdesktop.view.administration.groups.Delete', {
    extend : 'Ext.form.Panel',
    alias  : 'widget.administration_groupdelete',
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            closable      : true, //FIXME: see class comment, bug
            border        : false, //FIXME: see class comment, bug
            bodyStyle     :'padding:15px',
            defaultType   : 'displayfield',
            fieldDefaults : {
                msgTarget  : 'side',
                labelWidth : 75
            },
            defaults : {
                anchor   : '60%',
                disabled : true
            },
            items : [{
                xtype      : 'hiddenfield',
                name       : 'id',
                allowBlank : false,
                disabled   : false  // or it does not be submitted through form.submit()
            },{
                name       : 'name',
                fieldLabel : 'Name'
            },{
                xtype      : 'textareafield',
                name       : 'description',
                fieldLabel : 'Description'
            },{
                fieldLabel  : 'Attention',
                value       : 'Please check if this group really should be deleted!',
                formItemCls : 'ux-color-red',
                disabled    : false
            }],
            dockedItems: [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Delete',
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