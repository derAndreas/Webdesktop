<?php
/**
 * Form to change the password for a user
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Changepassword
 * @extends Zend_Form
 * @todo move this to an own namespace, like Admin_Form_User or Admin_Form_Member
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Changepassword extends Zend_Form {

    /**
     * Create form, so that the admin can change the users password
     *
     * @param Admin_Model_DbRow_User $user
     */
    public function __construct(Admin_Model_DbRow_User $user)
    {
        $pass1 = new Zend_Form_Element_Password('pass1');
        $pass1->setLabel('Password: ')
              ->setAttrib('class', 'text span-5')
              ->setRequired(TRUE)
              ->addValidator('notEmpty')
              ->addValidator('StringLength', false, array(8, 64));
              
        $pass2 = new Zend_Form_Element_Password('pass2');
        $pass2->setLabel('Repeat Password: ')
              ->setAttrib('class', 'text span-5')
              ->setRequired(TRUE)
              ->addValidator('notEmpty')
              ->addValidator('StringLength', false, array(8, 64));

        $hid = new Zend_Form_Element_Hidden('id');
        $hid->setRequired(TRUE)
            ->setValue($user->get('id'));

        $submit = new Zend_Form_Element_Submit('changepass');
        $submit->setLabel('save');

        $this->addElements(array($pass1, $pass2, $hid, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));
    }

}
?>
