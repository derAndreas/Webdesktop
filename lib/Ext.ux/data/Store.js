/**
 * Custom Store extenstion
 *  - to enable some load event
 *  - clone a store with data
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
 * @class Ext.ux.data.Store
 * @extends Ext.data.Store
 * @todo get all params that could be in the url and delimit them with a slash instead of ?&
 */
Ext.define('Ext.ux.data.Store', {
    extend: 'Ext.data.Store',
    /**
     * @constructor
     * @todo There is some code in the constructor, and the meaning of it is not really known anymore
     *       Don't know why I intercept the load event. Check where it is used and why it is used.
     *       If nothing found, remove it.
     */
    constructor: function(config) {
        var me = this;
        if(config && config.listeners && config.listeners.load) {
            config.listeners.load = Ext.Function.createInterceptor(config.listeners.load, me.onLoadStoreEvent);
        }
        if(me.listeners && me.listeners.load) {
            me.listeners.load = Ext.Function.createInterceptor(me.listeners.load, me.onLoadStoreEvent);
        }
        me.callParent(arguments);
    },
    /**
     * Function is bound to a possible load event in the store, configured in the
     * constructor. Because the meaning is unknown, this can be removed, if the
     * constructor is cleaned up.
     *
     * @param {Ext.data.Store} store
     * @param {Ext.data.Model} record
     * @param {Boolean} successful
     * @param {Object} options
     * @return {Boolean}
     */
    onLoadStoreEvent: function(store, records, successful, options) {
        if(successful == false) {
            return false;
        }

        return true;
    },
    /**
     * Clone a store with data
     * 
     * To clone a store we need to use record.copy.
     * Ext.data.Model uses a global internalId property which causes problems
     * if you use the store twice
     *
     * @return {Ext.data.Store}
     */
    clone: function() {
        var store = Ext.create('Ext.data.Store', {
            model: this.model.modelName
        });
        this.data.each(function(record) {
            this.add(record.copy());
        }, store.data);
        return store;
    }
});
