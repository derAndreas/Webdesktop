/**
 * Custom RowEditing plugin extenstion
 *
 * Add a cancel event to the Roweditor
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux.grid
 * @subpackage plugin
 * @namespace Ext.ux.grid.plugin
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Ext.ux.grid.plugin.RowEditing
 * @extends Ext.grid.plugin.RowEditing
 * @alias ux.rowediting
 */
Ext.define('Ext.ux.grid.plugin.RowEditing', {
    extend :'Ext.grid.plugin.RowEditing',
    alias  : 'plugin.ux.rowediting',
    /**
     * Add the CancelEvent
     *
     * @override
     * @see http://www.sencha.com/forum/showthread.php?131482-Ext.ux.grid.plugin.RowEditing-add-some-usefull-features&highlight=roweditor
     */
    init:function(grid){
        var me = this;
        
        me.addEvents(
            /**
             * @event canceledit
             * Fires when the edit is canceled
             *
             * The event fires with the parameter {Object} context, which contains
             *     grid   - The grid this editor is on
             *     view   - The grid view
             *     store  - The grid store
             *     record - The record being edited
             *     row    - The grid table row
             *     column - The grid Column defining the column that initiated the edit
             *     rowIdx - The row index that is being edited
             *     colIdx - The column index that initiated the edit
             *
             * @param {Object} context
             */
            'canceledit'
        );
        me.callParent(arguments);
        grid.addEvents('canceledit');
        grid.relayEvents(me, ['canceledit']);
    },
    /**
     * Cancel the edit of a row and fire the cancel event
     *
     * @override
     */
    cancelEdit: function() {
        var me = this;

        if (me.editing) {
            me.getEditor().cancelEdit();
            me.callParent(arguments);

            me.fireEvent('canceledit', me.context);
        }
    },
});