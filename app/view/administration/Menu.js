/**
 * The Window's main menu of the Administration Module
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
 * @class Webdesktop.view.administration.Menu
 * @extends Ext.toolbar.Toolbar
 * @alias administration_menu
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo Add something for the "HOME" Tab. Maybe some graphs with Ext.charts about user statistics
 * @todo add modules like controllers to the menu (need complete module part in controller / views / models etc)
 */
Ext.define('Webdesktop.view.administration.Menu', {
    extend : 'Ext.toolbar.Toolbar',
    alias  : 'widget.administration_menu',
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            border      : false, //FIXME: see class comment, bug
            frame       : false,
            vertical    : true,
            width       : 120,
            layout      : {
                autoSize : true,
                align    : 'stretch'
            },
            defaults    : {
                scale     : 'medium',
                iconAlign :'top'
            },
            items: [{
                xtype   :'splitbutton',
                text    : 'UserManagement',
                iconCls : 'icon-users',
                menu    : [{
                    text   : 'List Users',
                    action : 'list'
                }, {
                    text   : 'Add User',
                    action : 'add'
                }]
            },{
                text    : 'GroupManagement',
                iconCls : 'icon-groups',
                cls     : 'x-btn-as-arrow'
            },{
                text    : 'RoleManagement',
                iconCls : 'icon-roles',
                cls     : 'x-btn-as-arrow'
            },{
                text    : 'Controllers',
                iconCls : 'icon-controllers',
                cls     : 'x-btn-as-arrow'
            }]
        });
        
        me.callParent();
    }
});