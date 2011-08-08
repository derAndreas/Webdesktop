<?php
/**
 * Form for add/edit a user
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Form_User
 * @extends Zend_Form
 * @todo move this to an own namespace, like Admin_Form_User or Admin_Form_Member
 * @todo split the form into an own add and edit class, extend from a base user manage form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_User extends Zend_Form {
    /**
     * mode of the form
     *  - add
     *  - edit
     * @var string
     * @access protected
     */
    protected $mode;

    /**
     * create the form for user management
     *
     * @param array $groups
     * @param Admin_Model_DbRow_User $user
     * @param string $mode possible values add/edit, default 'add'
     */
    public function __construct(array $groups, Admin_Model_DbRow_User $user = NULL, $mode = 'add')
    {
        $this->mode = $mode;

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(TRUE)
             ->setLabel('Fullname: ')
             ->setAttrib('class', 'text span-5')
             ->addFilters(array('StripTags', 'StringTrim'))
             ->addValidator('notEmpty');

        $username = new Zend_Form_Element_Text('username');
        $username->setRequired(TRUE)
                 ->setLabel('Username: ')
                 ->setAttrib('class', 'text span-5')
                 ->addFilters(array('StripTags', 'StringTrim'))
                 ->addValidator('notEmpty')
                 ->addValidator('regex', false, array('/^[\w\.\-]+$/i'));

        $mail = new Zend_Form_Element_Text('mail');
        $mail->setRequired(TRUE)
             ->setLabel('E-Mailaddress: ')
             ->setAttrib('class', 'text span-5')
             ->setFilters(array('StripTags', 'StringTrim'))
             ->addValidators(array('notEmpty', 'EmailAddress'));


        $group = new Zend_Form_Element_Select('group');
        $group->setLabel('Group: ')
              ->setRequired(TRUE);
        FOREACH($groups AS $g) {
            $group->addMultiOption($g->get('id'), $g->get('name'));
        }
              

        $checkbox = new Zend_Form_Element_Checkbox('active');
        $checkbox->setLabel('User enabled: ')
                 ->setChecked(false)
                 ->setRequired(TRUE);

        $submit = new Zend_Form_Element_Submit($this->mode);
        $submit->setLabel('save');
        
        IF($mode === 'edit' && $user !== NULL) {
            $id = new Zend_Form_Element_Hidden('id');
            $id->setValue($user->get('id'))
               ->addValidator('Int');

            $name->setValue($user->get('name'));
            $username->setValue($user->get('username'));
            $mail->setValue($user->get('email'));
            $checkbox->setChecked($user->get('enabled') == 1 ? TRUE : FALSE);
            $group->setValue($user->get('groupid'));
        } ELSE {
            $id = NULL;
        }


        $this->addElements(array($name, $username, $mail, $checkbox, $group, $id, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));

    }

}

?>
