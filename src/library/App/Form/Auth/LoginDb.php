<?php
/**
 * Form for the LoginDb Authentication Strategy
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Form
 * @namespace App_Form_Auth
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Form_Auth_LoginDb
 */
class App_Form_Auth_LoginDb extends Zend_Form {
    /**
     * Constructor to build the form
     */
    public function __construct($options = NULL)
    {
        parent::__construct($options);

        $this->setName('Login form');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                 ->setAttrib('class', 'text')
                 ->setRequired(TRUE)
                 ->addFilters(array('StripTags', 'StringTrim'))
                 ->addValidator('notEmpty');
                 
        $passwort = new Zend_Form_Element_Password('passwort');
        $passwort->setLabel('Password')
                 ->setAttrib('class', 'text')
                 ->setRequired(TRUE);
                 
        $strategy = new Zend_Form_Element_Hidden('strategy');
        $strategy->setValue('db');

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setRequired(FALSE)
               ->setIgnore(TRUE)
               ->setLabel('Login');

        $this->addElements(array($username, $passwort, $strategy, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));

    }
}
?>
