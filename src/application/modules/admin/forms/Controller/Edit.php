<?php
/**
 * Form to edit a Controller
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Controller
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Controller_Edit
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Controller_Edit extends Zend_Form {

    /**
     * create the form to edit controller data
     *
     * @param Admin_Model_DbRow_Controller $ctrl
     * @access public
     */
    public function __construct(Admin_Model_DbRow_Controller $ctrl)
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($ctrl->get('moduleName'));

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($ctrl->get('controllerName'));

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags')
                    ->setValue($ctrl->get('description'));

        $hidden = new Zend_Form_Element_Hidden('id');
        $hidden->setValue($ctrl->get('id'))
               ->setRequired(TRUE);

        $submit = new Zend_Form_Element_Submit('editbutton');
        $submit->setLabel('Edit');

        $this->addElements(array($module, $controller, $description, $submit, $hidden));

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
