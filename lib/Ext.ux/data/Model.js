/**
 * Extension for the default Ext.data.Model
 *
 * - Handle new records validations easier
 *   In this app validations are used in the model with 'presence: id'.
 *   This will fail for new/added records, as they do not have any id yet.
 *   With the modifications here, call the validate with param set with keyValidateNew
 *   or isValidNew().
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Ext.ux.data
 * @subpackage Model
 * @namespace Ext.ux.data
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */
/**
 * @class Ext.ux.data.Model
 * @extends Ext.data.Model
 * @todo make it more universal and let the user configure the field(s) that
 *       should be filtered from the Ext.data.Error collection and still have
 *       a valid new record.
 */
Ext.define('Ext.ux.data.Model', {
    extend         : 'Ext.data.Model',
    keyValidateNew : 'add',
    /**
     * Override to have special handling for new records
     *
     * If we add new records and want to validate, the validation
     * fails because the model validation is: id must be present
     *
     * Adding this custom validate method, we can check if there is
     * a add action and remove all errors for the id field from Ext.data.Error
     * and return the object with real errors for the add process
     *
     * @param {String} type
     * @return {Ext.data.Error} errors
     * @override
     * @todo make it more universal and let the user configure the field(s) that
     *       should be filtered from the Ext.data.Error collection and still have
     *       a valid new record.
     */
    validate: function(type) {
        var errors = this.callParent();
        if(type === this.keyValidateNew) {  // check if this works
            // onAdd we have no "id" field. remove errors for this field
            errors.removeAll(errors.getByField('id'))
            return errors;
        }
        return errors;
    },
    /**
     * proper call to validate on new records
     * used in groups store filternew() method on syncing the store to backend
     * 
     * @return {Boolean}
     */
    isValidNew: function() {
        return this.validate(this.keyValidateNew).isValid();
    }
});