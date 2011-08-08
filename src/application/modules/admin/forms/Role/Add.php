<?php
/**
 * Form to add a Role to the ACL
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Role
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Role_Add
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Role_Add extends Zend_Form
{
    /**
     * create the form to add a acl role
     *
     * @access public
     */
    public function __construct()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(TRUE)
             ->setLabel('Name der Rolle: ')
             ->setAttrib('class', 'text span-5')
             ->addFilters(array('StripTags', 'StringTrim'))
             ->addValidator('notEmpty');

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags');

        $submit = new Zend_Form_Element_Submit('add');
        $submit->setLabel('Speichern');

        $this->addElements(array($name, $description, $submit));
        
        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            'Form'
        ));

    }

}

?>
