/**
 * The desktop Taskbar
 *
 * The Taskbar is a toolbar that contains
 *  - start button
 *  - quickstart toolbar
 *  - windowbar toolbar
 *  - sytemtray toolbar
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
 * @class Webdesktop.view.webdesktop.Taskbar
 * @extends Ext.toolbar.Toolbar
 * @alias webdesktop_taskbar
 */
Ext.define('Webdesktop.view.webdesktop.Taskbar', {
    extend   : 'Ext.toolbar.Toolbar',
    alias    : 'widget.webdesktop_taskbar',
    requires : [
        'Ext.button.Button',
        'Ext.resizer.Splitter',
        'Ext.menu.Menu',
        'Webdesktop.view.webdesktop.StartMenu'
    ],
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            itemId : 'webdesktop-taskbar',
            cls    : 'ux-taskbar',
            items : [{
                    // start button
                    xtype     : 'button',
                    text      : 'Start',
                    cls       : 'ux-start-button',
                    iconCls   : 'ux-start-button-icon',
                    menuAlign : 'bl-tl',
                    menu      : {
                        xtype: 'webdesktop_startmenu'
                    }
                }, {
                    // quickstart
                    xtype          : 'toolbar',
                    cls            : 'ux-desktop-quickstart',
                    itemId         : 'ux-desktop-quickstart',
                    minWidth       : 20,
                    width          : 60,
                    enableOverflow : true,
                    items: []
                }, {
                    xtype   : 'splitter',
                    html    : '&#160;',
                    height  : 14, // FIXME - there should be a CSS way here
                    width   : 2, // FIXME - there should be a CSS way here
                    cls     : 'x-toolbar-separator x-toolbar-separator-horizontal'
                }, {
                    // windowbar
                    xtype   : 'toolbar',
                    flex    : 1,
                    cls     : 'ux-desktop-windowbar',
                    itemId  : 'ux-desktop-windowbar',
                    items   : [ '&#160;' ],
                    layout  : {
                        overflowHandler : 'Scroller'
                    }
                }, '-', {
                    // sytem tray
                    xtype   : 'toolbar',
                    cls     : 'ux-desktop-systemtray',
                    itemId  : 'ux-desktop-systemtray',
                    width   : 80,
                    items   : []
                }
            ]
        });
        me.callParent();
    }
});
