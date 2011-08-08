<?php
/**
 * Description of login
 *
 * @author Godfather
 */
class App_Form_Auth_LoginDb extends Zend_Form {
    /**
     *
     * @return
     * @access public
     */
    public function __construct($options = NULL)
    {
        parent::__construct($options);

        $this->setName('Login Formular');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Benutzername')
                 ->setAttrib('class', 'text')
                 ->setRequired(TRUE)
                 ->addFilters(array('StripTags', 'StringTrim'))
                 ->addValidator('notEmpty');
                 
        $passwort = new Zend_Form_Element_Password('passwort');
        $passwort->setLabel('Passwort')
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
