/**
 * The main viewport if the webdesktop
 *
 * Only set the desktop view panel
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
 * @class Webdesktop.view.Viewport
 * @extends Ext.container.Viewport
 * @alias webdesktop_viewport
 */
Ext.define('Webdesktop.view.Viewport', {
    extend   : 'Ext.container.Viewport',
    alias    : 'widget.webdesktop_viewport',
    requires : [
        'Webdesktop.view.webdesktop.Desktop',
        'Webdesktop.view.webdesktop.Wallpaper',
        'Ext.ux.layout.FitAll'
    ],
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            layout : 'fitall',
            items  : {
                xtype : 'webdesktop_desktop'
            }
        });
        me.callParent();
    }
});