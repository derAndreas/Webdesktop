/**
 * Define the Role Model
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
 * @class Webdesktop.model.administration.Role
 * @extends Ext.ux.data.Model
 * @todo the field definition of "enabled" is part of a complex subject, because it is shared
 *       between form checkbox, grid checkcolumn, submitvalues and interpreting in the backend
 */
Ext.define('Webdesktop.model.administration.Role', {
    extend: 'Ext.ux.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id',          type: 'int'},
        {name: 'name',        type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'enabled',     type: 'int'}      // FIXME: Problems with enabled checkbox in edit form
    ],
    validations: [
        {type: 'presence', field: 'id'},
        {type: 'presence', field: 'name'},
        {type: 'presence', field: 'description'}
    ],
    associations: [{
        type: 'hasMany',
        model: 'Webdesktop.model.administration.User',
        name: 'users'
    },{
        type: 'hasMany',
        model: 'Webdesktop.model.administration.Group',
        name: 'groups'
    },{
        type: 'hasMany',
        model: 'Webdesktop.model.administration.Role',
        name: 'inherits'
    }]
});