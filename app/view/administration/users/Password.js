/**
 * Form for user change password
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
 * @class Webdesktop.view.administration.users.Password
 * @extends Ext.form.Panel
 * @alias administration_userpass
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.users.Password', {
    extend : 'Ext.form.Panel',
    alias  : 'widget.administration_userpass',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            closable      : true, //FIXME: see class comment, bug
            border        : false, //FIXME: see class comment, bug
            bodyStyle     :'padding:15px',
            defaultType   : 'textfield',
            fieldDefaults : {
                msgTarget  : 'side',
                labelWidth : 130
            },
            defaults      : {
                anchor : '50%'
            },
            items         : [{
                xtype : 'hidden',
                name  : 'id'
            },{
                xtype      : 'displayfield',
                name       : 'username',
                fieldLabel : 'Username'
            },{
                name       : 'password_input',
                fieldLabel : 'New Password',
                inputType  : 'password',
                minLength  : 8
            }, {
                name       : 'password_confirm',
                fieldLabel : 'Confirm Password',
                inputType  : 'password',
                validator  : function(value) {
                    var p1 = me.down('[name=password_input]');
                    return (value === p1.getValue()) ? true : 'Passwords do not match.'
                }
            }],
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Save',
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