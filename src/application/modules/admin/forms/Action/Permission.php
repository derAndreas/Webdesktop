<?php
/**
 * Form to change the permissions for an action
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
 * @extends Admin_Form_Action_Base
 */
class Admin_Form_Action_Permission extends Admin_Form_Action_Base
{

    /**
     * create the form to change permission
     *
     * in this form we can set the acl roles, which are allowed or denied
     * for this action
     *
     * @param Admin_Model_DbRow_Controller $controller
     * @param Admin_Model_DbRow_Action $action
     * @param array $roles array of Admin_Model_DbTable_Acl_Role Objects
     * @param array $rulesAllow
     * @param array $rulesDeny
     */
    public function __construct(Admin_Model_DbRow_Controller $controller,
                                Admin_Model_DbRow_Action $action,
                                array $roles,
                                array $rulesAllow,
                                array $rulesDeny
                                )
    {

        parent::__construct($controller);

        $rolesAllow = new Zend_Form_Element_MultiCheckbox('rolesallow', array(
            'label'     => 'Allow access',
            'order'     => 7
        ));
        $rolesDeny = new Zend_Form_Element_MultiCheckbox('rolesdeny', array(
            'label'     => 'Explicit Deny Access',
            'order'     => 8
        ));

        FOREACH($roles AS $role) {
            $rolesAllow->addMultiOption($role->get('id'), $role->get('name'));
            $rolesDeny->addMultiOption($role->get('id'), $role->get('name'));
        }
        
        $rolesAllow->setValue($rulesAllow);
        $rolesDeny->setValue($rulesDeny);

        $this->addElements(array(
            $rolesAllow,
            $rolesDeny,
            new Zend_Form_Element_Hidden('id', array(
                'required'  => true,
                'value'     => $action->get('id'),
                'order'     => 11
            ))
        ));

        // remove description element (from base form)
        $this->removeElement('description');
        $this->getElement('action')->setValue($action->get('actionName'));

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
