Ext.define('Webdesktop.view.administration.users.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.administration_userlist',
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            title: 'List of Users',
            store: 'Webdesktop.store.administration.Users',
            closable: true, // fixme: need to set here, because has no effect in tabpanel defaults configuration
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'right',
                items: [{
                    text: '',
                    iconCls: 'ux-icon-user-add',
                    tooltip: 'Add a new User'
                }, {
                    text: '',
                    iconCls: 'ux-icon-user-edit',
                    tooltip: 'Edit the selected User'
                }, {
                    text: '',
                    iconCls: 'ux-icon-user-delete',
                    tooltip: 'Delete the selected user'
                }, '-', {
                    text: '',
                    iconCls: 'ux-icon-password',
                    tooltip: 'Change the password of the selected user'
                }, '-',{
                    text: '',
                    iconCls: 'ux-icon-reload',
                    tooltip: 'Reload the list of users'
                }]
            }],
            columns: [{
                text: 'Name',
                flex: 1,
                dataIndex: 'name'
            },{
                text: 'Username',
                flex: 1,
                dataIndex: 'username'
            },{
                text: 'E-Mail',
                flex: 1,
                dataIndex: 'email'
            },{
                text: 'GroupName',
                flex: 1,
                dataIndex: 'groupname'
            },{
                xtype: 'checkcolumn',
                header: 'Enabled',
                width: 60,
                dataIndex: 'enabled'
            }]
        });
        me.callParent();
    }
});