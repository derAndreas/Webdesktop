/**
 * The desktop panel
 *
 * Main Panel in the viewport that contains the wallpaper,shortcut view and
 * the taskbar as a toolbar.
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
 * @class Webdesktop.view.webdesktop.Desktop
 * @extends Ext.panel.Panel
 * @alias webdesktop_desktop
 */
Ext.define('Webdesktop.view.webdesktop.Desktop', {
    extend : 'Ext.panel.Panel',
    alias  : 'widget.webdesktop_desktop',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            border : false,
            html   : '&#160;',
            itemId : 'desktop-panel',
            layout : 'fit',
            bbar   : {
                xtype : 'webdesktop_taskbar'
            },
            items  : [{
                xtype : 'webdesktop_wallpaper',
                id    : me.id + '_wallpaper'
            }, {
                xtype        : 'dataview',
                itemId       : 'ux-shortcut',
                overItemCls  : 'x-view-over',
                itemSelector : 'div.ux-desktop-shortcut',
                trackOver    : true,
                store        : Ext.data.StoreManager.get('Webdesktop.store.webdesktop.Shortcuts'), // get the store from controller
                style        : {
                    position : 'absolute'  //FIXME: Create CSS Class
                },
                tpl: [
                    '<tpl for=".">',
                        '<div class="ux-desktop-shortcut" id="{name}-shortcut">',
                            '<div class="ux-desktop-shortcut-icon {iconCls}">',
                                '<img src="',Ext.BLANK_IMAGE_URL,'" title="{name}">',
                            '</div>',
                            '<span class="ux-desktop-shortcut-text">{name}</span>',
                        '</div>',
                    '</tpl>',
                    '<div class="x-clear"></div>'
                ]
            }]
        });
        
        me.callParent(arguments);
    }
});
