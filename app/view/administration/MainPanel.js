/**
 * The Window of the Administration Module
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.MainPanel
 * @extends Ext.window.Window
 * @alias administration_mainpanel
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 * @todo Add something for the "HOME" Tab. Maybe some graphs with Ext.charts about user statistics
 */
Ext.define('Webdesktop.view.administration.MainPanel', {
    extend   : 'Ext.window.Window',
    alias    : 'widget.administration_mainpanel',
    requires : [
        'Webdesktop.view.administration.Menu'
    ],

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            closable    : true, //FIXME: see class comment, bug
            border      : false, //FIXME: see class comment, bug
            title       : 'My Admin',
            cls         : 'module-administration',
            iconCls     : 'administration-icon',
            id          : 'admin_id',   // FIXME:needed
            width       : 900,
            height      : 560,
            layout      : 'fit',
            defaults    : {
                border : false //FIXME: see class comment, bug
            },
            dockedItems : [{
                dock   : 'left',
                border : false, //FIXME: see class comment, bug
                items  : [{
                    xtype : 'administration_menu'
                }]
            }],
            items : {
                xtype      : 'tabpanel',
                activeItem : 0,
                border     : false, //FIXME: see class comment, bug
                defaults   : {
                    closable : true, //FIXME: see class comment, bug
                    border   : false //FIXME: see class comment, bug
                },
                items : [{
                    title       : 'Home',
                    closable    : false,
                    bodyPadding : 10,
                    html        : '<h1>Welcome to Administration Module</h1>'
                }]
            }
        });
        me.callParent();
    }
});
