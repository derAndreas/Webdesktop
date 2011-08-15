/**
 * Desktop Manager for a desktop controller
 *
 * This manager should be implented as a mixin to have the bundling between
 * desktop controller and window manager.
 *
 * It handles all kind of window stuff and events around the desktop panel, taskbar and so on
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux
 * @subpackage webdesktop
 * @namespace Ext.ux.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Ext.ux.webdesktop.Manager
 */
Ext.define('Ext.ux.webdesktop.Manager', {
    /**
     * @cfg {Webdesktop.controller.webdesktop.Desktop} desktop The desktop this manager is bound to
     * @deprecated because implentation should be used as mixin in the controller
     */
    desktop: null,
    /**
     * @cfg {Ext.ux.webdesktop.ModuleCollection} modules all modules that are available for the user
     */
    modules: null,
    /**
     * @cfg {Ext.util.MixedCollection} windows all windows that are inited
     */
    windows: null,
    /**
     * @cfg {Ext.window.Window} lastActiveWindow Contains the last active window
     */
    lastActiveWindow: null,
    /**
     * @constructor
     *
     * Setup the variables that are defined as class poperties
     */
    constructor: function(config) {
        var me = this;

        me.desktop = config.desktop;
        me.windows = Ext.create('Ext.util.MixedCollection');
        me.modules = Ext.create('Ext.ux.webdesktop.ModuleCollection', {
            listeners: {
                add: me.onAddModule,
                scope: me
            }
        });
    },
    /**
     * Add a window to the desktop and add all event listeners
     *
     * This methods add a window to the desktop. To do so , check if the
     * window does not already exists, if not create the window widget.
     * To identify if a window exists the param $options has multiple
     * identification attributes and is also importand for creating the window
     *
     * identification attributes: alias, itemId
     *
     * @param {Object} options query and
     * @return {Ext.window.Window}
     */
    addWindow: function(options, config) {
        var me      = this,
            sf      = Ext.String.format,
            q       = '',
            options = options || {widget: 'window'},
            config  = config || {},
            exWin   = me.windows.get(options.id),
            win;

        if(exWin) {
            return exWin;
        }

        config = Ext.applyIf(config, {
            stateful: false,
            isWindow: true,
            constrainHeader: true,
            minimizable: true,
            maximizable: true
        });

        if(options.alias) {
            win = Ext.create(options.alias, config);
        } else if(options.widget) {
            win = Ext.widget(options.widget, config);
        } else {
            throw 'Unknown property to create new window in Ext.ux.webdesktop.Manager::addWindow()';
        }
        
        me.windows.add(win);

        // do not add the taskbutton back reference here, can cause problems because of asynch loading
        // of the views and controller (taskbar not available with module.launcher.autorun
        // win.taskButton = me.desktop.addTaskButton(win);
        // win.animateTarget = win.taskButton.el;
        me.desktop.addTaskButton(win); // set the back reference between window and taskbutton in the addTaskButton function

        win.on({
            activate    : me.updateActiveWindow,
            beforeshow  : me.updateActiveWindow,
            deactivate  : me.updateActiveWindow,
            minimize    : me.onWindowMinimize,
            destroy     : me.onWindowClose,
            scope       : me
        });

        // replace normal window close w/fadeOut animation:
        win.doClose = function ()  {
            win.doClose = Ext.emptyFn; // dblclick can call again...
            win.el.disableShadow();
            win.el.fadeOut({
                listeners: {
                    afteranimate: function () {
                        win.destroy();
                    }
                }
            });
        };

        return win;
    },

    /**
     * Get a window
     *
     * Parameter can be a window instance or string / key
     *
     * @param {Ext.window.Window|String|Number} key
     * @return {Ext.window.Window}
     */
    getWindow: function(win) {
        return this.windows.get(win);
    },

    /**
     * Return all modules
     *
     * @return {Ext.ux.webdesktop.ModuleCollection}
     */
    getModules: function() {
        return this.modules;
    },


    // ------------------------------- EVENT HANDLING



    /**
     * Called when a module is added to the ModuleCollection
     * The notable part is the delay to add the element to the startmenu.
     * The redering is not finished if the modules are loaded onload,
     * so there is a 500ms delay.
     *
     * @param {Number} i The index at which the item was added.
     * @param {Object} module the module configuration
     * @param {String} key The key associated with the added item.
     * @todo refactor the code and make it more readable
     * @todo create testcases to validate if the delay waiting for startmenu is enough
     */
    onAddModule: function(i, module, key) {
        var me = this,
            fn = Ext.bind(function(module) {
                me.desktop.application.loadCssFile(module.moduleId + '/module.css');
                var config = Ext.apply(module.launcher, {
                    moduleId: module.moduleId
                });
                if(module.menuPath == 'toolmenu') {
                    // toolbar entry
                    me.desktop.getToolBar().insert(0, config);
                } else if(Ext.isString(module.menuPath)) {
                    // Startmenu entry
                    var target = me.desktop.getStartMenu().menu.down('#ux-start-menu-' + module.menuPath) ? 
                        me.desktop.getStartMenu().menu.down('#ux-start-menu-' + module.menuPath) :   
                        me.desktop.getStartMenu(); 
                    target.menu.add(config);
                }
            }, me);

        if(!me.desktop.getStartMenu()) {
            Ext.defer(fn, 500, me, [module]);
        } else {
            fn(module);
        }
    },
    /**
     * update the active window state
     */
    updateActiveWindow: function() {
        var me = this,
            desktop = me.desktop,
            activeWindow = me.getActiveWindow(),
            last = me.lastActiveWindow;

        if (activeWindow === last) {
            return;
        }

        if (last) {
            if (last.el.dom) {
                last.addCls(desktop.getInactiveWindowCls());
                last.removeCls(desktop.getActiveWindowCls());
            }
            last.active = false;
        }

        me.lastActiveWindow = activeWindow;

        if (activeWindow) {
            activeWindow.addCls(desktop.getActiveWindowCls());
            activeWindow.removeCls(desktop.getInactiveWindowCls());
            activeWindow.minimized = false;
            activeWindow.active = true;
        }

        desktop.setActiveButton(activeWindow && activeWindow.taskButton);
    },
    /**
     * Get the active window
     *
     * From the original ExtJs4 Desktop Example
     *
     * @return {Ext.window.Window} window
     */
    getActiveWindow: function () {
        var win = null,
            zmgr = this.getDesktopZIndexManager();

        if (zmgr) {
            // We cannot rely on activate/deactive because that fires against non-Window
            // components in the stack.

            zmgr.eachTopDown(function (comp) {
                if (comp.isWindow && !comp.hidden) {
                    win = comp;
                    return false;
                }
                return true;
            });
        }

        return win;
    },
    /**
     * Get the ZIndexManager for the Desktop
     *
     * From the original ExtJs4 Desktop Example
     *
     * Returns the ZIndexManager by looking at the first window in the
     * collection and returns the (global) ZIndexManager.
     *
     * @return {Ext.ZIndexManager
     */
    getDesktopZIndexManager: function () {
        var windows = this.windows;
        // TODO - there has to be a better way to get this...
        return (windows.getCount() && windows.getAt(0).zIndexManager) || null;
    },
    /**
     * Event on minimizing window
     *
     * @param {Ext.window.Window} win window that is minimized
     */
    onWindowMinimize: function(win) {
        win.taskButton.toggle(false);
        win.minimized = true;
        win.hide();
    },
    /**
     * Event on closing a window
     *
     * @param {Ext.window.Window} win window that is minimized
     */
    onWindowClose: function(win) {
        var me = this;
        me.windows.remove(win);
        me.desktop.removeTaskButton(win.taskButton);
        me.updateActiveWindow();
    },
    /**
     * Fires / called when a window should be restored
     *
     * @param {Ext.window.Window} win
     * @return Ext.window.Window
     */
    onRestoreWindow: function (win) {
        if (win.isVisible()) {
            win.restore();
            win.toFront();
        } else {
            win.show();
        }
        return win;
    }
});
