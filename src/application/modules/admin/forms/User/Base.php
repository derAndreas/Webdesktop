<?php
/**
 * Base Form for all user forms
 *
 * Reduces LOC and maintain complexity
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_User
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_User_Base
 * @extends Admin_Form_Base
 */
class Admin_Form_User_Base extends Admin_Form_Base
{
    /**
     * Constructor
     * Needs to be called by any extending form to add default decorators
     * 
     * @param array $groups
     */
    public function  __construct(array $groups)
    {
        $group = new Zend_Form_Element_Select('group', array(
            'required'   => true,
            'label'      => 'Group:'
        ));
        FOREACH($groups AS $g) {
            $group->addMultiOption($g->get('id'), $g->get('name'));
        }

        $this->addElements(array(
            new Zend_Form_Element_Text('name', array(
                'required'   => true,
                'label'      => 'Fullname:',
                'attribs'    => array('class' => 'text span-5'),
                'filters'    => array('StripTags', 'StringTrim'),
                'validators' => array('notEmpty')
            )),

            new Zend_Form_Element_Text('username', array(
                'required'   => true,
                'label'      => 'Username:',
                'attribs'    => array('class' => 'text span-5'),
                'filters'    => array('StripTags', 'StringTrim'),
                'validators' => array(
                    'notEmpty',
                    array('Regex',
                          false,
                          array('/^[\w\.\-]+$/i'))
                )
            )),

            new Zend_Form_Element_Text('mail', array(
                'required'   => true,
                'label'      => 'E-Mailaddress:',
                'attribs'    => array('class' => 'text span-5'),
                'filters'    => array('StripTags', 'StringTrim'),
                'validators' => array('notEmpty', 'EmailAddress')
            )),

            $group,

            new Zend_Form_Element_Checkbox('active', array(
                'required'   => true,
                'label'      => 'User enabled:',
                'checked'    => true
            )),

            new Zend_Form_Element_Submit('saveBtn', array(
                'label'     => 'Save'
            ))
        ));

        parent::__construct();
    }
}

?>
