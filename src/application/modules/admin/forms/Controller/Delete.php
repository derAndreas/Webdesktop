<?php
/**
 * Form to delete a Controller
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Controller
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Form_Controller_Delete
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Controller_Delete extends Zend_Form {

    /**
     * create the form to delete a controller from the database
     *
     * @param Zend_Db_Table_Row $row
     * @access public
     */
    public function __construct(Zend_Db_Table_Row $row)
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($row->uamc_module);

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($row->uamc_controller);

        $checkbox = new Zend_Form_Element_Checkbox('chkdelete');
        $checkbox->setLabel('Really delete? ')
                 ->setRequired(TRUE)
                 ->setChecked(FALSE);

        $hidden = new Zend_Form_Element_Hidden('id');
        $hidden->setValue($row->uamc_id)
               ->setRequired(TRUE);

        $submit = new Zend_Form_Element_Submit('deletebutton');
        $submit->setLabel('Delete');

        $this->addElements(array($module, $controller, $checkbox, $submit, $hidden));

        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));

    }

}
?>
