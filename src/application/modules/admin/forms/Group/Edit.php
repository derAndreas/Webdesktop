<?php
/**
 * Edit a Group form
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
 * @class Admin_Form_Group_Edit
 * @extends Admin_Form_Group_Base
 */
class Admin_Form_Group_Edit extends Admin_Form_Group_Base
{

    public function  __construct(Admin_Model_DbRow_Group $group)
    {
        parent::__construct();

        $this->addElement(
            new Zend_Form_Element_Hidden('id', array(
                'value'         => $group->get('id'),
                'validators'    => array('Int')
            ))
        );

        $this->getElement('name')->setValue($group->get('name'));
        $this->getElement('description')->setValue($group->get('description'));
    }
}

?>
