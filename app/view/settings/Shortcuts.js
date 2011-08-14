/**
 * The Settings Shortcuts panel
 *
 * User can select which module icons are placed during startup of webdesktop
 * on the desktop for quick access to them.
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
 * @class Webdesktop.view.settings.Shortcuts
 * @extends Ext.panel.Panel
 * @alias settings_shortcuts
 * @todo seems like a "border bug", border config must be in the formpanel definition
 *       or borders are rendered
 *       see: http://www.sencha.com/forum/showthread.php?140245-4.0.2a-Component-border-configuration-does-not-work-even-in-very-simple-cases/
 */
Ext.define('Webdesktop.view.settings.Shortcuts', {
    extend : 'Ext.panel.Panel',
    alias  : 'widget.settings_shortcuts',

    initComponent: function() {
        var me = this;

        Ext.apply(me, {
            title    : 'Shortcuts',
            layout   : 'border',
            border   : false, //FIXME: see class comment, bug
            frame    : false,
            defaults : {
                bodyPadding : '20',
                border      : false //FIXME: see class comment, bug
            },
            buttons: [{
                text        : 'Save',
                actionType  : 'save'
            }],
            items    : [{
                xtype       : 'treepanel',
                region      : 'west',
                border      : false, //FIXME: see class comment, bug
                rootVisible : false,
                useArrows   : true,
                width       : 250
            }, {
                region      : 'center',
                border      : false, //FIXME: see class comment, bug
                html        : 'Please select the shortcuts on the left side, that should be visible on the desktop.<br/>' +
                              'Press "Save" to have the shortcuts placed on the desktop after you refresh or login later again'
            }]
        });
        
        me.callParent();
    }
});