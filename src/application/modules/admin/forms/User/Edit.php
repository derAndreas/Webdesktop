<?php
/**
 * Edit a user form
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
 * @class Admin_Form_User_Edit
 * @extends Admin_Form_User_Base
 */
class Admin_Form_User_Edit extends Admin_Form_User_Base
{
    /**
     * Constructor to enhance the base form to edit a user
     * 
     * @param array $groups
     * @param Admin_Model_DbRow_User $user
     */
    public function  __construct(array $groups, Admin_Model_DbRow_User $user)
    {
        parent::__construct($groups);

        $this->addElement(
            new Zend_Form_Element_Hidden('id', array(
                'value'         => $user->get('id'),
                'validators'    => array('Int')
            ))
        );

        $this->getElement('name')->setValue($user->get('name'));
        $this->getElement('username')->setValue($user->get('username'));
        $this->getElement('mail')->setValue($user->get('email'));
        $this->getElement('active')->setChecked($user->get('enabled') == 1 ? TRUE : FALSE);
        $this->getElement('group')->setValue($user->get('groupid'));
    }
}

?>
