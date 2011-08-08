/**
 * Define the Controller Model
 *
 * Informations about controller status:
 *  A Controller status can have the 3 different states
 *      0 => Everything is fine, controller found as file and is in the Database (OK)
 *      1 => Controller found in file, but is not in database (NEW)
 *      2 => Controller found in database, but file reference vanished (DEL)
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
 * @class Webdesktop.model.administration.Controller
 * @extends Ext.ux.data.Model
 */
Ext.define('Webdesktop.model.administration.Controller', {
    extend: 'Ext.ux.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id',             type: 'int'},
        {name: 'moduleName',     type: 'string'},
        {name: 'controllerName', type: 'string'},
        {name: 'enabled',        type: 'bool'},
        {name: 'virtual',        type: 'int'},
        {name: 'status',         type: 'int'},
        {name: 'description',    type: 'string'}
    ],
    validations: [
        {type: 'presence', field: 'id'},
        {type: 'presence', field: 'moduleName'},
        {type: 'presence', field: 'controllerName'},
        {type: 'format',   field: 'id',    matcher: /^\d+$/}
    ]
});