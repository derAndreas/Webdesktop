<?php
/**
 * Form to edit an action
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
 * @class Admin_Form_Action_Edit
 * @extends Admin_Form_Action_Base
 */
class Admin_Form_Action_Edit extends Admin_Form_Action_Base {

    /**
     * Setup the edit action form
     *
     * @param Admin_Model_DbRow_Action $action
     * @param Admin_Model_DbRow_Controller $controller
     */
    public function __construct(Admin_Model_DbRow_Controller $controller, Admin_Model_DbRow_Action $action)
    {
        parent::__construct($controller);

        $this->addElement(new Zend_Form_Element_Hidden('id', array(
            'required'  => true,
            'value'     => $action->get('id'),
            'order'     => 11
        )));

        $this->getElement('action')->setValue($action->get('actionName'));
        $this->getElement('description')->setValue($action->get('description'));
    }

}
?>
