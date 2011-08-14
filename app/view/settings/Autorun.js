/**
 * The Settings Autorun panel
 *
 * User can change the modules, that should startup when launching the webdesktop
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Settings
 * @namespace Webdesktop.view.settings
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Webdesktop.view.settings.Autorun
 * @extends Ext.panel.Panel
 * @alias settings_autorun
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 */
Ext.define('Webdesktop.view.settings.Autorun', {
    extend : 'Ext.panel.Panel',
    alias  : 'widget.settings_autorun',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            title    : 'Autorun',
            layout   : 'border',
            border   : false, //FIXME: see class comment, bug
            frame    : false,
            defaults : {
                bodyPadding : '20',
                border      : false //FIXME: see class comment, bug
            },
            items    : [{
                xtype       : 'treepanel',
                region      : 'west',
                border      : false, //FIXME: see class comment, bug
                rootVisible : false,
                useArrows   : true,
                width       : 250
            }, {
                border : false, //FIXME: see class comment, bug
                region : 'center',
                html   : 'Please select the modules on the left side, that should autorun after launching the desktop.<br/>'
            }],
            buttons  : [{
                text       : 'Save',
                actionType : 'save'
            }]
        });
        
        me.callParent();
    }
});