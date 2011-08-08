/**
 * The Settings Color panel
 *
 * The user can change the fore/backgroundcolor of the webdesktop.
 *
 * Forecolor: Basic font-color
 * Background: If no wallpaper is selected, the background color is used
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
 * @class Webdesktop.view.settings.Color
 * @extends Ext.panel.Panel
 * @alias settings_color
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo this is currently a dummy, fill with some content
 */
Ext.define('Webdesktop.view.settings.Color', {
    extend : 'Ext.panel.Panel',
    alias  : 'widget.settings_color',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            border : false,
            frame  : false,
            title  : 'Color Settings',
            html   : "Color settings aren't implemented yet. Would be: Change basic fore- and background color"
        });
        
        me.callParent();
    }
});