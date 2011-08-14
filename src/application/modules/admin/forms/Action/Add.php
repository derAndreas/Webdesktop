<?php
/**
 * Form to add an action
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
 * @class Admin_Form_Action_Add
 * @extends Zend_Form
 */
class Admin_Form_Action_Add extends Admin_Form_Action_Base {
    /**
     * Create the add form
     *
     * @param Admin_Model_DbRow_Controller $controller
     * @param String $actionName
     */
    public function __construct(Admin_Model_DbRow_Controller $controller, $actionName)
    {
        parent::__construct($controller);
        $this->getElement('actionname')->setValue($actionName);
    }

}
?>
