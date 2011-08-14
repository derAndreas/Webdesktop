/**
 * Base form for all actions with roles
 *
 * Add/Edit/Delete form extend from this baseform. Reduce the LOC and complexity
 * in maintaining the code.
 *
 * The form is a lot modified in the validation way. because grids are part of
 * the form and need to be submitted like fields, the validation and submit action
 * is taken care in here.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.view.administration.groups
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.administration.roles.BaseForm
 * @extends Ext.form.Panel
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 * @todo Find a better way, if the extending form is "Add" for the hiddenfield (top of initComponent())
 */
Ext.define('Webdesktop.view.administration.roles.BaseForm', {
    extend : 'Ext.form.Panel',

    /**
     * @cfg {Ext.data.Store} assignedUsersStore Store for the assigned users to the role
     */
    assignedUsersStore  : null,
    /**
     * @cfg {Ext.data.Store} assignedGroupsStore Store for the assigned groups to the role
     */
    assignedGroupsStore : null,
    /**
     * @cfg {Ext.data.Store} assignedRolesStore Store for the assigned inherited roles to the role
     */
    assignedRolesStore  : null,

    /**
     * @cfg {Ext.ux.data.Store} existingUsersStore Store for the existing users to the role
     */
    existingUsersStore  : null,
    /**
     * @cfg {Ext.ux.data.Store} existingUsersStore Store for the existing groups to the role
     */
    existingGroupsStore : null,
    /**
     * @cfg {Ext.ux.data.Store} existingRolesStore Store for the existing inherited roles to the role
     */
    existingRolesStore  : null,

    initComponent: function() {
        var me = this,
            hiddenfield;

        hiddenfield = {
            xtype      : 'hiddenfield',
            name       : 'id',
            allowBlank : me.$className === "Webdesktop.view.administration.roles.Add" ? false : true,   // if we have add panel we dont need this field
            disabled   : false
        };

        /**
         * Define the stores (users, groups, roles) of assigned elements
         *
         * After DnD or initial Load, the panel title of the elements in the store
         * is updated with "datachanged" event.
         */
        me.assignedUsersStore = Ext.create('Ext.data.Store', {
            model     : 'Webdesktop.model.administration.User',
            listeners : {
                datachanged : function(store) {
                    me.down('tabpanel panel[tabPanelIdent=users]').setTitle('Assigned Users (' + store.getCount() + ')');
                }
            }
        });
        me.assignedGroupsStore = Ext.create('Ext.data.Store', {
            model     : 'Webdesktop.model.administration.Group',
            listeners : {
                datachanged : function(store) {
                    me.down('tabpanel panel[tabPanelIdent=groups]').setTitle('Assigned Groups (' + store.getCount() + ')');
                }
            }
        });
        me.assignedRolesStore = Ext.create('Ext.data.Store', {
            model     : 'Webdesktop.model.administration.Role',
            listeners : {
                datachanged : function(store) {
                    me.down('tabpanel panel[tabPanelIdent=roles]').setTitle('Assigned Roles (' + store.getCount() + ')');
                }
            }
        });

        /**
         * @todo:   Problem of shared stores
         *          The admin can open several tabs in the mainpanel that uses the shared stores
         *          If in one gridpanel has any change event, all other gridpanels that uses
         *          the same store are updated.
         *          As a workaround, every store extends Ext.ux.data.Store. The custom store
         *          implements a clone() functionality.
         */
        me.existingUsersStore  = Ext.getStore('Webdesktop.store.administration.Users').clone();
        me.existingGroupsStore = Ext.getStore('Webdesktop.store.administration.Groups').clone();
        me.existingRolesStore  = Ext.getStore('Webdesktop.store.administration.Roles').clone();

        Ext.apply(me, {
            closable      : true, //FIXME: see class comment, bug
            border        : false, //FIXME: see class comment, bug
            bodyStyle     :'padding:15px',
            defaultType   : 'textfield',
            fieldDefaults : {
                msgTarget  : 'top',
                labelWidth : 100
            },
            defaults      : {
                anchor : '60%',
                border : false
            },
            items         : [ hiddenfield,
                {
                    name       : 'name',
                    fieldLabel : 'Name of the Role',
                    allowBlank : false
                }, {
                    xtype      : 'textareafield',
                    name       : 'description',
                    fieldLabel : 'Description',
                    allowBlank : false
                }, {
                    // FIXME: Complex problem, see Webdesktop.model.administration.Role for details
                    xtype      : 'checkboxfield',
                    name       : 'enabled',
                    fieldLabel : 'Enabled',
                    boxLabel   : 'Enabled'
                }, {
                    xtype      : 'tabpanel',
                    plain      : true,
                    activeItem : 0,
                    layout     : 'hbox',
                    anchor     : '100%',
                    defaults   : {
                        frame  : true,
                        border : false,
                        height : 200 //FIXME: need to set a height or grids height is 2px, really?!
                    },
                    items      : [
                        me.getAssignedUsersTabConfig(),
                        me.getAssignedGroupsTabConfig(),
                        me.getAssignedRolesTabConfig()
                    ]
                }
            ],
            dockedItems : [{
                xtype : 'toolbar',
                dock  : 'top',
                items : ['->', {
                    text    : 'Save',
                    iconCls : 'ux-icon-accept'
                },{
                    text    : 'Cancel',
                    iconCls : 'ux-icon-cancel'
                }]
            }]
        });

        me.callParent();
        // we overwrite the getValues() method from the Ext.form.Basic to collect the grid values on submit
        me.form.getValues = Ext.bind(me.getValues, me.form, [me], true);
        // we overwrite the setValues() method from the Ext.form.Basic to set all values on loadRecord
        me.form.setValues = Ext.bind(me.setValues, me.form, [me], true);
    },
    /**
     * Lazy way to get the values from the grid too in the form submit
     * @return mixed
     * @overwrite Ext.form.Basic.setValues()
     */
    getValues: function(asString, dirtyOnly, includeEmptyText, useDataValues, refPanel) {
        var me     = this,
            panel  = Array.prototype.slice.call(arguments, -1)[0],
            values = [], 
            users  = [],
            groups = [],
            roles  = [];

        // Iterate over each field, code from Ext.form.Basic::setValues()
        me.getFields().each(function(field) {
            if (!dirtyOnly || field.isDirty()) {
                var data = field[useDataValues ? 'getModelData' : 'getSubmitData'](includeEmptyText);
                if (Ext.isObject(data)) {
                    Ext.iterate(data, function(name, val) {
                        if (includeEmptyText && val === '') {
                            val = field.emptyText || '';
                        }
                        if (name in values) {
                            var bucket = values[name],
                                isArray = Ext.isArray;
                            if (!isArray(bucket)) {
                                bucket = values[name] = [bucket];
                            }
                            if (isArray(val)) {
                                values[name] = bucket.concat(val);
                            } else {
                                bucket.push(val);
                            }
                        } else {
                            values[name] = val;
                        }
                    });
                }
            }
        });
        // helper functions. Iterates over the store and stores the id's of the
        // record in the array (second param). Because scope is the array,
        // use "this.push" in the function
        var getIdFn = function(r) {this.push(r.get('id'))};

        panel.assignedUsersStore.each(getIdFn, users);
        panel.assignedGroupsStore.each(getIdFn, groups);
        panel.assignedRolesStore.each(getIdFn, roles);

        Ext.apply(values, {
            users  : Ext.encode(users),
            groups : Ext.encode(groups),
            roles  : Ext.encode(roles)
        });

        if (asString) {
            values = Ext.Object.toQueryString(values);
        }
        return values;
    },
    /**
     * Set the values in the form and to the grid panels of existing users/groups/roles
     *
     * Because Grids are not form elements, this custom implmentation makes it
     * possible to add the data from the roles listing grid into this form.
     *
     * Most code is from the overwritten Ext.form.Basic::setValues()
     *
     *
     * @param {Object} values
     * @param {Ext.form.Panel} panel the form panel reference to load data into
     * @return {Ext.form.Panel} this
     * @overwrite Ext.form.Basic.setValues()
     */
    setValues: function(values, refPanel) {
        var me = this,
            setVal;

        setVal = function (fieldId, val) {
            var field = me.findField(fieldId);
            if (field) {
                field.setValue(val);
                if (me.trackResetOnLoad) {
                    field.resetOriginalValue();
                }
            }
        }

        if (Ext.isArray(values)) {
            // array of objects
            Ext.each(values, function(val) {
                setVal(val.id, val.value);
            });
        } else {
            // object hash
            Ext.iterate(values, setVal);
        }

        /**
         * load the users, groups and roles into their grids and remove from the other store
         * use me._record as source, because the param values is only record.data
         *
         * @todo remove a record from a grid with the record object does not work
         */
        me._record.users().each(function(record) {
            refPanel.assignedUsersStore.add(record);
            //refPanel.existingUsersStore.remove(record); //FIXME:  just using the record does not work
            refPanel.existingUsersStore.remove(refPanel.existingUsersStore.getById(record.get('id')));
        }, me);
        me._record.groups().each(function(record) {
            refPanel.assignedGroupsStore.add(record);
            //refPanel.existingUsersStore.remove(record); //FIXME:  just using the record does not work
            refPanel.existingGroupsStore.remove(refPanel.existingGroupsStore.getById(record.get('id')));
        }, me);
        me._record.inherits().each(function(record) {
            refPanel.assignedRolesStore.add(record);
            //refPanel.existingUsersStore.remove(record); //FIXME:  just using the record does not work
            refPanel.existingRolesStore.remove(refPanel.existingRolesStore.getById(record.get('id')));
        }, me);
        // do not show self role as inheritable role
        refPanel.existingRolesStore.remove(refPanel.existingRolesStore.getById(me._record.get('id')));

        return this;
    },

    /**
     * Generate the config for the Assigned Users grid TabPanel
     *
     * Code is used in this::initComponent(), but to have a better overview
     * put the code in this function
     *
     * @return {Object}
     * @todo check if refactoring is possible for less redundant code on the getAssigned*TabConfig functions
     */
    getAssignedUsersTabConfig: function() {
        var me = this;

        return {
            xtype         : 'panel',
            title         : 'Assigned Users (0)',
            tabPanelIdent : 'users', // need for easy ident the tabpanel in the store datachanged event, cannot use itemId, because several edits could be open
            layout        : {
                type  : 'hbox',
                align : 'stretch'
            },
            defaults      : {
                flex       : 1,
                stripeRows : true
            },
            items: [{
                xtype      : 'gridpanel',
                title      : 'Available Users',
                store      : me.existingUsersStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'firstGridDDGroup',
                        dropGroup : 'secondGridDDGroup'
                    }
                },
                columns: [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                },{
                    text      : 'Username',
                    dataIndex : 'username',
                    flex      : 1
                },{
                    text      : 'E-Mail',
                    dataIndex : 'email',
                    flex      : 1
                },{
                    text      : 'GroupName',
                    dataIndex : 'groupname',
                    flex      : 1
                },{
                    xtype     : 'checkcolumn',
                    dataIndex : 'enabled',
                    header    : 'Enabled',
                    width     : 60
                }]
            }, {
                xtype      : 'gridpanel',
                title      : 'Assigned Users',
                store      : me.assignedUsersStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'secondGridDDGroup',
                        dropGroup : 'firstGridDDGroup'
                    }
                },
                columns    : [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                },{
                    text      : 'Username',
                    dataIndex : 'username',
                    flex      : 1
                }]
            }]
        };
    },
    /**
     * Generate the config for the Assigned Groups grid TabPanel
     *
     * Code is used in this::initComponent(), but to have a better overview
     * put the code in this function
     *
     * @return {Object}
     * @todo check if refactoring is possible for less redundant code on the getAssigned*TabConfig functions
     */
    getAssignedGroupsTabConfig: function() {
        var me = this;

        return {
            xtype         : 'panel',
            title         : 'Assigned Groups (0)',
            tabPanelIdent : 'groups', // need for easy ident the tabpanel in the store datachanged event, cannot use itemId, because several edits could be open
            layout        : {
                type  : 'hbox',
                align : 'stretch'
            },
            defaults      : {
                flex       : 1,
                stripeRows : true
            },
            items         : [{
                xtype      : 'gridpanel',
                title      : 'Available Groups',
                store      : me.existingGroupsStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'firstGridDDGroup',
                        dropGroup : 'secondGridDDGroup'
                    }
                },
                columns    : [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                },{
                    text      : 'Users in Group',
                    dataIndex : 'memberscount',
                    flex      : 1
                }]
            }, {
                xtype      : 'gridpanel',
                title      : 'Assigned Groups',
                store      : me.assignedGroupsStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'secondGridDDGroup',
                        dropGroup : 'firstGridDDGroup'
                    }
                },
                columns    : [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                },{
                    text      : 'Users in Group',
                    dataIndex : 'memberscount',
                    flex      : 1
                }]
            }]
        };
    },
    /**
     * Generate the config for the Assigned Roles grid TabPanel
     *
     * Code is used in this::initComponent(), but to have a better overview
     * put the code in this function
     *
     * @return {Object}
     * @todo check if refactoring is possible for less redundant code on the getAssigned*TabConfig functions
     */
    getAssignedRolesTabConfig: function() {
        var me = this;

        return {
            xtype         : 'panel',
            title         : 'Assigned Roles (0)',
            tabPanelIdent : 'roles', // need for easy ident the tabpanel in the store datachanged event, cannot use itemId, because several edits could be open
            layout        : {
                type  : 'hbox',
                align : 'stretch'
            },
            defaults      : {
                flex       : 1,
                stripeRows : true
            },
            items         : [{
                xtype      : 'gridpanel',
                title      : 'Available Roles',
                store      : me.existingRolesStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'firstGridDDGroup',
                        dropGroup : 'secondGridDDGroup'
                    }
                },
                columns    : [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                }]
            }, {
                xtype      : 'gridpanel',
                title      : 'Assigned Roles',
                store      : me.assignedRolesStore,
                viewConfig : {
                    plugins : {
                        ptype     : 'gridviewdragdrop',
                        dragGroup : 'secondGridDDGroup',
                        dropGroup : 'firstGridDDGroup'
                    }
                },
                columns   : [{
                    text      : 'Name',
                    dataIndex : 'name',
                    flex      : 1
                }]
            }]
        };
    }
});