/**
 * Controller for a Desktop in Webdesktop
 *
 * This is a desktop in the webdesktop enviroment. It is based on the original
 * non-MVC version of the Sencha ExtJs4 Release 4.0.2a.
 * It tries to use the power of the MVC pattern, which was introduced with ExtJs4.
 * Several new elements of the new design was used, just as a proof of concept like
 *  - own mixins ( Desktop Manager Mixin)
 *  - config
 *  - refs
 *  - .. and much more
 *
 * There are many problems to solve and features to add. This is not production ready!
 *
 * Main File in the backend is:
 * There is no main file in the backend for the desktop controller. The Desktop
 * Controller controls just the frontend and the modules (other controllers).
 * There was a discussion on the Sencha forum about multiple applications in one
 * application, but this should show, that it is possible to have multiple
 * apps encapsulated in controllers and not complete applications.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Desktop
 * @namespace Webdesktop.controller.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.controller.webdesktop.Desktop
 * @extends Ext.app.Controller
 *
 * @todo Multiple Desktop (multi desktop environment)
 *          This should be possible which one Ext.ZIndexManager per Desktop Controller/Manager
 *          Problem here is, how to post bind a ZIndexManager to a Desktop Viewport, as
 *          the viewport is not rendered on the time, where ZIndexManager is created and needed.
 * @todo Move more dekstop event handling into the manager mixin
 * @todo add cascade/tile active windows, need to update config context menu and write the event handler
 */
Ext.define('Webdesktop.controller.webdesktop.Desktop' ,{
    extend: 'Ext.app.Controller',
    mixins: {
        manager: 'Ext.ux.webdesktop.Manager'
    },
    requires: [
        'Webdesktop.view.webdesktop.Taskbar',
        'Ext.ux.webdesktop.Module',
        'Ext.ux.webdesktop.ModuleCollection',
        'Ext.ux.data.proxy.ZfProxy'
    ],
    stores: [
        'Webdesktop.store.webdesktop.Shortcuts'
    ],
    refs: [
        { ref: 'taskBar',       selector: 'webdesktop_taskbar'},
        { ref: 'startMenu',     selector: 'webdesktop_startmenu'},
        { ref: 'toolBar',       selector: 'webdesktop_startmenu toolbar[cls=ux-start-menu-toolbar]'},
        { ref: 'quickStart',    selector: 'webdesktop_taskbar #ux-desktop-quickstart'},
        { ref: 'windowBar',     selector: 'webdesktop_taskbar #ux-desktop-windowbar'},
        { ref: 'systemTray',    selector: 'webdesktop_taskbar #ux-desktop-systemtray'},
        { ref: 'windowBarContextMenu', selector: '#taskbar-contextmenu'},
        { ref: 'viewPort',      selector: 'webdesktop_viewport'},
        { ref: 'wallpaper',     selector: 'webdesktop_wallpaper'},
        { ref: 'shortcutsView', selector: 'webdesktop_desktop dataview[itemId=ux-shortcut]',}
    ],

    config: {
        /**
         * URL to load the Wallpapers
         * Provided after the this::bindUser() is called
         * @cfg {String} wpUrl
         */
        wpUrl               : '',
        /**
         * URL to load the Themes
         * Provided after the this::bindUser() is called
         * @cfg {String} themesUrl
         */
        themesUrl           : '',
        /**
         * URL to load the preview images of themes
         * Provided after the this::bindUser() is called
         * @cfg {String} themePreviewUrl
         */
        themePreviewUrl     : '',
        /**
         * The complete user config from the backend after the user logged in
         * Provided after the this::bindUser() is called
         * @cfg {Object} userConfig
         * @todo check if it is better in the new style, to use a model/store here
         */
        userConfig          : {},
        /**
         * currently selected Theme
         * @cfg {String} theme (default to 'ext-all.css' / sencha default blue)
         */
        theme               : 'ext-all.css',
        /**
         * CSS class to append to the active window
         * @cfg {String} activeWindowCls
         */
        activeWindowCls     : 'ux-desktop-active-win',
        /**
         * CSS class to append to inactive window(s)
         * @cfg {String} inactiveWindowCls
         */
        inactiveWindowCls   : 'ux-desktop-inactive-win',
        /**
         * Config for the contextmenu of the windowbar
         * This is the basic config for the contextmenu and states are
         * determined before show
         *
         * @cfg {Object} taskbarWindowMenu
         * @todo add something like cascade/tile all active windows
         */
        taskbarWindowMenu   : {
            defaultAlign    : 'br-tr',
            itemId          : 'taskbar-contextmenu',
            items           : [
                { text: 'Restore',  actionType: 'restore'},
                { text: 'Minimize', actionType: 'minimize'},
                { text: 'Maximize', actionType: 'maximize'},
                '-',
                { text: 'Close',    actionType: 'close'}
            ]
        },
        /**
         * Config for the quickstart buttons
         * placeholders will be overwritten in self::onBeforeAddQuickstart()
         *
         * @cfg {Object} quickStartButton
         * @todo rename to quickStartButtonTemplate (and all usages)
         */
        quickStartButton: {
            text: null,
            overflowText: 'placeholder',
            tooltip: {
                text: 'placeholder',
                align: 'bl-tl'
            }
        }
    },

    /**
     * Variable for the shortcuts store
     *
     * @cfg {Ext.data.Store} shortcuts
     * @todo Shortcuts store is loaded within the store config, so we can access it
     *       through getter function or a ref?
     */
    shortcuts: null,

    /**
     * Init function for initialization of the desktop controller
     */
    init: function() {
        var me = this;
        me.initConfig(this.config);

        me.mixins.manager.constructor.call(this, {desktop: me});

        me.shortcuts  = Ext.data.StoreManager.get('Webdesktop.store.webdesktop.Shortcuts'); //FIXME: see @todo of variable definition, replace with getter
        me.windowMenu = Ext.create('Ext.menu.Menu', me.getTaskbarWindowMenu());

        me.control({
            'webdesktop_viewport': {
                afterlayout: me.initWindowBarMenu
            },
            '#taskbar-contextmenu': {
                beforeshow: me.onWindowMenuBeforeShow,
                hide: me.onWindowMenuHide
            },
            '#taskbar-contextmenu > menuitem[actionType=restore]': {
                click: me.onWindowMenuRestore
            },
            '#taskbar-contextmenu > menuitem[actionType=minimize]': {
                click: me.onWindowMenuMinimize
            },
            '#taskbar-contextmenu > menuitem[actionType=maximize]': {
                click: me.onWindowMenuMaximize
            },
            '#taskbar-contextmenu > menuitem[actionType=close]': {
                click: me.onWindowMenuClose
            },
            'webdesktop_taskbar #ux-desktop-quickstart': {
                beforeadd: me.onBeforeAddQuickstart
            },
            'webdesktop_taskbar #ux-desktop-quickstart > button': {
                click: me.onClickModuleHandler
            },
            'webdesktop_startmenu menu menuitem': {
                click: me.onClickModuleHandler
            },
            'webdesktop_startmenu toolbar[dock=right] button': {
                click: me.onClickModuleHandler
            },
            'webdesktop_desktop dataview[itemId=ux-shortcut]': {
                itemclick: me.onClickShortcut
            },
            'webdesktop_startmenu': {
                afterrender: me.onStartMenuAfterRender
            }
        });

        
    },

    /**
     * Get the Desktop Manager
     *
     * Return the instance of Ext.ux.webdesktop.Manager.
     * I think its replaced by the Manager mixin, deprecate it
     *
     * @return Ext.ux.webdesktop.Manager
     * @deprecated
     */
    getManager: function() {
        return this.manager;
    },

    /**
     * Bind the User data to the desktop
     *
     * After the login is successfull the desktop is bound to the logged in user.
     * This means that basic informations like URLs are set and user
     * informations like theme, background, shortcuts, autoruns and quickstarts
     * are set and used during desktop startup.
     *
     * @param {Object} config
     * @todo refactor
     */
    bindUser: function(config) {
        var me        = this,
            launchers = config.launchers || {},
            modules   = config.modules || {},
            style     = config.style || {};

        me.application.apiUrl  = config.apiUrl;
        me.setWpUrl(config.wpUrl);
        me.setThemesUrl(config.themesUrl);
        me.setThemePreviewUrl(config.themePreviewUrl);
        me.setUserConfig(config);

        me.getModules().addAll(modules);

        // helper functions because of Ext.defer usage

        /**
         * Add a module to the quickstart bar
         *
         * @param {Object} Module
         * @scope me
         */
        var fnAddQuick = Ext.bind(function(module) {
            me.getQuickStart().add(Ext.apply({ // getQuickStart()
                moduleId: module.moduleId
            }, module.launcher));
        }, me);

        // autorun
        Ext.each(launchers.autorun, function(item) {
            var module = me.getModules().get(item),
                cn = me.application.getController(module.controller);
                if(!cn.isInit) {
                    cn.init(me.application, me);
                }
                cn.launch();
        }, me);

        // check the quickstart elements
        Ext.each(launchers.quickstart, function(item) {
            var module = me.getModules().get(item);
            if(!me.getQuickStart()) {
                // its not rendered yet, give it some time
                Ext.defer(fnAddQuick, 300, me, [module]);
            } else {
                fnAddQuick(module);
            }
        }, me);

        // create desktop shortcuts
        Ext.each(launchers.shortcut, function(item) {
            me.shortcuts.add(me.getModules().get(item));
        }, me);

        // FIXME: test adding style loading

        Ext.defer(function(file, pos) {
            me.getWallpaper().setWallpaper(file, pos);
        }, 500, me, [style.wallpaper.file, style.wallpaper.position]);

        if(style.theme && style.theme.id && style.theme.src){
            me.setUITheme(style.theme.src);
        }

    },

    /**************************************************************************\
    | EVENT BINDING
    \**************************************************************************/

    /**
     * Bind the ContextMenu to the windowbar
     */
    initWindowBarMenu: function() {
        this.getWindowBar().el.on('contextmenu', this.onWindowBarButtonContextMenu, this);
    },
    /**
     * Modify the button before added to the QuickStart
     *
     * @param {Ext.toolbar.Toolbar} toolbar
     * @param {Ext.button.Button} button
     * @return Ext.button.Button
     */
    onBeforeAddQuickstart: function(toolbar, button) {
        return Ext.apply(
            button,
            Ext.apply(this.getQuickStartButton(), {
                overflowText: button.text,
                tooltip     : {
                    text : button.text,
                }
            }
        ));
    },

    /**
     * Fires when a button (quickstart/startmenu) is clicked
     *
     * Launches the module controller
     *
     * @param {Ext.button.Button|Ext.menu.Item|Object} cmp
     * @event {Object} event
     * @return boolean
     */
    onClickModuleHandler: function(cmp, event) {
        if(!cmp.moduleId) {
            return false;
        }
        var me     = this,
            module = me.getModules().get(cmp.moduleId),
            cn     = me.application.getController(module.controller);
        if(!cn.isInit) {
            cn.init(me.application, me);
        }
        cn.launch();
        return true;
    },

    /**
     * Fires when a shortcut is clicked
     *
     * @uses self::onClickModuleHandler() to launch the module
     * @param {Ext.view.View} view
     * @param {Ext.data.Model} record
     * @return {Boolean}
     */
    onClickShortcut: function(view, record) {
        var me     = this,
            module = me.getModules().get(record.get('moduleId'));
        return me.onClickModuleHandler(module)
    },


    /**
     * Show and place the context menu when right clicked
     * on the window bar element
     *
     * @param {Object} event
     */
    onWindowBarButtonContextMenu: function(event) {
        var me  = this,
            t   = event.getTarget(),
            btn = this.getWindowBar().getChildByElement(t) || null;
        if (btn) {
            event.stopEvent();
            me.windowMenu.currentWindow = btn.win;
            me.windowMenu.showBy(t);
        }
    },
    /**
     * fires before the context menu on the windowbar is shown
     * set the contents of the contextmenu to the current window options
     *
     * @param {Ext.menu.Menu} menu
     */
    onWindowMenuBeforeShow: function(menu) {
        var items = menu.items.items,
            win   = menu.currentWindow;
        items[0].setDisabled(win.maximized !== true && win.hidden !== true); // Restore
        items[1].setDisabled(win.minimized === true); // Minimize
        items[2].setDisabled(win.maximized === true || win.hidden === true); // Maximize
    },
    /**
     * fires when the contextmenu hides
     */
    onWindowMenuHide: function() {
        this.getWindowBarContextMenu().currentWindow = null;
    },
    /**
     * fires when click "restore" in the contextmenu
     * restores a minized window
     */
    onWindowMenuRestore: function() {
        var me  = this,
            win = me.getWindowBarContextMenu().currentWindow;
        me.onRestoreWindow(win);
    },
    /**
     * fires when clicking "minimize" in the context menu
     * minimize the window
     */
    onWindowMenuMinimize: function() {
        this.getWindowBarContextMenu().currentWindow.minimize();
    },
    /**
     * fires when clicking "maximize" in the context menu
     * maximize the window
     */
    onWindowMenuMaximize: function() {
        this.getWindowBarContextMenu().currentWindow.maximize();
    },
    /**
     * fires when clicking "close" in the context menu
     * close the window
     */
    onWindowMenuClose: function() {
        this.getWindowBarContextMenu().currentWindow.close();
    },

    /**
     * fires after the startmenu renders
     * Set the title of the panel
     *
     * @param {Webdesktop.view.webdesktop.StartMenu} panel
     * @todo Need user informations with the load of the page in GLOBAL_USER_PROFILE to set the title here
     */
    onStartMenuAfterRender: function(panel) {
        panel.setTitle('Welcome!'); // FIXME: @todo see methods doc
    },

    /**************************************************************************\
    | Supporting methods
    \**************************************************************************/

    /**
     * Add element to windowbar
     *
     * @param {Ext.window.Window} win
     * @todo move to Ext.ux.webdesktop.Manager
     * @todo rename to addWindowBarButton
     */
    addTaskButton: function(win) {
        var me = this,
            fn = function(config, win) {
                var cmp = me.getWindowBar().add(config);
                cmp.toggle(true);
                // add the taskbutton reference to the window here
                win.taskButton = cmp;
                win.animateTarget = cmp.el;
            },
            config = {
                iconCls      : win.iconCls,
                enableToggle : true,
                toggleGroup  : 'all',
                width        : 140,
                text         : Ext.util.Format.ellipsis(win.title, 20),
                win          : win,
                listeners    : {
                    click : me.onWindowBtnClick,
                    scope : me
                }
            };

        // if windowbar isnt rendered yet, defer
        if(!me.getWindowBar()) {
            Ext.defer(fn, 500, me, [config, win]);
        } else {
            fn(config, win);
        }
    },
    /**
     * Remove a button from the WindowBar
     *
     * @param {Ext.button.Button} btn
     * @return {Ext.button.Button} found
     * @todo move to Ext.ux.webdesktop.Manager
     * @todo rename to removeWindowBarButton
     */
    removeTaskButton: function(btn) {
        var me = this,
            found;
        me.getWindowBar().items.each(function (item) {
            if (item === btn) {
                found = item;
            }
            return !found;
        });
        if (found) {
            me.getWindowBar().remove(found);
        }
        return found;
    },
    /**
     * Mark a button in the WindowBar as active element
     *
     * @param {Ext.button.Button} btn
     * @todo move to Ext.ux.webdesktop.Manager
     * @todo rename to setActiveWindowBarButton
     */
    setActiveButton: function(btn) {
        if (btn) {
            btn.toggle(true);
        } else {
            if(this.getWindowBar()) {
                this.getWindowBar().items.each(function (item) {
                    if (item.isButton) {
                        item.toggle(false);
                    }
                });
            }
        }
    },
    /**
     * OnClick Element in windowbar
     *
     * @param {Ext.button.Button} btn
     */
    onWindowBtnClick: function (btn) {
        var win = btn.win;
        if (win.minimized || win.hidden) {
            win.show();
        } else if (win.active) {
            win.minimize();
        } else {
            win.toFront();
        }
    },
    /**
     * Change the URL of the CSS for a theme so that theme changes
     *
     * @param {String} src
     */
    setUITheme: function(src) {
        var me = this;
        Ext.util.CSS.swapStyleSheet('user-style-theme', me.getThemesUrl() + src);
        me.setTheme(src);  // config var!
    }
});