<?php
/**
 * Form to edit a Controller
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Controller
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Controller_Edit
 * @extends Admin_Form_Controller_Base
 */
class Admin_Form_Controller_Edit extends Admin_Form_Controller_Base
{
    /**
     * create the form to edit controller data
     *
     * @param Admin_Model_DbRow_Controller $controller
     * @access public
     */
    public function __construct(Admin_Model_DbRow_Controller $controller)
    {
        parent::__construct($controller);

        $this->addElement(new Zend_Form_Element_Hidden('id', array(
            'required'      => true,
            'value'         => $controller->get('id'),
            'order'         => 11
        )));
    }
}
?>
