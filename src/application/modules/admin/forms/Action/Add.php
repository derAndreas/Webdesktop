<?php
/**
 * Form to add a User in the ACL
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Action
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Action_Add
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Action_Add extends Zend_Form {
    /**
     * Create the add form
     *
     * @param Admin_Model_DbRow_Controller $control
     * @param String $actionName
     */
    public function __construct($control, $actionName)
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($control->get('moduleName'));

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($control->get('controllerName'));

        $action = new Zend_Form_Element_Text('actionname');
        $action->setLabel('Action: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
               ))
               ->setValue($actionName);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags');

        $submit = new Zend_Form_Element_Submit('addbutton');
        $submit->setLabel('Add');

        $this->addElements(array($module, $controller, $action, $description, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            'Form'
        ));
    }

}
?>
