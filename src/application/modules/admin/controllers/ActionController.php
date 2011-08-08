<?php
/**
 * Class for handling actions in the application
 *
 * Modify (Add/Edit/Delete) or set permissions for an action.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @namespace Admin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_ActionController
 * @extends Zend_Controller_Action
 */
class Admin_ActionController extends Zend_Controller_Action {
    /**
     * The module-controller DbModel
     *
     * @var Admin_Model_DbTable_Acl_ModuleController
     * @access protected
     */
    protected $dbController;
    /**
     * The module-controller Model
     *
     * @var Admin_Model_Acl_ControllersActions
     * @access protected
     */
    protected $ctrlActionModel;
    /**
     * The action DbModel
     *
     * @var Admin_Model_DbTable_Acl_Action
     * @access protected
     */
    protected $dbAction;

    /**
     * initial method, load models
     *
     * @access public
     */
    public function init()
    {
        $this->ctrlActionModel = new Admin_Model_Acl_ControllersActions();
        $this->dbController    = new Admin_Model_DbTable_Acl_ModuleController();
        $this->dbAction        = new Admin_Model_DbTable_Acl_Action();
    }

    /**
     * List all known actions from a module/controller
     *
     * Controller Id must be given via request paramenter "control"
     *
     * @view views/scripts/action/action/index.phtml
     * @access public
     */
    public function indexAction()
    {
        $ctrl = $this->dbController->find($this->checkControllerIdParam());
        $vCtrl = FALSE;
        $vAction = array();

        IF($ctrl->count() === 1) {
            $ctrlRow = new Admin_Model_DbRow_Controller($ctrl->current());

            FOREACH($this->dbAction->findActionByControllerId($ctrlRow->get('id')) AS $row) {
                $vAction[] = new Admin_Model_DbRow_Action($row);
            }

            $vCtrl = $ctrlRow;
        }
        
        $this->view->controller = $vCtrl;
        $this->view->actions    = $vAction;
    }

    /**
     * Scan for new or vanished actions in a controller
     *
     * Controller Id must be given via request paramenter "control"
     *
     * @view views/scripts/action/scan.phtml
     * @access public
     */
    public function scanAction()
    {
        $ctrl    = $this->dbController->find($this->checkControllerIdParam());
        $vCtrl   = new Admin_Model_DbRow_Controller();
        $all     = array();
        $vVanish = array();
        $vNew    = array();

        IF($ctrl->count() === 1) {
            $ctrlRow = $vCtrl->fromArray($ctrl->current());

            FOREACH($this->dbAction->findActionByControllerId($ctrlRow->get('id')) AS $row) {
                $all[] = new Admin_Model_DbRow_Action($row);
            }

            $scanned  = $this->ctrlActionModel->getActions(
                            $ctrlRow->get('moduleName'),
                            $ctrlRow->get('controllerName'),
                            $ctrlRow->get('virtual'));

            $vNew    = $this->dbAction->filterExistingActions($ctrlRow->get('id'), $scanned);
            $vVanish = $this->ctrlActionModel->filterVanishedActions($all, $scanned);
        }

        $this->view->controller = $vCtrl;
        $this->view->vanished   = $vVanish;
        $this->view->new        = $vNew;
    }

    /**
     * Add an action from the module/controller to action DbTable
     * Forwards to the edit action method 
     *
     * @view views/scripts/action/add.phtml
     * @access public
     * @todo Add Exception handling / error notifications to user
     */
    public function addAction()
    {
        $ctrl   = $this->dbController->find($this->checkControllerIdParam());
        $action = $this->getRequest()->getParam('actionname', NULL);

        IF($action === NULL || $ctrl->count() !== 1) {
            throw new Admin_Model_Acl_Exception('Invalid or no ActionName/ControllerId Parameter given');
        }

        $ctrl = new Admin_Model_DbRow_Controller($ctrl->current());
        $form       = new Admin_Form_Action_Add($ctrl, $action);

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $actionRow = new Admin_Model_DbRow_Action(array(
                    'mcId'        => $ctrl->get('id'),
                    'actionName'  => $this->getRequest()->getParam('actionname'),
                    'enabled'     => 0,
                    'description' => $this->getRequest()->getParam('description', '')
                ));

                $this->dbAction->insert($actionRow->toDbArray());
                $this->_redirect('admin/action/scan/control/' . $ctrl->get('id'));

            } ELSE {
                $form->addError('Exexpected error occured');
            }
        }

        $this->view->form       = $form;
        $this->view->controller = $ctrl;
    }

    /**
     * Delete an Action from the database, which does not exists as an action method anymore
     *
     * @view views/scripts/action/delete.phtml
     * @access public
     */
    public function deleteAction()
    {
        $actionRow  = new Admin_Model_DbRow_Action();
        $ctrlRow    = new Admin_Model_DbRow_Controller();
        $action     = $this->dbAction->find($this->checkActionIdParam());

        IF($action->count() !== 1) {
            $form  = new Admin_Form_Action_Delete($actionRow, $ctrlRow);
            $form->setErrors(array('Invalid ActionId, cannot proceed!'));
        } ELSE {
            $actionRow->fromArray($action);
            $ctrlRow->fromArray($this->dbController->find($actionRow->get('mcId')));
            $form  = new Admin_Form_Action_Delete($actionRow, $ctrlRow);

            IF($this->getRequest()->isPost()) {
                IF($form->isValid($this->getRequest()->getParams()) === TRUE
                && $form->getElement('chkdelete')->isChecked() === TRUE) {
                    $this->dbAction->deleteById($actionRow->get('id'));
                    // delete all rules which are bound to this module / controller
                    $dbRules = new Admin_Model_DbTable_Acl_Rule();
                    $dbRules->deleteByActionId($actionRow->get('id'));

                    $this->_redirect('admin/action/scan');
                } ELSE {
                    $form->setErrors(array(
                            'Delete not successfull. Did you checked the checkbox?'
                    ));
                }
            }
        }

        $this->view->form       = $form;
        $this->view->controller = $ctrlRow;
    }


    /**
     * change the status of an action (enable/disable)
     *
     * @view no view script
     * @access public
     * @todo error handling
     */
    public function statusAction()
    {
        $actionRow = new Admin_Model_DbRow_Action($this->dbAction->find($this->checkActionIdParam()));
        IF($actionRow->get('id')) {
            $actionRow->fromArray(array(
                'enabled' => $actionRow->get('enabled') == 0 ? 1 : 0
            ));
            $this->dbAction->update($actionRow->toDbArray(array('enabled')), $actionRow->get('id'));
        } ELSE {
            // FIXME: Error Handling
        }

        $this->_redirect('admin/action/index/control/' . $actionRow->get('mcId'));
    }

    /**
     * Edit an Action
     *
     * @view views/scripts/action/delete.phtml
     * @access public
     */
    public function editAction()
    {
        $actionRow = new Admin_Model_DbRow_Action();
        $ctrlRow   = new Admin_Model_DbRow_Controller();
        $action    = $this->dbAction->find($this->checkActionIdParam());

        IF($action->count() !== 1) {
            $form  = new Admin_Form_Action_Delete($actionRow, $ctrlRow);
            $form->setErrors(array('Invalid ActionId, cannot proceed!'));
        } ELSE {
            $actionRow->fromArray($action);
            $ctrlRow->fromArray($this->dbController->find($actionRow->get('mcId')));
            $form = new Admin_Form_Action_Edit($actionRow, $ctrlRow);

            IF($this->getRequest()->isPost()) {
                IF($form->isValid($this->getRequest()->getParams())) {
                    $actionRow->set('description', $this->getRequest()->getParam('description'));
                    $this->dbAction->update(
                        $actionRow->toDbArray(array('description')),
                        $actionRow->get('id')
                    );
                    $this->_redirect('admin/action/index/control/' . $ctrlRow->get('id'));
                }
            }
        }

        $this->view->form       = $form;
        $this->view->controller = $ctrlRow;
    }

    /**
     * change to permission for this action
     *
     * @view views/scripts/action/permission.phtml
     * @access public
     */
    public function permissionAction()
    {
        $actionRow  = new Admin_Model_DbRow_Action($this->dbAction->find($this->checkActionIdParam()));
        $ctrlRow    = new Admin_Model_DbRow_Controller($this->dbController->find($actionRow->get('mcId')));
        $dbRoles    = new Admin_Model_DbTable_Acl_Role();
        $dbRules    = new Admin_Model_DbTable_Acl_Rule();
        $roles      = array();
        $rules      = array();
        $allowRules = array();
        $denyRules  = array();

        FOREACH($dbRoles->fetchActiveRoles() AS $row) {
            $roles[] = new Admin_Model_DbRow_Role($row);
        }

        FOREACH($dbRules->fetchRulesForAction($actionRow->get('id')) AS $row) {
            $rules[] = new Admin_Model_DbRow_Permission($row);
        }

        FOREACH($rules AS $rule) {
            IF($rule->get('rule') === Admin_Model_DbTable_Acl_Rule::RULE_DB_ALLOW) {
                $allowRules[] = $rule->get('roleId');
            } ELSEIF($rule->get('rule') ===  Admin_Model_DbTable_Acl_Rule::RULE_DB_DENY) {
                $denyRules[]  = $rule->get('roleId');
            }
        }

        $form = new Admin_Form_Action_Permission($ctrlRow, $actionRow, $roles, $allowRules, $denyRules);

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams()) && $form->hasPermissionCollision($this->getRequest()) === FALSE) {

                $dbRules->deleteByActionId($actionRow->get('id'));

                $allow = (array) $form->getElement('rolesallow')->getValue();
                $deny  = (array) $form->getElement('rolesdeny')->getValue();

                FOREACH($allow AS $roleId) {
                    $dbRules->addRule($ctrlRow->get('id'), $actionRow->get('id'), $roleId, Admin_Model_DbTable_Acl_Rule::RULE_DB_ALLOW);
                }

                FOREACH($deny AS $roleId) {
                    $dbRules->addRule($ctrlRow->get('id'), $actionRow->get('id'), $roleId, Admin_Model_DbTable_Acl_Rule::RULE_DB_DENY);
                }
                $this->_redirect(sprintf('admin/action/index/control/%d/id/%d', $ctrlRow->get('id'), $actionRow->get('id')));
            } ELSE {
                $form->addError('Mindestens eine Rolle wurde der Zugriff erlaubt und verweigert.');
            }
        }

        $this->view->form       = $form;
        $this->view->controller = $ctrlRow;
    }


    /**
     * check if a valid action Id is given. if not throw an exception
     *
     * @access private
     * @throws Exception if no valid id is given
     * @return int the Id
     */
    private function checkActionIdParam()
    {
        $id = $this->getRequest()->getParam('id');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbAction->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }

    /**
     * check if a valid controller Id is given. if not throw an exception
     *
     * @access private
     * @throws Exception if no valid id is given
     * @return integer the Id
     */
    private function checkControllerIdParam()
    {
        $id = $this->getRequest()->getParam('control');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbController->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }
}
?>
