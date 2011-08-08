/**
 * Controller for Settings Module in Webdesktop
 *
 * Settings module should be available through ACL for every user to change
 * the user appearance and look and fell like
 *  - wallpaper
 *  - themes
 *  - shortcuts on desktop
 *  - autorun
 *  - quickstart
 *  - colors (@TODO: Need to implement)
 *
 * Main File in the backend is:
 *      FileName  : src/application/modules/webdesktop/models/modules/Settings.php
 *      ClassName : Webdesktop_Model_Modules_Settings
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Settings
 * @namespace Webdesktop.controller.settings
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.controller.settings.Settings
 * @extends Ext.ux.webdesktop.Module
 * @todo currently the main layout is 'fit' in the window, change the layout
 *       to 'card' and swap the panels as cards. Do not pre render the panels and
 *       change the panels with .::loadCard()
 */
Ext.define('Webdesktop.controller.settings.Settings', {
    extend: 'Ext.ux.webdesktop.Module',
    stores: [
        'Webdesktop.store.settings.Wallpapers',
        'Webdesktop.store.settings.Themes'
    ],
    models: [
        'Webdesktop.model.settings.Wallpaper',
        'Webdesktop.model.settings.Theme'
    ],
    views: [
        'Webdesktop.view.settings.Window'
    ],
    refs: [
        { ref: 'settingsWin',       selector: 'settings_window'},
        { ref: 'background',        selector: 'settings_background'},
        { ref: 'backgroundPreview', selector: 'settings_background panel[region=center]'},
        { ref: 'theme',             selector: 'settings_theme'},
        { ref: 'shortcuts',         selector: 'settings_shortcuts'},
        { ref: 'autorun',           selector: 'settings_autorun'},
        { ref: 'quickstart',        selector: 'settings_quickstart'},
    ],
    /**
     * Load the CSS files from the ressources/css folder
     * @see Ext.ux.webdesktop.Module::init()
     * @see app.js -> Ext.application.loadCssFile()
     */
    useCss: [
        'settings/settings.css'
    ],
    /**
     * Run the controller and show the settings window
     */
    launch: function() {
        var me = this,
            win = me.getDesktop().addWindow({
                widget : 'settings_window'
            }, {
                themesView : {
                    previewUrl: me.getDesktop().getThemePreviewUrl()
                }
            });
        me.control({
            'settings_menu button' : {
                click: me.loadCard
            },
            'settings_background dataview': {
                itemclick: me.setPreviewWallpaper
            },
            'settings_background button[actionType=save]': {
                click: me.setSaveWallpaper
            },
            'settings_theme': {
                beforeactivate: me.onThemesBeforeActivate
            },
            'settings_theme button[actionType=save]': {
                click: me.setSaveTheme
            },
            'settings_shortcuts': {
                beforeactivate: me.onShortcutsBeforeActivate
            },
            'settings_shortcuts treepanel': {
                checkchange: me.onShortcutsCheckChange
            },
            'settings_shortcuts button[actionType=save]': {
                click: me.setSaveShortcuts
            },
            'settings_autorun': {
                beforeactivate: me.onAutorunBeforeActivate
            },
            'settings_autorun button[actionType=save]': {
                click: me.setSaveAutorun
            },
            'settings_quickstart': {
                beforeactivate: me.onQuickstartBeforeActivate
            },
            'settings_quickstart treepanel': {
                checkchange: me.onQuickstartCheckChange
            },
            'settings_quickstart button[actionType=save]': {
                click: me.setSaveQuickstart
            },
        });
        win.show();
    },

    /**
     * Load the panel, based on the buttons in the menu clicked
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo see Class docs comment, change layout from fit to card and then this
     *       function is deprecated
     */
    loadCard: function(btn, event) {
        var me    = this,
            win   = me.getSettingsWin(),
            type  = btn.actionType || null,
            view  = 'settings_' + type;
        if(type) {
            win.getLayout().setActiveItem(win.down(view));
        }
    },

    /**
     * Set the selected wallpaper in the preview panel
     *
     * @param {Ext.view.View} view
     * @param {Webdesktop.model.settings.Theme} record
     */
    setPreviewWallpaper: function(view, record) {
        var me = this,
            preview = me.getBackgroundPreview(),
            src = record.get('src') !== '' ? record.get('src') : Ext.BLANK_IMAGE_URL;

        preview.items.first().setSrc(me.getDesktop().getWpUrl() + src);
    },
    /**
     * Save the selected user choice of wallpaper on the backend
     *
     * @param {Ext.view.View} view
     * @param {Webdesktop.model.settings.Theme} record
     */
    setSaveWallpaper: function(btn, event) {
        var me = this,
            records = me.getBackground().down('dataview').getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            stretch = me.getBackground().down('checkboxfield[name=stretch]').getValue() || false;

        if(record) {
            Ext.Ajax.request({
                url    : me.getApplication().apiUrl + '_module/settings/_action/changeWallpaper',
                params : {
                    id      : record.get('id'),
                    stretch : stretch === true ? 1 : 0
                },
                success: function() {
                    me.getDesktop().getWallpaper().setWallpaper(me.getDesktop().getWpUrl() + record.get('src'), stretch);
                }
            });
        }
    },

    /**
     * Fires if the themes button is activated
     * Select the current activate theme in the dataview
     *
     * @param {Webdesktop.view.settings.Theme} panel
     */
    onThemesBeforeActivate: function(panel) {
        var me      = this,
            view    = panel.down('dataview'),
            current = me.getDesktop().getTheme(),
            records = view.getStore().data.filterBy(function(item) {
                if(current.indexOf(item.get('src')) > -1) {
                    return item;
                }
                return false;
            });
        view.select(records.first());
    },

    /**
     * Fire when the use hit save to save the new selected theme
     */
    setSaveTheme: function() {
        var me      = this,
            records = me.getTheme().down('dataview').getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null;

        if(record) {
            Ext.Ajax.request({
                url: me.getApplication().apiUrl + '_module/settings/_action/changeTheme',
                params: {
                    id: record.get('id')
                },
                success: function() {
                    var src = record.get('src'),
                        desktop = me.getDesktop();
                    desktop.setUITheme(src);
                }
            });
        }
    },
    /**
     * Fires before the Shortcuts panel is rendered
     *
     * Push the data to the treepanel store, so that the user can select
     * the shortcuts that should be visible on the desktop
     *
     * @param {Webdesktop.view.settings.Shortcuts} tree
     */
    onShortcutsBeforeActivate: function(panel) {
        var me = this,
            tree = panel.down('treepanel'),
            modules = me.getDesktop().getModules(),
            shortcuts = me.getDesktop().shortcuts,
            root = {
                expanded: true,
                text: "Root",
                children: []
            };
        modules.each(function(m) {
            var r = shortcuts.findRecord('moduleId', m.moduleId);
            root.children.push({
                text: m.launcher.text,
                moduleId: m.moduleId,
                leaf: true,
                checked: r ? true : false
            });
        }, me);

        tree.setRootNode(root);
    },
    /**
     * Fire when user clicks on a item in the treepanal of shortcuts
     *
     * Show/Hide the shortcut on the desktop
     *
     * @param {Ext.data.Store.ImplicitModel} node that was checked/unchecked
     * @param {Boolean} status new checked status
     */
    onShortcutsCheckChange: function(node, status) {
        var me = this,
            modules = me.getDesktop().getModules(),
            module = modules.get(node.raw.moduleId),
            store = me.getDesktop().shortcuts,
            record = store.findRecord('moduleId', module.moduleId)
        if(status === true) {
            // add shortcut to desktop
            store.add(module)
        } else {
            // remove shortcut from desktop
            store.remove(record);
        }
    },
    /**
     * Sends a request to the backend and save the shortcuts settings
     */
    setSaveShortcuts: function() {
        var me = this,
            result = [];

        Ext.each(me.getShortcuts().down('treepanel').getView().getChecked(), function(record) {
            result.push(record.raw.moduleId);
        }, me);
        Ext.Ajax.request({
            url    : me.getApplication().apiUrl + '_module/settings/_action/changeShortcuts',
            params : {
                modules : Ext.encode(result)  // params are not automaticlly json encoded by Ext.data.Operation
            }
        });
    },

    /**
     * Fires before the Autorun panel is rendered
     *
     * Push the data to the treepanel store, so that the user can select
     * the autorun that should be visible on the desktop
     *
     * @param {Webdesktop.view.settings.Shortcuts} tree
     */
    onAutorunBeforeActivate: function(panel) {
        var me      = this,
            tree    = panel.down('treepanel'),
            modules = me.getDesktop().getModules(),
            root    = {
                expanded : true,
                text     : "Root",
                children : []
            };
        modules.each(function(m) {
            var r = Ext.Array.contains(me.getDesktop().getUserConfig().launchers.autorun, m.moduleId);
            root.children.push({
                text     : m.launcher.text,
                moduleId : m.moduleId,
                leaf     : true,
                checked  : r ? true : false
            });
        }, me);

        tree.setRootNode(root);
    },
    /**
     * Sends a request to the backend and save the autorun settings
     */
    setSaveAutorun: function() {
        var me = this,
            result = [];

        Ext.each(me.getAutorun().down('treepanel').getView().getChecked(), function(record) {
            result.push(record.raw.moduleId);
        }, me);

        Ext.Ajax.request({
            url    : me.getApplication().apiUrl + '_module/settings/_action/changeAutorun',
            params : {
                modules : Ext.encode(result)  // params are not automaticlly json encoded by Ext.data.Operation
            }
        });
    },

    /**
     * Fires before the Quickstart panel is rendered
     *
     * Push the data to the treepanel store, so that the user can select
     * the Quickstart that should be visible on the desktop
     *
     * @param {Webdesktop.view.settings.Shortcuts} tree
     */
    onQuickstartBeforeActivate: function(panel) {
        var me      = this,
            tree    = panel.down('treepanel'),
            modules = me.getDesktop().getModules(),
            root    = {
                expanded : true,
                text     : "Root",
                children : []
            };

        modules.each(function(m) {
            var r = Ext.Array.contains(me.getDesktop().getUserConfig().launchers.quickstart, m.moduleId);
            root.children.push({
                text     : m.launcher.text,
                moduleId : m.moduleId,
                leaf     : true,
                checked  : r ? true : false
            });
        }, me);

        tree.setRootNode(root);
    },
    /**
     * Fire when user clicks on a item in the treepanal of quickstart
     *
     * Show/Hide the modules in the quickstartbar
     *
     * @param {Ext.data.Store.ImplicitModel} node that was checked/unchecked
     * @param {Boolean} status new checked status
     */
    onQuickstartCheckChange: function(node, status) {
        var me      = this,
            modules = me.getDesktop().getModules(),
            module  = modules.get(node.raw.moduleId);

        if(status === true) {
            // add shortcut to desktop
            me.getDesktop().getQuickStart().add(Ext.apply({
                moduleId : module.moduleId
            }, module.launcher));
        } else {
            // remove shortcut from desktop
            Ext.each(me.getDesktop().getQuickStart().query('button'), function(item) {
                if(item.moduleId == module.moduleId) {
                    me.getDesktop().getQuickStart().remove(item);
                    return false;
                }
            }, me);
        }
    },
    /**
     * Sends a request to the backend and save the Quickstart settings
     */
    setSaveQuickstart: function() {
        var me     = this,
            result = [];

        Ext.each(me.getQuickstart().down('treepanel').getView().getChecked(), function(record) {
            result.push(record.raw.moduleId);
        }, me);

        Ext.Ajax.request({
            url    : me.getApplication().apiUrl + '_module/settings/_action/changeQuickstart',
            params : {
                modules : Ext.encode(result)  // params are not automaticlly json encoded by Ext.data.Operation
            }
        });
    }
});