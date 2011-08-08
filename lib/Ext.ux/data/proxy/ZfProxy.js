/**
 * Zend Framework Ajax Proxy
 *
 * The proxy handles some parameters on URLs different. The DC param isn't added
 * with a question mark, its a / delimited url param.
 *
 * This needs more attention, especcially on params/extraparams and all other params
 * stuff, that can go into the URL. Then change the proxy type in every call.
 *
 * The webdesktop is working with the default ajax proxy, but the URLs are not so
 * nicely structured.
 *      With normal proxy it looks liek this
 *          /noc/webdesktop/api/request/_module/settings/_action/loadWallpapers?_dc=1312052330953&page=1&start=0&limit=25
 *      With ZF Proxy it currently looks liek this
 *          /noc/webdesktop/api/request/_dc/1312055130193
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux.data
 * @subpackage proxy
 * @namespace Ext.ux.data.proxy
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Ext.ux.data.proxy.ZfProxy
 * @extends Ext.data.proxy.Ajax
 * @alias zf
 * @todo get all params that could be in the url and delimit them with a slash instead of ?&
 */
Ext.define('Ext.ux.data.proxy.ZfProxy', {
    extend        : 'Ext.data.proxy.Ajax',
    alias         : 'proxy.zf',
    actionMethods : {
        create : 'POST',
        read   : 'POST',
        update : 'POST',
        destroy: 'POST'
    },

    /**
     * Generates a url based on a given Ext.data.Request object. By default, ServerProxy's buildUrl will
     * add the cache-buster param to the end of the url. Subclasses may need to perform additional modifications
     * to the url.
     * 
     * @param {Ext.data.Request} request The request object
     * @return {String} The url
     * @overwrite Ext.data.ServerProxy::buildUrl()
     */
    buildUrl: function(request) {
        var url = this.getUrl(request);

        if (!url) {
            throw new Error("You are using a ServerProxy but have not supplied it with a url.");
        }

        if (this.noCache) {
            url = url +  (url.substring(url.length-1) === '/' ? '' : '/') + Ext.String.format("{0}/{1}", this.cacheString, Ext.Date.now());
        }

        return url;
    }

});