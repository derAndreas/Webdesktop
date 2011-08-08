/**
 * Define the Permission Model
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Administration
 * @namespace Webdesktop.model.administration
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */
/**
 * @class Webdesktop.model.administration.Permission
 * @extends Ext.data.Model
 */
Ext.define('Webdesktop.model.administration.Permission', {
    extend: 'Ext.data.Model',
    idProperty: 'ident',
    fields: [
        {name: 'ident',    type: 'string'},
        {name: 'mcId',     type: 'int'},
        {name: 'aId',      type: 'int'},
        {name: 'roleName', type: 'string'},
        {name: 'roleId',   type: 'int'},
        {name: 'rule',     type: 'int'}
    ],
    validations: [
        {type: 'presence', field: 'ident'},
        {type: 'presence', field: 'roleId'},
        //{type: 'presence', field: 'rule'},  //FIXME: bug in 4.0.2a, does not validate (int) 0 to true, should be fixed in 4.1
        {type: 'format',   field: 'rule', matcher: /^\d+$/},
    ]
});

