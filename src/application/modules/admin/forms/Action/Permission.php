<?php
/**
 * Form to change the User permissions in the ACL
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
 * @class Admin_Form_Action_Permission
 * @extends Zend_Form
 * @todo create a base form for all forms to reduce the LOC, like decorators and common fields
 *       every form should extend from this new form then
 */
class Admin_Form_Action_Permission extends Zend_Form {

    /**
     * create the form to change permission
     *
     * in this form we can set the acl roles, which are allowed or denied
     * for this action
     *
     * @param Admin_Model_DbRow_Controller $ctrlRow
     * @param Admin_Model_DbRow_Action $actionRow
     * @param array $roles array of Admin_Model_DbTable_Acl_Role Objects
     * @param array $rulesAllow
     * @param array $rulesDeny
     */
    public function __construct(Admin_Model_DbRow_Controller $ctrlRow,
                                Admin_Model_DbRow_Action $actionRow,
                                array $roles,
                                array $rulesAllow,
                                array $rulesDeny
                                )
    {
        $module = new Zend_Form_Element_Text('module');
        $module->setLabel('Module: ')
               ->setIgnore(TRUE)
               ->setAttribs(array(
                   'class' => 'text span-4',
                   'readonly' => 'true'
               ))
               ->setValue($ctrlRow->get('moduleName'));

        $controller = new Zend_Form_Element_Text('controller');
        $controller->setLabel('Controller: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($ctrlRow->get('controllerName'));

        $action = new Zend_Form_Element_Text('action');
        $action->setLabel('Action: ')
                   ->setIgnore(TRUE)
                   ->setAttribs(array(
                       'class' => 'text span-4',
                       'readonly' => 'true'
                   ))
                   ->setValue($actionRow->get('actionName'));


        $rolesAllow = new Zend_Form_Element_MultiCheckbox('rolesallow');
        $rolesAllow->setLabel('Allow access');
        $rolesDeny = new Zend_Form_Element_MultiCheckbox('rolesdeny');
        $rolesDeny->setLabel('Explicit Deny Access');

        FOREACH($roles AS $role) {
            $rolesAllow->addMultiOption($role->get('id'), $role->get('name'));
            $rolesDeny->addMultiOption($role->get('id'), $role->get('name'));
        }
        
        $rolesAllow->setValue($rulesAllow);
        $rolesDeny->setValue($rulesDeny);


        $submit = new Zend_Form_Element_Submit('save');
        $submit->setLabel('save');

        $hidden = new Zend_Form_Element_Hidden('id');
        $hidden->setValue($actionRow->get('id'))
               ->setRequired(TRUE);

        $this->addElements(array($module, $controller, $action, $rolesAllow, $rolesDeny, $submit, $hidden));

        $this->setDecorators(array(
            'FormElements',
            array('errors', array('class' => 'error', 'placement' => 'prepend')),
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
            'Form'
        ));
    }

    /**
     *
     * @return
     * @access public
     */
    public function hasPermissionCollision(Zend_Controller_Request_Abstract $request)
    {
        $allow = (array) $this->getElement('rolesallow')->getValue();
        $deny = (array) $this->getElement('rolesdeny')->getValue();
        FOREACH($allow AS $a) {
            IF(in_array($a, $deny)) {
                RETURN TRUE;
            }
        }
        RETURN FALSE;
    }

}
?>
