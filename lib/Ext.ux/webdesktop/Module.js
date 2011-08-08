/**
 * Base Class for every module/controller in the webdesktop
 *
 * A new module with its controllers must extend from this class,
 * because it provides special funtionality and easy references.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux
 * @subpackage Webdesktop
 * @namespace Ext.ux.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Ext.ux.webdesktop.Module
 * @extends Ext.app.Controller
 */
Ext.define('Ext.ux.webdesktop.Module', {
    extend : 'Ext.app.Controller',
    /**
     * Init function of the controller
     *
     * Is fired before the launch event if the app starts a controller.
     * this give us the time to load module CSS files.
     *
     * @param {Ext.app.Application} application
     * @param {Webdesktop.controller.webdesktop.Desktop} desktopController
     */
    init   : function(application, desktopController) {
        // template method, no CallParent because init() in app.Controller has no function
        var me = this;
        if(me.useCss) {
            if(Ext.isArray(me.useCss)) {
                Ext.each(me.useCss, me.application.loadCssFile, me.application);
            } else {
                me.application.loadCssFile(me.useCss);
            }
        }
        me.desktopController = desktopController; // we want to know, which desktopcontroller loaded the module
        me.isInit = true;
    },
    /**
     * Get the DesktopController instance
     *
     * @return {Webdesktop.controller.webdesktop.Desktop}
     */
    getDesktop: function() {
        return this.desktopController;
    },
    /**
     * Get the application instance
     *
     * @return {Ext.app.Application}
     */
    getApplication: function() {
        return this.application;
    }
});