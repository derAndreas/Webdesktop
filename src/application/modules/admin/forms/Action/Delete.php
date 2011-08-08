<?php
/**
 * Form to delete a User from the ACL
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
 * @class Admin_Form_Action_Delete
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Action_Delete extends Zend_Form {

    /**
     * Generate the Delete form
     *
     * @param Admin_Model_DbRow_Action $rowAction
     * @param Admin_Model_DbRow_Controller $rowController
     */
    public function __construct(Admin_Model_DbRow_Action $rowAction, Admin_Model_DbRow_Controller $rowController)
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($rowController->get('moduleName'));

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($rowController->get('controllerName'));

        $action = new Zend_Form_Element_Text('action');
        $action->setLabel('Action: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($rowAction->get('actionName'));

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags')
                    ->setValue($rowAction->get('description'))
                    ->setAttribs(array(
                       'class' => 'text span-6',
                       'readonly' => 'true'
                   ));

        $checkbox = new Zend_Form_Element_Checkbox('chkdelete');
        $checkbox->setLabel('Really delete? ')
                 ->setRequired(TRUE)
                 ->setChecked(FALSE);

        $hidden = new Zend_Form_Element_Hidden('id');
        $hidden->setValue($rowAction->get('id'))
               ->setRequired(TRUE);

        $submit = new Zend_Form_Element_Submit('deletebutton');
        $submit->setLabel('Delete');

        $this->addElements(array($module, $controller, $action, $description, $checkbox, $submit, $hidden));

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
