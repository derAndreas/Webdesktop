/**
 * Base form for all actions with users
 *
 * Add/Edit/Delete form extend from this baseform. Reduce the LOC and complexity
 * in maintaining the code.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration.users
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.users.BaseForm
 * @extends Ext.form.Panel
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 * @todo Find a better way, if the extending form is "Add" for the hiddenfield (top of initComponent())
 */
Ext.define('Webdesktop.view.administration.users.BaseForm', {
    extend : 'Ext.form.Panel',

    initComponent: function() {
        var me = this,
            hiddenfield;

        hiddenfield = {
            xtype: 'hiddenfield',
            name: 'id',
            allowBlank: me.$className === "Webdesktop.view.administration.users.Add" ? false : true,   // if we have add panel we dont need this field
            disabled: false
        };

        Ext.apply(me, {
            closable      : true, //FIXME: see class comment, bug
            border        : false, //FIXME: see class comment, bug
            bodyStyle     :'padding:15px',
            defaultType   : 'textfield',
            fieldDefaults : {
                msgTarget  : 'side',
                labelWidth : 75
            },
            defaults : {
                anchor : '60%'
            },
            items : [
                hiddenfield,
                {
                    name       : 'name',
                    fieldLabel : 'Name',
                    allowBlank : false
                },{
                    name       : 'username',
                    fieldLabel : 'Username',
                    allowBlank : false
                },{
                    name       : 'email',
                    fieldLabel : 'Email',
                    vtype      : 'email'
                }, {
                    xtype          : 'combobox',
                    name           : 'groupid',
                    fieldLabel     : 'Group',
                    allowBlank     : false,
                    forceSelection : true,
                    typeAhead      : true,
                    store          : 'Webdesktop.store.administration.Groups',
                    valueField     : 'id',
                    displayField   : 'name',
                    queryMode      : 'local'         // put autoload in the store or use here remote
                }, {
                    xtype          : 'checkboxfield',
                    name           : 'enabled',
                    fieldLabel     : 'Enabled',
                    boxLabel       : 'Enabled',
                    inputValue     : 1,
                    uncheckedValue : 0
                }
            ],
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Delete',
                    iconCls : 'ux-icon-accept'
                },{
                    text    : 'Cancel',
                    iconCls : 'ux-icon-cancel',
                }]
            }]
        });

        me.callParent();
    }
});