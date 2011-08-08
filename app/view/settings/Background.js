/**
 * The Settings Wallpaper panel
 *
 * User can change the wallpaper
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Settings
 * @namespace Webdesktop.view.settings
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.settings.Background
 * @extends Ext.panel.Panel
 * @alias settings_background
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 */
Ext.define('Webdesktop.view.settings.Background', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.settings_background',

    initComponent: function() {
        var me = this;
        Ext.apply(me, {
            border   : false, //FIXME: see class comment, bug
            frame    : false,
            layout   : 'border',
            defaults : {
                border : false, //FIXME: see class comment, bug
            },
            buttons  : [{
                 text       : 'Save',
                 actionType : 'save'
            }],
            items    : [{
                xtype    : 'panel',
                title    : 'Available Wallpapers',
                region   : 'west',
                layout   : 'fit',
                split    : true,
                width    : 150,
                minWidth : 100,
                defaults : {
                    border : false, //FIXME: see class comment, bug
                },
                items: [{
                    xtype  : 'panel',
                    layout : 'border',
                    split  : true,
                    items  : [{
                        xtype        : 'dataview',
                        region       : 'center',
                        autoScroll   : true,
                        store        : 'Webdesktop.store.settings.Wallpapers',
                        itemSelector : 'div.x-item',
                        trackOver    : true,
                        itemCls      : 'x-item',
                        overItemCls  : 'x-item-over',
                        selModel     : {
                            deselectOnContainerClick: false
                        },
                        style       : {
                            backgroundColor: 'white'  //FIXME: create CSS class
                        },
                        tpl: [
                            '<tpl for=".">',
                                '<div class="x-item">',
                                    '<h3>{name}</h3>',
                                    '<span>some description</span>',
                                '</div>',
                            '</tpl>'
                        ]
                    }, {
                        xtype       : 'panel',
                        region      : 'south',
                        title       : 'Options',
                        collapsible : true,
                        border      : false, //FIXME: see class comment, bug
                        height      : 50,
                        items       : [{
                            xtype      : 'checkboxfield',
                            name       : 'stretch',
                            fieldLabel : 'Stretch to Fit'
                        }]
                    }]
                }]
            }, {
                xtype  : 'panel',
                title  : 'Preview',
                region : 'center',
                layout : 'fit',
                items  : [{
                    xtype : 'image',
                    src   : Ext.BLANK_IMAGE_URL
                }]
            }]
        });

        me.callParent();
    }
});