<?php
/**
 * Form for add/edit a group
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
 * @class Admin_Form_Group
 * @extends Zend_Form
 * @todo move this to an own namespace, like Admin_Form_Group or Admin_Form_Member
 * @todo split the form into an own add and edit class, extend from a base group manage form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Group extends Zend_Form {
    /**
     * mode of the form
     *  - add
     *  - edit
     * @var string
     * @access protected
     */
    protected $mode;

    /**
     * create form for group management
     * 
     * @param Admin_Model_DbRow_Group $group default NULL
     * @param string $mode possible values add/edit, default 'add'
     */
    public function __construct(Admin_Model_DbRow_Group $group = NULL, $mode = 'add')
    {
        $this->mode = $mode;

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(TRUE)
             ->setLabel('Groupname: ')
             ->setAttrib('class', 'text span-5')
             ->addFilters(array('StripTags', 'StringTrim'))
             ->addValidator('notEmpty');

        $desc = new Zend_Form_Element_Textarea('description');
        $desc->setLabel('Description: ')
             ->setFilters(array('StringTrim'))
             ->addValidator('StringLength', false, array(0, 255));

        $id = new Zend_Form_Element_Hidden('id');

        $submit = new Zend_Form_Element_Submit($this->mode);
        $submit->setLabel('Save');
        
        IF($mode === 'edit' && $group !== NULL) {
            $name->setValue($group->get('name'));
            $desc->setValue($group->get('description'));
            $id->setValue($group->get('id'));
        }


        $this->addElements(array($name, $desc, $id, $submit));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));

    }

}

?>
