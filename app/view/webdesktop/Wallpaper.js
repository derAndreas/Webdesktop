/**
 * The desktop Wallpapper
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
 * @class Webdesktop.view.webdesktop.Wallpaper
 * @extends Ext.Component
 * @alias webdesktop_wallpaper
 */
Ext.define('Webdesktop.view.webdesktop.Wallpaper', {
    extend    : 'Ext.Component',
    alias     : 'widget.webdesktop_wallpaper',
    cls       : 'ux-wallpaper',
    html      : '<img src="'+Ext.BLANK_IMAGE_URL+'">',

    /**
     * @cfg {Boolean} strech status flag if wallpaper should be streched
     */
    stretch   : false,
    /**
     * @cfg {String} wallpaper The current wallpaper file
     */
    wallpaper: null,
    /**
     * Set the Wallpaper after render
     */
    afterRender: function () {
        var me = this;
        me.callParent();
        me.setWallpaper(me.wallpaper, me.stretch);
    },
    /**
     * Apply the new state of the wallpaper
     * @todo ehm, what is this doing?!, fix docs
     */
    applyState: function () {
        var me = this, old = me.wallpaper;
        me.callParent(arguments);
        if (old != me.wallpaper) {
            me.setWallpaper(me.wallpaper);
        }
    },

    /**
     * Get the current state of the wallpaper
     * @todo don't know what that means, fix docs
     */
    getState: function () {
        return this.wallpaper && { wallpaper: this.wallpaper };
    },

    /**
     * Set the wallpaper on the desktops dataview
     *
     * Handle the CSS class if stretching is active.
     * Set EXT BLANK IMG if no Wallpaper is selected
     *
     * @param {String} wallpaper
     * @param {Boolean} stretch
     * @return {Webdesktop.view.webdesktop.Wallpaper}
     */
    setWallpaper: function (wallpaper, stretch) {
        var me = this,
            imgEl, bkgnd;

        me.stretch   = (stretch !== false);
        me.wallpaper = wallpaper;

        if (me.rendered) {
            imgEl = me.el.dom.firstChild;

            if (!wallpaper || wallpaper == Ext.BLANK_IMAGE_URL) {
                Ext.fly(imgEl).hide();
            } else if (me.stretch) {
                imgEl.src = wallpaper;

                me.el.removeCls('ux-wallpaper-tiled');
                Ext.fly(imgEl).setStyle({
                    width: '100%',
                    height: '100%'
                }).show();
            } else {
                Ext.fly(imgEl).hide();

                bkgnd = 'url('+wallpaper+')';
                me.el.addCls('ux-wallpaper-tiled');
            }

            me.el.setStyle({
                backgroundImage: bkgnd || ''
            });
        }
        return me;
    }
});