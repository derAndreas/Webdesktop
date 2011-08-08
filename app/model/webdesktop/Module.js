/**
 * Define the Module Model
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model
 * @namespace Webdesktop.model.webdesktop
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */
/**
 * @class Webdesktop.model.webdesktop.Module
 * @extends Ext.data.Model
 * @todo Needs to be refactored! Not all fields match the current data structure
 *       from the backend. This could solve some problems in the desktop controller
 *       and maybe even in Settings Modeul (shortcuts/autorun).
 *       Should be done wisely, our could break the whole application
 * @todo Add types for fields
 */
Ext.define('Webdesktop.model.webdesktop.Module', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'moduleId'    },
        { name: 'controller'  },
        { name: 'iconCls' },
        { name: 'text'    },
        { name: 'tooltip' },
        { name: 'menuPath'    }
    ]
});
