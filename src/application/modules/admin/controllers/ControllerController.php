<?php
/**
 * Class for handling controllers in the application
 *
 * Modify (Add/Edit/Delete) or set permissions for a controller
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @namespace Admin
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Admin_ControllerController
 * @extends Zend_Controller_Action
 */
class Admin_ControllerController extends Zend_Controller_Action {
    /**
     * The ModuleController DbModel
     *
     * @var Admin_Model_DbTable_Acl_ModuleController
     * @access protected
     */
    protected $dbCtrl;
    /**
     * The ModuleController Model
     *
     * @var Admin_Model_Acl_ControllersActions
     * @access protected
     */
    protected $ctrlModel;

    /**
     * initial method, load models
     *
     * @access public
     */
    public function init()
    {
        $this->ctrlModel = new Admin_Model_Acl_ControllersActions();
        $this->dbCtrl    = new Admin_Model_DbTable_Acl_ModuleController();
    }

    /**
     * List all known Controllers from the database
     *
     * @view views/scripts/controller/index.phtml
     * @access public
     */
    public function indexAction()
    {
        $controllers = array();

        FOREACH($this->dbCtrl->fetchAllOrderByModuleController() AS $row) {
            $controllers[] = new Admin_Model_DbRow_Controller($row);
        }

        $this->view->controllers = $controllers;
    }

    /**
     * Scan controller directories for updates
     *
     * @view views/scripts/controller/scan.phtml
     * @access public
     */
    public function scanAction()
    {
        $all = array();

        FOREACH($this->dbCtrl->fetchAll() AS $row) {
            $all[] = new Admin_Model_DbRow_Controller($row);
        }

        $scanned  = $this->ctrlModel->getControllers();
        $new      = $this->dbCtrl->filterExistingControllers($scanned);
        $vanished = $this->ctrlModel->filterVanishedControllers($all, $scanned);
        
        $this->view->vanished = $vanished;
        $this->view->new      = $new;
    }
    /**
     * Delete an Controller from the database, which does not exists
     * as an controller file anymore.
     * Delete also from all releveant tables like
     *  - actions
     *  - rules
     *
     * @view views/scripts/controller/delete.phtml
     * @access public
     */
    public function deleteAction()
    {
        $ctrlRow = new Admin_Model_DbRow_Controller($this->dbCtrl->find($this->checkControllerIdParam()));
        $form      = new Admin_Form_Controller_Delete($ctrlRow);
        IF($ctrlRow->get('id')) {
            IF($this->getRequest()->isPost()) {
                IF($form->isValid($this->getRequest()->getParams()) === TRUE
                && $form->getElement('chkdelete')->isChecked() === TRUE) {
                    // delete the model controller
                    $this->dbCtrl->deleteById($ctrlRow->get('id'));
                    // delete all actions for this module / controller
                    $actionDbModel = new Admin_Model_DbTable_Acl_Action;
                    $actionDbModel->deleteByControllerId($ctrlRow->get('id'));
                    // delete all rules which are bound to this module / controller
                    $rulesDbModel = new Admin_Model_DbTable_Acl_Rule;
                    $rulesDbModel->deleteByControllerId($ctrlRow->get('id'));

                    $this->_redirect('admin/controller/scan');
                } ELSE {
                    $form->addErrors(array(
                            'Delete not successfull. Did you checked the checkbox?'
                    ));
                }
            }
        } ELSE {
            $form->addErrors(array('Invalid Controller Id, cannot proceed'));
        }

        $this->view->form = $form;
    }

    /**
     * Add a new controller to the database (from scanning section)
     *
     * @view views/scripts/controller/add.phtml
     * @access public
     * @todo add validation on parameters
     */
    public function addAction()
    {
        $module     = $this->getRequest()->getParam('modul');
        $controller = $this->getRequest()->getParam('control');
        $virtual    = $this->getRequest()->getParam('virtual', 0);
        $form       = new Admin_Form_Controller_Add($module, $controller);

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $row = new Admin_Model_DbRow_Controller(array(
                    'moduleName'     => $this->getRequest()->getParam('modul', ''),
                    'controllerName' => $this->getRequest()->getParam('control', ''),
                    'enabled'        => 0,
                    'virtual'        => $virtual,
                    'description'    => $this->getRequest()->getParam('description', '')
                ));
                $this->dbCtrl->insert($row->toDbArray(array('moduleName', 'controllerName', 'enabled', 'virtual', 'description')));

                $this->_redirect('admin/controller/scan');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Edit an Controller in the database
     *
     * @view views/scripts/controller/edit.phtml
     * @access public
     * @todo remove the possibility to change the virtual status,
     *       this could causes in other problems, when working with controllers that
     *       are marked as virtual!
     */
    public function editAction()
    {
        $ctrlRow = new Admin_Model_DbRow_Controller($this->dbCtrl->find($this->checkControllerIdParam()));
        $form = new Admin_Form_Controller_Edit($ctrlRow);

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $ctrlRow->fromArray(array(
                    'description' => $this->getRequest()->getParam('description')
                ));
                $this->dbCtrl->update($ctrlRow->toDbArray(array('description')), $ctrlRow->get('id'));
                
                $this->_redirect('admin/controller/index');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Change the Status of a controller (enabled/disabled)
     *
     * @view views/scripts/controller/status.phtml
     * @access public
     */
    public function statusAction()
    {
        $ctrlRow = new Admin_Model_DbRow_Controller($this->dbCtrl->find($this->checkControllerIdParam()));
        $ctrlRow->set('enabled', $ctrlRow->get('enabled') == 1 ? 0 : 1);
        $this->dbCtrl->update($ctrlRow->toDbArray(array('enabled')), $ctrlRow->get('id'));

        // disabled all actions too, they are relevant in the ACL
        IF($ctrlRow->get('enabled') === 0) {
            $actionRow = new Admin_Model_DbRow_Action(array(
                'enabled' => 0
            ));
            $actionDbModel = new Admin_Model_DbTable_Acl_Action;
            $actionDbModel->updateWithControllerId($actionRow->toDbArray(array('enabled')), $ctrlRow->get('id'));
        }

        $this->_redirect('admin/controller/index');
        
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
        $id = $this->getRequest()->getParam('id');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbCtrl->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }
}
?>
