/**
 * Controller for the login in Webdesktop
 *
 * The login controller ensures that only logged in users can use the
 * webdesktop.
 *
 * @TODO
 * In the current implementation the login and sessionmanagement is completly
 * handled by the backend and this controller is more a passthrough of informations
 * from the global defined GLOBAL_USER_PROFILE to the desktop controller.
 * To get this login controller working there are several changes to make:
 *  - backend ACL must allow for every user to view webdesktop/index
 *  - add loadmasks and hold desktop controller launching after login
 *  - much more
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Login
 * @namespace Webdesktop.controller.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.controller.webdesktop.Login
 * @extends Ext.app.Controller
 *
 * @todo see above description and the big todo!
 */
Ext.define('Webdesktop.controller.webdesktop.Login' ,{
    extend: 'Ext.app.Controller',
    views: [
        'Webdesktop.view.webdesktop.Login'
    ],
    /**
     * Init the login controller
     *
     * in the current implementation in this method the else statement will always
     * match, because the login is completly in the backend (login page and webdesktop selection)
     */
    init: function() {
        var me = this;
        if(GLOBAL_USER_LOGGED_IN !== true || !Ext.isDefined(GLOBAL_USER_PROFILE) || !Ext.isObject(GLOBAL_USER_PROFILE)) {
            me.loginWindow = Ext.create('widget.webdesktop_login');
            me.control({
               '.webdesktop_login button[text="Login"]': {
                   click: me.onFormSubmit
               }
            });
        } else {
            me.application.getController('webdesktop.Desktop').bindUser(GLOBAL_USER_PROFILE);
        }
    },

    /**
     * Fires on Submit of the login form
     *
     * If successfully logged in, the user will forwarded to desktop controller
     * and on error a simple error message is shown.
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onFormSubmit: function(btn, event) {
        var me = this;
        var form = btn.up('form').getForm();
        if (form.isValid()) {
            form.submit({
                clientValidation: true,
                url: 'index/login',
                success: function(form, action) {
                    me.loginWindow.destroy();
                    me.application.getController('webdesktop.Desktop').bindUser(action.result.userDesktop);
                },
                failure: function(form, action) {
                    Ext.Msg.alert('Failed to Login', action.result.msg);
                }
            });
        }
    }
});