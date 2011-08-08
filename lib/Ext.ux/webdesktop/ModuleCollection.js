/**
 * Collection, where all user available modules are stored
 *
 * This class is a Ext.util.MixedCollection, but with the option to bind
 * listeners on creation time of the MixedCollection
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux
 * @subpackage Webdesktop
 * @namespace Ext.ux.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Ext.ux.webdesktop.ModuleCollection
 * @extends Ext.util.MixedCollection
 */
Ext.define('Ext.ux.webdesktop.ModuleCollection', {
    extend: 'Ext.util.MixedCollection',
    /**
     * @constructor
     * @param {Object} config
     * @param {Boolean} allowFn
     * @param {Function} keyFn
     */
    constructor: function(config, allowFn, keyFn) {
        Ext.apply(this, config || {});
        this.callParent(allowFn, keyFn);
    },
    /**
     * Custom getKey function
     *
     * Find modules not by the keyword "id", rather than "moduleId"
     *
     * @return {Ext.ux.webdesktop.Module}
     */
    getKey: function(module) {
        return module.moduleId;
    }
})