<?php
/**
 * Form to change the password for a user
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
 * @class Admin_Form_User_Changepassword
 * @extends Admin_Form_Base
 */
class Admin_Form_User_Changepassword extends Admin_Form_Base
{

    /**
     * Create form, so that the admin can change the users password
     *
     * @param Admin_Model_DbRow_User $user
     */
    public function __construct(Admin_Model_DbRow_User $user)
    {
        $this->addElements(array(
            new Zend_Form_Element_Password('pass1', array(
                'required'      => true,
                'label'         => 'Password:',
                'attribs'       => array('class' => 'text span-5'),
                'validators'    => array(
                    'notEmpty',
                    array('StringLength',
                        false,
                        array(8, 64)
                    )
                ),
            )),

            new Zend_Form_Element_Password('pass2', array(
                'required'      => true,
                'label'         => 'Repeat Password:',
                'attribs'       => array('class' => 'text span-5'),
                'validators'    => array(
                    'notEmpty',
                    array('StringLength',
                        false,
                        array(8, 64)
                    )
                ),
            )),

            new Zend_Form_Element_Hidden('id', array(
                'required'      => true,
                'value'         => $user->get('id')
            )),

            new Zend_Form_Element_Submit('changepass', array(
                'label'         => 'Save'
            ))
        ));

        parent::__construct();
    }

}
?>
