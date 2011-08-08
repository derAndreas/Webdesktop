/**
 * Form for user Add
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
 * @class Webdesktop.view.administration.users.Add
 * @extends Webdesktop.view.administration.users.BaseForm
 * @alias administration_useradd
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.users.Add', {
    extend : 'Webdesktop.view.administration.users.BaseForm',
    alias  : 'widget.administration_useradd',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : 'Add new user',
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Save',
                    iconCls : 'ux-icon-add'
                },{
                    text    : 'Cancel',
                    iconCls : 'ux-icon-cancel'
                }]
            }]
        });

        me.callParent();
    }
});