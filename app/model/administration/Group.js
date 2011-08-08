/**
 * Define the Group Model
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
 * @class Webdesktop.model.administration.Group
 * @extends Ext.ux.data.Model
 */
Ext.define('Webdesktop.model.administration.Group', {
    extend: 'Ext.ux.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id',           type: 'int'},
        {name: 'name',         type: 'string'},
        {name: 'description',  type: 'string'},
        {name: 'memberscount', type: 'int'}
    ],
    validations: [
        {type: 'presence', field: 'id'},
        {type: 'presence', field: 'name'},
        {type: 'presence', field: 'description'}
    ]
});