<?php
/**
 * Base Form for all group forms
 *
 * Reduces LOC and maintain complexity
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Group
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Group_Base
 * @extends Admin_Form_Base
 */
class Admin_Form_Group_Base extends Admin_Form_Base
{
    /**
     * Constructor
     */
    public function  __construct()
    {
        $this->addElements(array(
            new Zend_Form_Element_Text('name', array(
                'required'   => true,
                'label'      => 'Groupname:',
                'attribs'    => array('class' => 'text span-5'),
                'filters'    => array('StripTags', 'StringTrim'),
                'validators' => array('notEmpty')
            )),

            new Zend_Form_Element_Textarea('description', array(
                'label'      => 'Description:',
                'attribs'    => array('class' => 'span-5'),
                'filters'    => array('StripTags', 'StringTrim'),
                'validators' => array(
                    array('StringLength',
                          false,
                          array(0, 255))
                )
            )),

            new Zend_Form_Element_Submit('saveBtn', array(
                'label'     => 'Save'
            ))
        ));

        parent::__construct();
    }
}

?>
