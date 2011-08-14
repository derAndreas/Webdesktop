<?php
/**
 * Form to delete a User from the ACL
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Action
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Action_Delete
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Action_Delete extends Admin_Form_Action_Base
{

    /**
     * Generate the Delete form
     *
     * @param Admin_Model_DbRow_Controller $controller
     * @param Admin_Model_DbRow_Action $action
     */
    public function __construct(Admin_Model_DbRow_Controller $controller, Admin_Model_DbRow_Action $action)
    {
        parent::__construct($controller);

        $this->addElements(array(
            new Zend_Form_Element_Hidden('id', array(
                'required'  => true,
                'value'     => $action->get('id'),
                'order'     => 11
            )),
            new Zend_Form_Element_Checkbox('chkdelete', array(
                'required'  => true,
                'label'     => 'Really Delete?',
                'checked'   => false,
                'order'     => 6
            ))
        ));

        $this->getElement('action')->setValue($action->get('actionName'));
        $this->getElement('description')->setValue($action->get('description'))
                                        ->setAttrib('readonly', 'true');
        
    }

}
?>
