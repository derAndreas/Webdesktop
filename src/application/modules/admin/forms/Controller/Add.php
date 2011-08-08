<?php
/**
 * Form to add a Controller
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
 * @class Admin_Form_Controller_Add
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Controller_Add extends Zend_Form {
    /**
     * create the form for add a controller to the database
     *
     * @param int $moduleId
     * @param int $controllerId
     * @access public
     */
    public function __construct($moduleId, $controllerId)
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($moduleId);

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($controllerId);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags');

        $submit = new Zend_Form_Element_Submit('addbutton');
        $submit->setLabel('Add');

        $this->addElements(array($module, $controller, $description, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            'Form'
        ));

    }

}
?>
