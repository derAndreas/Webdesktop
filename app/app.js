Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext.ux': 'lib/Ext.ux'
    }
});

/**
 * General Information:
 *
 * - all aliases are not namespaced, because Ext.ComponentQuery can not work with "." in alias name
 *   At the moment all aliases are "namespaced" with an underscore
 *   Watch bug report: http://www.sencha.com/forum/showthread.php?132643-Ext.ComponentQuery.query%28%29-for-ItemId-s-xtypes-containing-.
 *
 * @todo Fix the onAjaxRequest handling
 *          Idea was: If any error occure during any ajax request fetch the error and show some decent error to the user
 *                    or show the login page if session timed out
 */

Ext.application({
    name: 'Webdesktop',
    appFolder: 'app',
    autoCreateViewport: true,
    /**
     * @todo docs
     * @todo maybe move path to global to remove static configuration
     */
    cssFolder: 'resources/css/',
    controllers: [
        'webdesktop.Desktop',
        'webdesktop.Login'
    ],
    views: [
        'Webdesktop.view.Viewport'
    ],
    stores: [],
    /**
     * @todo docs
     */
    ERRORCODE: {
        'BADREQUEST' : 400,
        'AUTHENTICATION' : 401,
        'PRECONDITION' : 412
    },
    /**
     * @todo docs
     */
    launch: function() {
        var me = this;
        Ext.QuickTips.init();
        Ext.Ajax.on('requestcomplete', me.onAjaxRequest,me);
    },
    /**
     * @todo docs
     */
    loadCssFile: function(name) {
        Ext.core.DomHelper.append(Ext.getHead(), {tag: 'link', type: 'text/css', rel: 'stylesheet', href: this.cssFolder + name});
    },
    /**
     * @todo docs
     */
    onAjaxRequest: function(connection, response, options) {
        var me = this; // scope: app
        try {
            if (response.responseText) {
                var jsonData = Ext.JSON.decode(response.responseText);
                if(jsonData.success === false) {
                    switch(jsonData.code) {
                        case me.ERRORCODE.AUTHENTICATION:
                            console.log('User timed out, force to login');
                        break;
                        case me.ERRORCODE.BADREQUEST:
                            console.log('check for MSG, display MSG');
                        break;
                        case me.ERRORCODE.PRECONDITION:
                            console.log('check for MSG, display MSG');
                        break;
                        default:
                            console.log('unknown error occured');
                    }
                }
            }
        } catch(err) {
            console.log('Irregular JSON');
        }
    }
});
