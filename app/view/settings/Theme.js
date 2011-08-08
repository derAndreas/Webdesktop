/**
 * The Settings Theme panel
 *
 * User can change the theme of the webdesktop. Supported themes are only
 * the sencha basic ones. Because some elements are custom designed, in the
 * future create mixins with scss for the desktop theming
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
 * @class Webdesktop.view.settings.Theme
 * @extends Ext.panel.Panel
 * @alias settings_theme
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo add a preview functionality
 */
Ext.define('Webdesktop.view.settings.Theme', {
    extend : 'Ext.panel.Panel',
    alias  : 'widget.settings_theme',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            border : false, //FIXME: see class comment, bug
            frame  : false,
            title  : 'Theme selector',
            layout : 'fit',
            cls    : 'themes-selector',
            items  : [{
                xtype        : 'dataview',
                autoScroll   : true,
                store        : 'Webdesktop.store.settings.Themes',
                trackOver    : true,
                overItemCls  : 'x-item-over',
                itemSelector : 'div.thumb-wrap',
                selModel     : {
                    deselectOnContainerClick : false
                },
                tpl: [
                    '<tpl for=".">',
                        '<div class="thumb-wrap" id="{name}">',
                        '<div class="thumb"><img src="'+me.initialConfig.previewUrl+'{preview}" title="{name}"></div>',
                        '<span class="x-editable">{name}</span></div>',
                    '</tpl>',
                    '<div class="x-clear"></div>'
                ]
            }],
            buttons : [{
                text       : 'Save',
                actionType : 'save'
            }
            /*,{
                //todo: something in the future
                text       : 'Preview',
                actionType : 'preview'
            } */
            ]
        });

        me.callParent();
    }
});