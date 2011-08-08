/**
 * Define the User Model
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
 * @class Webdesktop.model.administration.User
 * @extends Ext.data.Model
 */
Ext.define('Webdesktop.model.administration.User', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id',        type: 'int'},
        {name: 'name',      type: 'string'},
        {name: 'username',  type: 'string'},
        {name: 'email',     type: 'string'},
        {name: 'groupname', type: 'string'},
        {name: 'groupid',   type: 'int'},
        {name: 'enabled',   type: 'int'}
    ],
    validations: [
        {type: 'presence', field: 'id'},
        {type: 'presence', field: 'name'},
        {type: 'presence', field: 'username'},
        {type: 'presence', field: 'groupid'},
        {type: 'format',   field: 'id',    matcher: /^\d+$/},
        {type: 'format',   field: 'email', matcher: /^(\w+)([\-+.][\w]+)*@(\w[\-\w]*\.){1,5}([A-Za-z]){2,6}$/} // email regex from Ext.form.field.VTypes
    ]
});