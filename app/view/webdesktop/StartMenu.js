/**
 * The desktop startmenu
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Webdesktop
 * @namespace Webdesktop.view.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.webdesktop.StartMenu
 * @extends Ext.panel.Panel
 * @alias webdesktop_startmenu
 * @todo add logout handler
 * @todo several small fixes regarding the code, see inline
 */
Ext.define('Webdesktop.view.webdesktop.StartMenu', {
    extend   : 'Ext.panel.Panel',
    alias    : 'widget.webdesktop_startmenu',
    requires : [
        'Ext.menu.Menu',
        'Ext.toolbar.Toolbar'
    ],
    /**
     * @cfg {Ext.menu.Menu} menu The menu behind the startmenu
     */
    menu : null,

    initComponent: function() {
        var me = this;

        // FIXME: Need to place menu config here, see description
        //        below in the items configuragion of the panel
        me.menu = Ext.create('Ext.menu.Menu', {
            border   : false,
            floating : false,
            items    : [{
                text    : 'Programs',
                itemId  : 'ux-start-menu-programs',
                iconCls : '', //FIXME: add icon
                menu    : {
                    items: []
                }
            }]
        });
        me.menu.layout.align = 'stretch'; // FIXME: Move into menu creation above

        Ext.apply(me, {
            title        : '', //fixme: get the username from the user init config and place it here
            layout       : 'fit',
            width        : 300,
            height       : 300,
            iconCls      : 'user',
            ariaRole     : 'menu',
            cls          : 'x-menu ux-start-menu',
            defaultAlign : 'bl-tl',
            floating     : true,
            shadow       : true,
            items        : me.menu,
            dockedItems  : {
                xtype    : 'toolbar',
                dock     : 'right',
                cls      : 'ux-start-menu-toolbar',
                vertical : true,
                width    : 100,
                layout   : {
                    align : 'stretch'
                },
                items    : [
                    '->',
                    {
                        text    :'Logout',
                        iconCls :'logout',
                        handler : Ext.emptyFn,      // FIXME: Add Logout handler
                        scope   : me
                    }
                ]
            }
            /*
            FIXME:  Cannot directly place the menu config here.
                    the layout.align does not work. defined menu like in original example
                    above
            [
                {
                    xtype: 'menu',
                    border: false,
                    floating: false,
                    layout: {
                        type: 'auto',
                        align: 'stretch'
                    },
                    items: [{
                        text: 'Programs',
                        menu: {
                            items: [{text: 'demo'}]
                        }
                    }]
                }
            ]
            */
        });
        me.callParent();
        
        Ext.menu.Manager.register(me);

        me.on('deactivate', function () {
            me.hide();
        });
    },
    /**
     * Calculate the floating menu position to be on top of the start button
     *
     * @param {Ext.button.Button} cmp
     * @param {Object} pos
     * @param {Object} off
     * @return {Webdesktop.view.webdesktop.StartMenu}
     */
    showBy: function(cmp, pos, off) {
        var me = this;

        if (me.floating && cmp) {
            me.layout.autoSize = true;
            me.show();

            // Component or Element
            cmp = cmp.el || cmp;

            // Convert absolute to floatParent-relative coordinates if necessary.
            var xy = me.el.getAlignToXY(cmp, pos || me.defaultAlign, off);
            if (me.floatParent) {
                var r = me.floatParent.getTargetEl().getViewRegion();
                xy[0] -= r.x;
                xy[1] -= r.y;
            }
            me.showAt(xy);
            me.doConstrain();
        }
        return me;
    }
});