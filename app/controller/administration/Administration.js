/**
 * Controller for Administration Module in Webdesktop
 *
 * Administration Module allows users to manage the application.
 * From here it is possible to
 *  - add/edit/delete/changepass users
 *  - add/edit/delete groups
 *  - add/edit/delete roles
 *  - add/remove users and groups to roles
 *  - add/edit/delete controllers and controllers actions
 *  - set permissions to controllers and controllers actions
 *
 * Main File in the backend is:
 *      FileName  : src/application/modules/webdesktop/models/modules/Administration.php
 *      ClassName : Webdesktop_Model_Modules_Administration
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.controller.administration
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.controller.administration.Administration
 * @extends Ext.ux.webdesktop.Module
 * @todo On many forms an error reporting is missing after a save/add button is
 * @todo add modules like controllers to the menu (need complete module part in controller / views / models etc)
 */
Ext.define('Webdesktop.controller.administration.Administration', {
    extend: 'Ext.ux.webdesktop.Module',
    // Models that are loaded during controller initialisation
    models: [
        'Webdesktop.model.administration.User',
        'Webdesktop.model.administration.Group',
        'Webdesktop.model.administration.Role',
        'Webdesktop.model.administration.Controller',
        'Webdesktop.model.administration.Action',
        'Webdesktop.model.administration.Permission'
    ],
    // Stores that are loaded during controller initialisation
    stores: [
        'Webdesktop.store.administration.Users',
        'Webdesktop.store.administration.Groups',
        'Webdesktop.store.administration.Roles',
        'Webdesktop.store.administration.Controllers',
        'Webdesktop.store.administration.Actions',
        'Webdesktop.store.administration.Permissions'
    ],
    // Views that are loaded during controller initialisation
    // Note: Only the mainpanel is loaded during init, the rest use post loaded
    // with the 'uses' config. This is only important as long as no minified all in
    // one deplay file is generated
    views: [
        'Webdesktop.view.administration.MainPanel'
    ],
    // Files that are loaded after controller is inited. They are not needed
    // to startup the controller
    uses: [
        'Webdesktop.view.administration.users.List',
        'Webdesktop.view.administration.users.Info',
        'Webdesktop.view.administration.users.BaseForm',
        'Webdesktop.view.administration.users.Add',
        'Webdesktop.view.administration.users.Edit',
        'Webdesktop.view.administration.users.Delete',
        'Webdesktop.view.administration.users.Password',
        'Webdesktop.view.administration.groups.List',
        'Webdesktop.view.administration.groups.Delete',
        'Webdesktop.view.administration.roles.List',
        'Webdesktop.view.administration.roles.BaseForm',
        'Webdesktop.view.administration.roles.Add',
        'Webdesktop.view.administration.roles.Edit',
        'Webdesktop.view.administration.roles.Delete',
        'Webdesktop.view.administration.controllers.List',
        'Webdesktop.view.administration.controllers.Edit',
        'Webdesktop.view.administration.controllers.Add',
        'Webdesktop.view.administration.controllers.Delete',
        'Webdesktop.view.administration.controllers.Permission',
        'Webdesktop.view.administration.actions.List',
        'Webdesktop.view.administration.actions.Edit',
        'Webdesktop.view.administration.actions.Add',
        'Webdesktop.view.administration.actions.Delete',
        'Webdesktop.view.administration.actions.Permission',
        'Ext.ux.grid.column.CheckColumn',
        'Ext.ux.grid.plugin.RowEditing'
    ],
    /**
     * Load the CSS files from the ressources/css folder
     * @see Ext.ux.webdesktop.Module::init()
     * @see app.js -> Ext.application.loadCssFile()
     */
    useCss: [
        'administration/administration.css'
    ],
    // refs for shorthand getting views or view elements
    refs: [{
        ref     : 'userList',
        selector: '.administration_userlist'
    }, {
        ref     : 'groupList',
        selector: '.administration_grouplist'
    }, {
        ref     : 'roleList',
        selector: '.administration_rolelist'
    }, {
        ref     : 'controllerList',
        selector: '.administration_controllerlist'
    }, {
        ref     : 'actionList',
        selector: '.administration_actionlist'
    }, {
        ref     : 'tabPanel',
        selector: 'tabpanel'
    }],
    /**
     * Init function for the controller
     *
     * Do not autorun code here, just setup the controller
     *
     * @todo remove, because no node is loaded here
     * @param {Ext.app.Application} application
     * @param {Ext.app.Controller} desktopController
     */
    init: function(application, desktopController) {
        // place some code here
        this.callParent(arguments);   // need to call because inheritance of app.Controller is needed!
    },
    /**
     * Launch the controller
     *
     * Init the main window that for this controller is used and setup
     * the event listening on all components/views and their actions
     *
     * @todo many views have a cancel button, implement the event listener and write the function
     */
    launch: function() {
        var me = this,
            win = me.getDesktop().addWindow({
                widget : 'administration_mainpanel',
                id     : 'admin_id'
            });
        me.control({
            /**
             * MAIN PANEL Actions
             * Add Actions to the buttons in the MAINMENU
             */
            // User SPLIT Button
            '.administration_menu splitbutton[iconCls=icon-users]': {
                click: me.initUsersList
            },
            '.administration_menu splitbutton[iconCls=icon-users] menuitem[action=list]': {
                click: me.initUsersList
            },
            '.administration_menu splitbutton[iconCls=icon-users] menuitem[action=add]': {
                click: me.initUsersAdd
            },
            // Group Button
            '.administration_menu button[iconCls=icon-groups]': {
                click: me.initGroupsList
            },
            // Role SPLIT Button
            '.administration_menu button[iconCls=icon-roles]': {
                click: me.initRolesList
            },
            // Controller Button
            '.administration_menu button[iconCls=icon-controllers]': {
                click: me.initControllers
            },

            ////////////////// PANEL ACTIONS ///////////////

            /** USER: GRID LIST **/
            '.administration_userlist toolbar[dock=right] button[iconCls=ux-icon-user-add]': {
                click: me.initUsersAdd
            },
            '.administration_userlist > tableview': {
                itemdblclick: me.initUserInfo
            },
            '.administration_userlist toolbar[dock=right] button[iconCls=ux-icon-user-edit]': {
                click: me.initUserEdit
            },
            '.administration_userlist toolbar[dock=right] button[iconCls=ux-icon-user-delete]': {
                click: me.initUserDelete
            },
            '.administration_userlist toolbar[dock=right] button[iconCls=ux-icon-password]': {
                click: me.initUserChangePassword
            },
            '.administration_userlist toolbar[dock=right] button[iconCls=ux-icon-reload]': {
                click: me.doReloadUserListStore
            },

            /** USER: GRID ADD **/
            '.administration_useradd toolbar[dock=top] button[iconCls=ux-icon-add]': {
                click: me.onSaveUserAdd
            },

            /** USER: GRID EDIT **/
            '.administration_useredit toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveUserEdit
            },
            /** USER: GRID Password Change **/
            '.administration_userpass toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveUserPass
            },

            /** USER: GRID DELETE **/
            '.administration_userdelete toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveUserDelete
            },

            /** USER: GRID INFO 
            '.administration_userinfo': {
                // todo
            },
            **/
            // END USER

            /** GROUP: GRID LIST **/
            '.administration_grouplist toolbar[dock=right] button[iconCls=ux-icon-group-add]': {
                click: me.initGroupAdd
            },
            '.administration_grouplist toolbar[dock=right] button[iconCls=ux-icon-group-edit]': {
                click: me.initGroupEdit
            },
            '.administration_grouplist toolbar[dock=right] button[iconCls=ux-icon-group-delete]': {
                click: me.initGroupDelete
            },
            '.administration_grouplist toolbar[dock=right] button[iconCls=ux-icon-reload]': {
                click: me.doReloadGroupListStore
            },

            /** GROUP: GRID LIST >> ROWEDITOR PLUGIN **/
            '.administration_grouplist': {
                edit: me.onSaveGroupAction,
                canceledit: me.onCancelGroupAction
            },

            /** Group: DELETE **/
            '.administration_groupdelete toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveGroupDelete
            },

            // END GROUPS


            /** ROLES: GRID LIST **/
            '.administration_rolelist toolbar[dock=right] button[iconCls=ux-icon-group-add]': {
                click: me.initRoleAdd
            },

            '.administration_rolelist toolbar[dock=right] button[iconCls=ux-icon-group-edit]': {
                click: me.initRoleEdit
            },
            '.administration_rolelist toolbar[dock=right] button[iconCls=ux-icon-group-delete]': {
                click: me.initRoleDelete
            },
            '.administration_rolelist toolbar[dock=right] button[iconCls=ux-icon-reload]': {
                click: me.doReloadRoleListStore
            },
            /** ROLES: Add **/
            '.administration_roleadd toolbar[dock=top] button[iconCls=ux-icon-add]': {
                click: me.onSaveRoleAdd
            },

            /** ROLES: Edit **/
            '.administration_roleedit toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveRoleEdit
            },
            /** ROLES: DELETE **/
            '.administration_roledelete toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveRoleDelete
            },

            // END ROLES

            /** CONTROLLER LIST **/
            '.administration_controllerlist toolbar[dock=top] button[iconCls=ux-icon-reload]': {
                click: me.doReloadControllerListStore
            },
            '.administration_controllerlist toolbar[dock=top] button[actionType=showNewDeletedController]': {
                toggle: me.doFilterStoreControllerStatus
            },
            '.administration_controllerlist toolbar[dock=top] button[actionType=editController]': {
                click: me.initControllerEdit
            },
            '.administration_controllerlist toolbar[dock=top] button[actionType=addController]': {
                click: me.initControllerAdd
            },
            '.administration_controllerlist toolbar[dock=top] button[actionType=deleteController]': {
                click: me.initControllerDelete
            },
            '.administration_controllerlist': {             //FIXME: Find a better query
                itemclick: me.onClickGridController
            },
            '.administration_controllerlist toolbar[dock=top] button[actionType=permissionsController]': {
                click: me.initControllerPermission
            },


            /** Controller: GRID EDIT **/
            '.administration_controlleredit toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveControllerEdit
            },
            /** Controller: GRID Add **/
            '.administration_controlleradd toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveControllerAdd
            },
            /** Controller: Delete **/
            '.administration_controllerdelete toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveControllerDelete
            },
            /** Controller: Change Controller Status **/
            '.administration_controllerlist toolbar[dock=top] button[actionType=statusController]': {
                click: me.onClickChangeController
            },

            /** Controller: List all Actions for the selected Controller **/
            '.administration_controllerlist toolbar[dock=top] button[actionType=listActions]': {
                click: me.initActionList
            },

            /** CONTROLLER PERMISSIONS **/
            '.administration_controllerpermission toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveControllerPermissions
            },

            // End Grid

            /** Actions **/
            '.administration_actionlist': {             //FIXME: Find a better query
                itemclick: me.onClickGridAction
            },
            /** Action: Change Action Status **/
            '.administration_actionlist toolbar[dock=top] button[actionType=statusAction]': {
                click: me.onClickChangeAction
            },
            /** Action: init action edit **/
            '.administration_actionlist toolbar[dock=top] button[actionType=editAction]': {
                click: me.initActionEdit
            },
            /** Action: init action add **/
            '.administration_actionlist toolbar[dock=top] button[actionType=addAction]': {
                click: me.initActionAdd
            },
            /** Action: init action delete**/
            '.administration_actionlist toolbar[dock=top] button[actionType=deleteAction]': {
                click: me.initActionDelete
            },
            /** Action: init action permission **/
            '.administration_actionlist toolbar[dock=top] button[actionType=permissionsAction]': {
                click: me.initActionPermissions
            },
            /** Select a row in the action list **/
            '.administration_actionlist toolbar[dock=top] button[iconCls=ux-icon-reload]': {
                click: me.doReloadActionListStore
            },
            /** Action: toggle current - new/delete actions **/
            '.administration_actionlist toolbar[dock=top] button[actionType=showNewDeletedAction]': {
                toggle: me.doFilterStoreActionStatus
            },
            /** Action: Save Edit Action */
            '.administration_actionedit toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveActionEdit
            },
            /** Action: Save add Action */
            '.administration_actionadd toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveActionAdd
            },
            /** Action: Save Edit Permissions */
            '.administration_actionpermission toolbar[dock=top] button[iconCls=ux-icon-accept]': {
                click: me.onSaveActionPermission
            }
        });
        win.show();
    },
    
    
    /**
     * HELPER FUNCTIONS
     */
    
    /**
     * Load or Add a panel to the mainpanel
     * 
     * @param alias String Alias of the panel to load/add
     * @param options object Options to pass to Ext.create while adding a panel
     * @param query string Optional query string if a special panel (mostly itemId) should be identified
     * @return Ext.Component the created Component
     */
    loadAndInitTabWidget: function(alias, options, query) {
        var me     = this,
            q      = query ? query : '.' + alias,
            widget = me.getTabPanel().child(q);
        if(!widget) {
            widget = me.getTabPanel().add(Ext.create('widget.' + alias, options));
        }
        me.getTabPanel().setActiveTab(widget);
        return widget;
    },
    /**
     * Reload the user list grid store
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    doReloadUserListStore: function(btn, event) {
        if(this.getUserList()) {
            this.getUserList().getStore().load();
        }
    },
    /**
     * Reload the group list grid store
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    doReloadGroupListStore: function(btn, event) {
        if(this.getGroupList()) {
            this.getGroupList().getStore().load();
        }
    },
    /**
     * Reload the role list grid store
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    doReloadRoleListStore: function(btn, event) {
        if(this.getRoleList()) {
            this.getRoleList().getStore().load();
        }
    },
    /**
     * Reload the controller list grid store
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo rewrite the hiding of the buttons
     */
    doReloadControllerListStore: function(btn, event) {
        var me = this,
            g = me.getControllerList();
        if(g) {
            g.getSelectionModel().deselectAll();
            g.getStore().load();
            // buttons in toolbar need to be hidden, because row focus is gone after relaod
            /*
            Ext.each(['addController', 'editController', 'deleteController', 'statusController', 'permissionsController', 'listActions'], function(el) {
                //g.down('toolbar button[actionType='+el+']').setVisible(false);
            }, me);
            */
        }
    },
    /**
     * Reload the controller list grid store
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    doReloadActionListStore: function(btn, event) {
        var g  = btn.up('gridpanel');
        g.getSelectionModel().deselectAll();
        g.getStore().load({
            params : {
                cid : g.cId
            }
        });
    },

    /**
     * Filter the controller list store
     *
     * @param {Ext.button.Button} btn
     * @param event Status of the toggle button || event object (based on where method was called)
     */
    doFilterStoreControllerStatus: function(btn, event) {
        this.getControllerList().getStore().clearFilter();
        this.getControllerList().getStore().filter(
            new Ext.util.Filter({
                filterFn: function(item) {
                    return btn.pressed === false ? item.data.status == 0 : item.data.status != 0;
                }
            })
        );
    },
    /**
     * Filter the controller list store
     *
     * @param {Ext.button.Button} btn
     * @param event Status of the toggle button || event object (based on where method was called)
     */
    doFilterStoreActionStatus: function(btn, event) {
        // get the gridpanel with ComponentQuery, because there can be multiple
        // action listings
        var store = btn.up('gridpanel').getStore();

        store.clearFilter();
        store.filter(
            new Ext.util.Filter({
                filterFn: function(item) {
                    return btn.pressed === false ? item.data.status == 0 : item.data.status != 0;
                }
            })
        );
    },


    /**
     * EVENT Handlers
     */

    /////// EVENTS ON MAINPANEL SPLITBUTTONS

    /**
     * Init the user list grid
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initUsersList: function(btn, event) {
        this.loadAndInitTabWidget('administration_userlist');
    },
    /**
     * Init the user add form panel
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initUsersAdd: function(btn, event) {
        // we want to enable multiple add panels, generate uniqe IDs
        var name   = 'administration_useradd',
            itemId = name + '-' + Ext.id();
        this.loadAndInitTabWidget(
            name,
            {
                itemId: itemId
            },
            'administration_useradd[itemId='+itemId+']'
        );
    },
    /**
     * Init the group list grid
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initGroupsList: function(btn, event) {
        this.loadAndInitTabWidget('administration_grouplist');
    },
    /**
     * Init the user roles list grid
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initRolesList: function(btn, event) {
        this.loadAndInitTabWidget('administration_rolelist');
    },
    /**
     * Init the controller list grid
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initControllers: function(btn, event) {
        this.loadAndInitTabWidget('administration_controllerlist');
    },


    /**
     * USERS
     */

    /**
     * Init the user edit form panel
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initUserEdit: function(btn, event) {
        var records = this.getUserList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_useredit',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Edit: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },
    /**
     * Init the user delete form panel
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initUserDelete: function(btn, event) {
        var records = this.getUserList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_userdelete',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Delete: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },
    /**
     * Init the user info form panel
     * 
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo implement: Add icon to grid, write backend scripts et cetera
     */
    initUserInfo: function(btn, event) {
        var records = this.getUserList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_userinfo',
            itemId, query, view;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            view   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Info: ' + record.get('name')
                },
                query
            );
            //view.bind() @todo: bind the record to the store to load the info
        }
    },
    /**
     * Init the user change pw form panel
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initUserChangePassword: function(btn, event) {
        var records = this.getUserList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_userpass',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form = this.loadAndInitTabWidget(name,
                {
                    itemId : itemId,
                    title  : 'PassChange: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },

    
    /**
     * GROUPS
     */

    /**
     * Init the group add
     *
     * Group sections uses the RowEditor Grid Plugin
     * If the user clicks on the add button this method inits
     * the RowEditor
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initGroupAdd: function(btn, event) {
        var me     = this,
            redit  = me.getGroupList().getPlugin('RowEditorGroupList'),
            record = Ext.ModelManager.create({
                name        : 'New Groupname',
                description : 'A short description'
            },
            'Webdesktop.model.administration.Group');

        redit.cancelEdit();
        me.getGroupList().getStore().insert(0, record);
        redit.startEdit(0, 0);
    },

    /**
     * Init the group edit rowplugin for the selected row
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initGroupEdit: function(btn, event) {
        var records   = this.getGroupList().getSelectionModel().getSelection(),
            record    = records.length === 1 ? records[0] : null,
            rowEditor = this.getGroupList().getPlugin('RowEditorGroupList');
        if(record) {
            rowEditor.startEdit(record, 0);
        }
    },

    /**
     * Init the Group Delete Form Panel
     *
     * Group Delete uses a normal formPanel, so that the user can
     * verity the delete of the uses
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo: enhancement: In the FormPanel add more informations, which or how many users
     *                     are bound to the selected group
     */
    initGroupDelete: function(btn, event) {
        var records = this.getGroupList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_groupdelete',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Delete: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },

    /**
     * ROLES
     */

    /**
     * Init the role add
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initRoleAdd: function(btn, event) {
        // we want to enable multiple add panels, generate unique Id
        var name   = 'administration_roleadd',
            itemId = name + '-' + Ext.id();
        this.loadAndInitTabWidget(
            name,
            {
                itemId : itemId
            },
            'administration_roleadd[itemId='+itemId+']'
        );
    },

    /**
     * Init the role edit
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initRoleEdit: function(btn, event) {
        var records = this.getRoleList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_roleedit',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Edit: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },

    /**
     * Init the Role Delete Form Panel
     *
     * Role Delete uses a normal formPanel, so that the user can
     * verity
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo: enhancement: In the FormPanel add more informations, which or how many users/groups
     *                     are bound to the selected role
     */
    initRoleDelete: function(btn, event) {
        var records = this.getRoleList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_roledelete',
            itemId, query, form;
        if(record) {
            itemId = name + '-' + record.get('id');
            query  = Ext.String.format('.{0}[itemId={1}]', name, itemId);
            form   = this.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    title  : 'Delete: ' + record.get('name')
                },
                query
            );
            form.getForm().loadRecord(record);
        }
    },

    /** CONTROLLERS **/

    /**
     * Load the Controller View
     *
     * This is a shared function for every init view for controller actions.
     * Add/Edit/Delete/Permission are loaded through this piece of code.
     * The Event Listeners on the button point to individual functions, that are
     * very short. Here we receive the call from the short event handler functions
     * and do the actual loading of the view.
     *
     * @param {String} type
     * @private
     */
    _initController: function(type) {
        var me = this,
            sf = Ext.String.format,
            records = me.getControllerList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            alias, itemId, rId, title, query, form;
        if(record) {
            alias  = sf('administration_controller{0}', type);
            title  = sf('{0}/{1}', record.get('moduleName'), record.get('controllerName'));
            rId    = record.get('id') ? record.get('id') : title;
            itemId = sf('{0}-{1}', alias, rId);
            query  = sf('.{0}[itemId={1}]', alias, itemId);
            if(type !== 'permission') {
                // every view, except permission, is a form
                form = me.loadAndInitTabWidget(alias, {
                    itemId : itemId,
                    title  : sf('{0} {1}', Ext.String.capitalize(type), title)
                }, query);
                form.getForm().loadRecord(record);
            } else {
                // permission view is a grid, so differnt handling
                me.loadAndInitTabWidget(alias, {
                    itemId : itemId,
                    record : record,
                    title  : sf('{0} {1}', Ext.String.capitalize(type), title)
                }, query);
            }
        }
    },

    /**
     * Init the Controller Edit view
     *
     * The actual loading of this view is done in this::_initController
     *
     * @see this::_initController
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initControllerEdit: function(btn, event) {
        this._initController('edit');
    },
    /**
     * Init the Controller Add view
     *
     * The actual loading of this view is done in this::_initController
     *
     * @see this::_initController
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initControllerAdd: function(btn, event) {
        this._initController('add');
    },
    /**
     * Init the Controller Delete view
     *
     * The actual loading of this view is done in this::_initController
     *
     * @see this::_initController
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initControllerDelete: function(btn, event) {
        this._initController('delete');
    },
    /**
     * Load the permissions grid for the selected  controller
     *
     * The actual loading of this view is done in this::_initController
     *
     * @see this::_initController
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initControllerPermission: function(btn, event) {
        this._initController('permission');
    },


    ///////////////////////////////////////////////////
    // Actions in the sections
    ///////////////////////////////////////////////////

    initActionList: function() {
        var me      = this,
            sf      = Ext.String.format,
            records = me.getControllerList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            name    = 'administration_actionlist',
            cId, itemId, query, title, grid;
        if(record) {
            cId    = record.get('id');
            itemId = sf('{0}-{1}', name, cId);
            query  = sf('.{0}[itemId={1}]', name, itemId);
            title  = sf('List Actions: {0}/{1}', record.get('moduleName'), record.get('controllerName'));
            grid   = me.loadAndInitTabWidget(
                name,
                {
                    itemId : itemId,
                    cId    : cId, // Note: custom variable into grid
                    title  : title
                },
                query
            );

            grid.getStore().load({
                params : {
                    cid : cId
                }
            });
        }
    },

    /**
     * Load the Action View
     *
     * This is a shared function for every init view for Action actions.
     * Add/Edit/Delete/Permission are loaded through this piece of code.
     * The Event Listeners on the button point to individual functions, that are
     * very short. Here we receive the call from the short event handler functions
     * and do the actual loading of the view.
     *
     * @param {String} type
     * @private
     */
    _initAction: function(type) {
        var me = this,
            sf = Ext.String.format,
            records = me.getActionList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null,
            alias, itemId, rId, title, query, form;
        if(record) {
            alias  = sf('administration_action{0}', type);
            rId    = record.get('id') ? record.get('id') : record.get('actionName');
            title  = sf('{0} {1}', Ext.String.capitalize(type), record.get('actionName'));
            itemId = sf('{0}-{1}', alias, rId);
            query  = sf('.{0}[itemId={1}]', alias, itemId);
            if(type !== 'permission') {
                form = me.loadAndInitTabWidget(alias, {
                    itemId : itemId,
                    title  : title
                }, query);
                form.getForm().loadRecord(record);
            } else {
                me.loadAndInitTabWidget(alias, {
                    itemId : itemId,
                    record : record,
                    title  : title
                }, query);
            }
        }
    },
    /**
     * Init the Action Edit view
     *
     * The actual loading of this view is done in this::_initAction
     *
     * @see this::_initAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initActionEdit: function() {
        this._initAction('edit');
    },
    /**
     * Init the Action delete view
     *
     * The actual loading of this view is done in this::_initAction
     *
     * @see this::_initAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initActionDelete: function(btn, event) {
        this._initAction('delete');
    },
    /**
     * Init the Action add view
     *
     * The actual loading of this view is done in this::_initAction
     *
     * @see this::_initAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initActionAdd: function(btn, event) {
        this._initAction('add');
    },
    /**
     * Init the Action permissions view
     *
     * The actual loading of this view is done in this::_initAction
     *
     * @see this::_initAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    initActionPermissions: function(btn, event) {
        this._initAction('permission');
    },


    /**
     * USERS
     */

    /**
     * Fires when the user clicks ADD to add a user
     * 
     * The configuration for the submit is generated in the method
     * this::_genFormSubmitAction(), because its redundant code
     *
     * @see this::_genFormSubmitAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveUserAdd: function(btn, event) {
        var me        = this,
            form      = btn.up('form').getForm();
        if(form.isValid()) {
            form.submit(me._genFormSubmitAction('saveNewUser', function() {
                me.doReloadUserListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_userlist'));
            }));
        }
    },
    /**
     * Fires when the user clicks on SAVE to save the modification if a user
     *
     * The configuration for the submit is generated in the method
     * this::_genFormSubmitAction(), because its redundant code
     *
     * @see this::_genFormSubmitAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveUserEdit: function(btn, event) {
        var me     = this,
            form   = btn.up('form').getForm(),
            errors = form.updateRecord(form.getRecord()).getRecord().validate();
        if(errors.isValid()) {
            form.submit(me._genFormSubmitAction('saveEditUser', function() {
                me.doReloadUserListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_userlist'));
            }));
        } else {
            form.markInvalid(errors);
        }
    },
    /**
     * Fires when the user clicks SAVE to delete a user
     *
     * The configuration for the submit is generated in the method
     * this::_genFormSubmitAction(), because its redundant code
     *
     * @see this::_genFormSubmitAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo refactor
     */
    onSaveUserDelete: function(btn, event) {
        var me = this,
            form = btn.up('form').getForm();
        if(form.getRecord().get('id')) {
            Ext.Msg.show({
                 title   :'Delete User?',
                 msg     : 'Do you really want to delete the selected user?',
                 buttons : Ext.Msg.YESNO,
                 icon    : Ext.Msg.QUESTION,
                 fn      : function(answer) {
                     if(answer === 'yes') {
                         form.submit(me._genFormSubmitAction('saveDeleteUser', function() {
                            me.doReloadUserListStore();
                            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_userlist'));
                        }));
                     }
                 }
            });
        } else {
            Ext.Msg.show({
                title   :'Error',
                msg     : 'Something went wrong. Cannot delete User. Please reload and try again.',
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },
    /**
     * Fires when the user clicks on SAVE to save the modification if a user
     *
     * The configuration for the submit is generated in the method
     * this::_genFormSubmitAction(), because its redundant code
     *
     * @see this::_genFormSubmitAction
     * @param {Ext.button.Button} btn
     * @param {Object} event
     * @todo mark form errors if form is not valid
     */
    onSaveUserPass: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.isValid()) {
            form.submit(me._genFormSubmitAction('saveEditUserPw', function() {
                me.doReloadUserListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_userlist'));
            }));
        } else {
            // FIXME: error handling
            return false;
        }
    },


    /**
     * GROUPS
     *
     * GROUPSECTION uses the Ext.ux.grid.plugin.RowEditor and not for add/edit/delete
     * an own panel. add/edit are handled with RowEditor only delete has an own
     * FormPanel.
     */

    /**
     * Fires when the users clicks "update" in the RowEditor to add/edit a group
     *
     * @param {Object} event
     * @todo return the changed records from the backend, the see the fixme comment inline
     */
    onSaveGroupAction: function(event) {
        var store  = event.store,
            record = event.record,
            errors = record.phantom === true ? record.validate('add') : record.validate();

        if(errors.isValid()) {
            record.endEdit();
            store.sync();
            //FIXME: return the records from the backend so that the store can
            //       update the store automaticly. After that remove the reload store
            this.doReloadGroupListStore();
        }  else {
            Ext.Msg.show({
                title   : 'Error',
                msg     : errors.getRange().join("\n"),
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },

    /**
     * Fires when the user clicks "cancel" in the RowEditor to stop add/edit.
     * The cancel event is confirmed whe the records is phantom
     *
     * @param {Object} event
     */
    onCancelGroupAction: function(event) {
        var store = event.store,
            record = event.record;
        if(record.phantom === true) {
            store.remove(record);
        }
    },

    /**
     * Delete the group after the user hits delete
     *
     * The delete action is donw with a form so that the user
     * can check if he is deleting the correct group.
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveGroupDelete: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.getRecord().get('id')) {
            Ext.Msg.show({
                title   : 'Delete Group?',
                msg     : 'Do you really want to delete the selected Group?',
                buttons : Ext.Msg.YESNO,
                icon    : Ext.Msg.QUESTION,
                fn      : function(answer) {
                    if(answer === 'yes') {
                        form.submit(me._genFormSubmitAction('saveDeleteGroup', function() {
                            me.doReloadGroupListStore();
                            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_grouplist'));
                        }));
                    }
                }
            });
        } else {
            Ext.Msg.show({
                title   : 'Error',
                msg     : 'Something went wrong. Cannot delete group. Please reload and try again.',
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },


    /**
     * ROLES
     */

    /**
     * Fires when the users clicks "save" in the administration_roleadd form
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveRoleAdd: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.isValid()) {
            form.submit(me._genFormSubmitAction('saveNewRole', function() {
                me.doReloadRoleListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_rolelist'));
            }));
        }
    },
    /**
     * Save the changes on an edited role
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveRoleEdit: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        form.submit(me._genFormSubmitAction('saveEditRole', function() {
            me.doReloadRoleListStore();
            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_rolelist'));
        }));
    },

    /**
     * Delete the role after the user hits delete
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveRoleDelete: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.getRecord().get('id')) {
            Ext.Msg.show({
                title   : 'Delete Role?',
                msg     : 'Do you really want to delete the selected Role?',
                buttons : Ext.Msg.YESNO,
                icon    : Ext.Msg.QUESTION,
                fn      : function(answer) {
                    if(answer === 'yes') {
                        form.submit(me._genFormSubmitAction('saveDeleteRole', function() {
                            me.doReloadRoleListStore();
                            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_rolelist'));
                        }));
                    }
                }
            });
        } else {
            Ext.Msg.show({
                title   : 'Error',
                msg     : 'Something went wrong. Cannot delete group. Please reload and try again.',
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },


    /**
     * CONTROLLERS
     */

    /**
     * Fires when the user selects a row in the ControllerGrid
     *
     * To ensure that the user does not do anything with controllers, that
     * are not in the correct state, show/hide the buttons in the top toolbar.
     *
     * @param {Ext.grid.View} view
     * @param {Webdesktop.model.administration.Controller} records
     */
    onClickGridController: function(view, record) {
        var me   = this,
            /**
             * Show/hide Buttons
             * Helper function to recude code
             *
             * @param {Boolean} addVisible Add Button visible
             * @param {Boolean} editVisible Edit Button visible
             * @param {Boolean} delVisible Delete Button visible
             */
            fnSwap = function(addVisible, editVisible, delVisible) {
                me.getControllerList().down('toolbar button[actionType=addController]').setVisible(addVisible);
                me.getControllerList().down('toolbar button[actionType=editController]').setVisible(editVisible);
                me.getControllerList().down('toolbar button[actionType=deleteController]').setVisible(delVisible);
                Ext.each(['statusController', 'permissionsController', 'listActions'], function(el) {
                    me.getControllerList().down('toolbar button[actionType='+el+']').setVisible(true);
                }, me);
            };
        // change the button visibility on the record.status field
        // 1 == New
        // 2 == Vanished
        // 3 == ok
        if(record.get('status') == 1) {
            fnSwap(true, false, false);
        } else if(record.get('status') == 2) {
            fnSwap(false, false, true);
        } else if(record.get('status') == 0) {
            fnSwap(false, true, false);
        } else {
            fnSwap(false, false, false);
        }
    },

    /**
     * Fires when the user edit save button is hit
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveControllerEdit: function(btn, event) {
        var me     = this,
            form   = btn.up('form').getForm(),
            errors = form.updateRecord(form.getRecord()).getRecord().validate();
        if(errors.isValid()) {
            form.submit(me._genFormSubmitAction('saveEditController', function() {
                me.doReloadControllerListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_controllerlist'));
            }));
        } else {
            form.markInvalid(errors);
        }
    },

    /**
     * Fires when the user add save button is hit
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveControllerAdd: function(btn, event) {
        var me     = this,
            form   = btn.up('form').getForm(),
            errors = form.updateRecord(form.getRecord()).getRecord().validate('add');
        if(errors.isValid()) {
            form.submit(me._genFormSubmitAction('saveAddController', function() {
                me.doReloadControllerListStore();
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_controllerlist'));
            }));
        } else {
            form.markInvalid(errors);
        }
    },
    /**
     * Fires when the user add save button is hit
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveControllerDelete: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.getRecord().get('id')) {
            Ext.Msg.show({
                 title   :'Delete Controller?',
                 msg     : 'Do you really want to delete the selected controller?',
                 buttons : Ext.Msg.YESNO,
                 icon    : Ext.Msg.QUESTION,
                 fn      : function(answer) {
                     if(answer === 'yes') {
                         form.submit(me._genFormSubmitAction('saveDeleteController', function() {
                            me.doReloadControllerListStore();
                            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_controllerlist'));
                        }));
                     }
                 }
            });
        } else {
            Ext.Msg.show({
                title   :'Error',
                msg     : 'Something went wrong. Cannot delete controller. Please reload and try again.',
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },

    /**
     * Click on the Button change the enabled status
     *
     * @todo: change the grid cell (dirty) and sync grid, remove grid reload
     */
    onClickChangeController: function() {
        var me      = this,
            records = me.getControllerList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null;
        if(record) {
            Ext.Ajax.request({
                url    : me.getApplication().apiUrl + '_module/administration/_action/changeControllerStatus',
                params : {
                    id : record.get('id')
                },
                success: function(response){
                    me.doReloadControllerListStore();
                }
            });
        }
    },

    /**
     * Save the changed Permissions for an controller
     * (change permission for all actions of that controller)
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveControllerPermissions: function(btn, event) {
        btn.up('gridpanel').getStore().sync();
    },


    /**
     * ACTIONS
     */
    /**
     * Fires when the user selects a row in a ActionGrid
     *
     * To ensure that the user does not do anything with actions, that
     * are not in the correct state, show/hide the buttons in the top toolbar.
     *
     * @param {Ext.grid.View} view
     * @param {Webdesktop.model.administration.Action} records
     */
    onClickGridAction: function(view, record) {
        var me    = this,
            /**
             * Show/hide Buttons
             * Helper function to recude code
             *
             * @param {Boolean} addVisible Add Button visible
             * @param {Boolean} editVisible Edit Button visible
             * @param {Boolean} delVisible Delete Button visible
             */
            fnSwap = function(addVisible, editVisible, delVisible) {
                view.ownerCt.down('toolbar button[actionType=addAction]').setVisible(addVisible);
                view.ownerCt.down('toolbar button[actionType=editAction]').setVisible(editVisible);
                view.ownerCt.down('toolbar button[actionType=deleteAction]').setVisible(delVisible);

                Ext.each(['statusAction', 'permissionsAction'], function(el) {
                    view.ownerCt.down('toolbar button[actionType='+el+']').setVisible(true);
                }, me);
            };
        // change the button visibility on the record.status field
        // 1 == New
        // 2 == Vanished
        // 3 == ok
        if(record.get('status') == 1) {
            fnSwap(true, false, false);
        } else if(record.get('status') == 2) {
            fnSwap(false, false, true);
        } else if(record.get('status') == 0) {
            fnSwap(false, true, false);
        } else {
            fnSwap(false, false, false);
        }
    },

    /**
     * Click on the Button change the enabled status
     *
     * @todo: change the grid cell (dirty) and sync grid, remove grid reload
     */
    onClickChangeAction: function() {
        var me      = this,
            records = me.getActionList().getSelectionModel().getSelection(),
            record  = records.length === 1 ? records[0] : null;
        if(record) {
            Ext.Ajax.request({
                url    : me.getApplication() + '_module/administration/_action/changeActionStatus',
                params : {
                    id : record.get('id')
                },
                success: function(response){
                    me.doReloadActionListStore(me.getActionList().down('toolbar[dock=top] button[iconCls=ux-icon-reload]'));
                }
            });
        }
    },
    /**
     * Save the edited action on the backend
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveActionEdit: function(btn, event) {
        var me     = this,
            form   = btn.up('form').getForm(),
            errors = form.updateRecord(form.getRecord()).getRecord().validate();
        if(errors.isValid()) {
            form.submit(me._genFormSubmitAction('saveEditAction', function() {
                me.doReloadActionListStore(me.getActionList().down('toolbar[dock=top] button[iconCls=ux-icon-reload]'));
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_actionlist'));
            }));
        } else {
            form.markInvalid(errors);
        }
    },

    /**
     * Save the added action on the backend
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveActionAdd: function(btn, event) {
        var me = this,
            form = btn.up('form').getForm(),
            errors = form.updateRecord(form.getRecord()).getRecord().validate('add');
        if(errors.isValid()) {
            form.submit(me._genFormSubmitAction('saveAddAction', function() {
                me.doReloadActionListStore(me.getActionList().down('toolbar[dock=top] button[iconCls=ux-icon-reload]'));
                me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_actionlist'));
            }));
        } else {
            form.markInvalid(errors);
        }
    },

    /**
     * Save the deleted action on the backend
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveActionDelete: function(btn, event) {
        var me   = this,
            form = btn.up('form').getForm();
        if(form.getRecord().get('id')) {
            Ext.Msg.show({
                 title   :'Delete Action?',
                 msg     : 'Do you really want to delete the selected action?',
                 buttons : Ext.Msg.YESNO,
                 icon    : Ext.Msg.QUESTION,
                 fn      : function(answer) {
                     if(answer === 'yes') {
                         form.submit(me._genFormSubmitAction('saveDeleteAction', function() {
                            me.doReloadControllerListStore();
                            // bring back the user list grid to front
                            me.getTabPanel().setActiveTab(me.getTabPanel().down('.administration_actionlist'));
                        }));
                     }
                 }
            });
        } else {
            Ext.Msg.show({
                title   :'Error',
                msg     : 'Something went wrong. Cannot delete action. Please reload and try again.',
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
    },
    /**
     * Save the permissions of an action on the backend
     *
     * Uses the Ext.data.Store::sync() method
     *
     * @param {Ext.button.Button} btn
     * @param {Object} event
     */
    onSaveActionPermission: function(btn, event) {
        btn.up('gridpanel').getStore().sync();
    },

    /**
     * Generate the config for the form submit
     *
     * The FormPanel actions like user add/edit/delete, group delete
     * use a form.submit action. To reduce the LOC we use this helper
     * function
     *
     * @param {String} action The action name that is called in the backend
     * @param {Function} successCallback if the action was successfull the form will be destroyes and a custom callback is performed
     * @return object
     */
    _genFormSubmitAction: function(action, successCallback) {
        var me = this;
        return {
            clientValidation : true,
            url              : me.getApplication().apiUrl,
            params           : {
                _module: 'administration',
                _action: action
            },
            success          : function(form, action) {
                if(action.result.success == true) {
                    Ext.callback(successCallback, me);
                    form.owner.destroy();
                } else {
                    Ext.Msg.alert(action.result.error, action.result.errormessages.join("\n"));
                }
            },
            failure          : function(form, action) {
                switch (action.failureType) {
                    case Ext.form.action.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                        break;
                    case Ext.form.action.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                        break;
                    case Ext.form.action.Action.SERVER_INVALID:
                       Ext.Msg.alert(action.result.error, action.result.errormessages.join("\n"));
               }
            }
        };
    }
});