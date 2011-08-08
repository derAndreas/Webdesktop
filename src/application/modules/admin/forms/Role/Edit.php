<?php
/**
 * Form to edit a Role in the ACL
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Role
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_Form_Role_Edit
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Role_Edit extends Zend_Form
{
    /**
     * create the form to edit a role
     * 
     * @param Admin_Model_DbRow_Role $role
     * @param array $groups
     * @param array $users
     * @param array $inherits
     * @access public
     */
    public function __construct(Admin_Model_DbRow_Role $role,
                                array $groups,
                                array $users,
                                array $inherits)
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(TRUE)
             ->setLabel('Name of the role: ')
             ->setAttrib('class', 'text span-5')
             ->addFilters(array('StripTags', 'StringTrim'))
             ->addValidator('notEmpty')
             ->setValue($role->get('name'));

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description: ')
                    ->addFilter('StripTags')
                    ->setValue($role->get('description'));


        $submit = new Zend_Form_Element_Submit('edit');
        $submit->setLabel('save');

        $selectGroups = new Zend_Form_Element_Multiselect('groups');
        $selectGroups->setLabel('Groups: ');
        FOREACH($groups AS $group) {
            $selectGroups->addMultiOption($group->get('id'), $group->get('name'));
        }
        #$selectGroups->setValue(array(2,3)); //TODO: ?? Don't remember why this is here, debug later

        $selectUsers = new Zend_Form_Element_Multiselect('users');
        $selectUsers->setLabel('Users: ');
        FOREACH($users AS $user) {
            $selectUsers->addMultiOption($user->get('id'), $user->get('name'));
        }
        

        $inheritRole = new Zend_Form_Element_Multiselect('inherit');
        $inheritRole->setLabel('Roles: ')
                    ->addMultiOption(0, 'none');
        FOREACH($inherits AS $inRole) {
            $inheritRole->addMultiOption($inRole->get('id'), $inRole->get('name'));
        }

        $this->addElements(array($name, $description, $selectGroups, $selectUsers, $inheritRole));
        $this->addDisplayGroup(array('name', 'description'), 'metadata', array('legend' => 'General Informations'));
        $this->addDisplayGroup(array('groups', 'users'), 'roleassignments', array('legend' => 'Apply groups and users to this role'));
        $this->addDisplayGroup(array('inherit'), 'roleinherit', array('legend' => 'Inherit the rights from the following roles'));
        $this->addElement($submit);
        

        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            'Form'
        ));

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));

    }

}

?>
