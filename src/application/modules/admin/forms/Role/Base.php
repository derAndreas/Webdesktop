<?php
/**
 * Base Form for all role forms
 *
 * Reduces LOC and maintain complexity
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
 * @class Admin_Form_Role_Base
 * @extends Admin_Form_Base
 */
class Admin_Form_Role_Base extends Admin_Form_Base
{

    /**
     * Constrcuts the form
     *
     * @param Admin_Model_DbRow_Role $role
     * @param array $groups
     * @param array $users
     * @param array $inherits
     */
    public function  __construct(Admin_Model_DbRow_Role $role, array $groups, array $users, array $inherits)
    {
        // create the dynamic multi options
        $selectGroups = new Zend_Form_Element_Multiselect('groups', array('label' => 'Groups:'));
        $selectUsers  = new Zend_Form_Element_Multiselect('users', array('label' => 'Users:'));
        $inheritRole  = new Zend_Form_Element_Multiselect('inherit', array(
            'label'         => 'Roles:',
            'multiOptions'  => array(0 => 'none')
        ));

        FOREACH($groups AS $group) {
            $selectGroups->addMultiOption($group->get('id'), $group->get('name'));
        }
        FOREACH($users AS $user) {
            $selectUsers->addMultiOption($user->get('id'), $user->get('name'));
        }
        FOREACH($inherits AS $inRole) {
            $inheritRole->addMultiOption($inRole->get('id'), $inRole->get('name'));
        }

        $this->addElements(array(
            new Zend_Form_Element_Text('name', array(
                'required'      => true,
                'label'         => 'Name of the role:',
                'attribs'       => array('class' => 'text span-5'),
                'filters'       => array('StripTags', 'StringTrim'),
                'validators'    => array('notEmpty'),
                'value'         => $role->get('name', ''),
                'order'         => 1
            )),

            new Zend_Form_Element_Textarea('description', array(
                'label'         => 'Description:',
                'filters'       => array('StripTags'),
                'value'         => $role->get('description'),
                'order'         => 2
            )),

            $selectGroups,
            $selectUsers,
            $inheritRole,

            new Zend_Form_Element_Submit('saveBtn', array(
                'label'         => 'Save',
                'order'         => 10
            )),
        ));

        $this->addDisplayGroups(array(
            array(
                array('name', 'description'),
                'metadata',
                array('legend' => 'General Informations')
            ),
            array(
                array('groups', 'users'),
                'roleassignments',
                array('legend' => 'Apply groups and users to this role')
            ),
            array(
                array('inherit'),
                'roleinherit',
                array('legend' => 'Inherit the rights from the following roles')
            )
        ));

        parent::__construct();
    }
}

?>
