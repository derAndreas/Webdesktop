/**
 * Form to Edit a new Role in the system
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
 * @class Webdesktop.view.administration.roles.Edit
 * @extends Webdesktop.view.administration.roles.BaseForm
 * @alias administration_roleedit
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 * @todo closable config is configured in the tabpanel defaults config, but not applied, configured in this formpanel too
 *       see: http://www.sencha.com/forum/showthread.php?142085-TabPanel-defaults-closable-true-not-configurable&p=631184
 */
Ext.define('Webdesktop.view.administration.roles.Edit', {
    extend: 'Webdesktop.view.administration.roles.BaseForm',
    alias: 'widget.administration_roleedit',
    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            title    : 'Edit Role',
            closable : true, //FIXME: see class comment, bug
            border   : false //FIXME: see class comment, bug
        });

        me.callParent();

    }
});