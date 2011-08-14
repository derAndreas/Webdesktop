<?php
/**
 * Base Form for all action forms
 *
 * Reduces LOC and maintain complexity
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
 * @class Admin_Form_Action_Base
 * @extends Admin_Form_Base
 */
class Admin_Form_Action_Base extends Admin_Form_Base
{
    /**
     *
     * @param Admin_Model_DbRow_Controller $controller
     */
    public function  __construct(Admin_Model_DbRow_Controller $controller)
    {
        $this->addElements(array(
            new Zend_Form_Element_Text('module', array(
                'ignore'    => true,
                'label'     => 'Module:',
                'attribs'   => array(
                    'class'     => 'text span-4',
                    'readonly'  => 'true'
                ),
                'value'     => $controller->get('moduleName'),
                'order'     => 1
            )),

            new Zend_Form_Element_Text('controller', array(
                'ignore'    => true,
                'label'     => 'Controller:',
                'attribs'   => array(
                    'class'     => 'text span-4',
                    'readonly'  => 'true'
                ),
                'value'     => $controller->get('controllerName'),
                'order'     => 2
            )),

            new Zend_Form_Element_Text('action', array(
                'ignore'    => true,
                'label'     => 'Action:',
                'attribs'   => array(
                    'class'     => 'text span-4',
                    'readonly'  => 'true'
                ),
                'value'     => '',
                'order'     => 3
            )),

            new Zend_Form_Element_Textarea('description', array(
                'label'     => 'Description:',
                'filters'   => array('StripTags'),
                'attribs'   => array(
                    'class'     => 'text span-6',
                ),
                'order'     => 4
            )),

            new Zend_Form_Element_Submit('saveBtn', array(
                'label'     => 'Save',
                'order'     => 10
            ))
        ));
        
        parent::__construct();
    }
}
?>
