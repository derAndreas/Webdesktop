<?php
/**
 * Class for handling groups in the application
 *
 * Modify (Add/Edit/Delete) groups
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @namespace Admin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_GroupController
 * @extends Zend_Controller_Action
 */
class Admin_GroupController extends Zend_Controller_Action {
    /**
     * Contains the DbTable Model for groups
     *
     * @var Admin_Model_DbTable_Groups
     * @access protected
     */
    protected $dbGroups;

    /**
     * initial method, load models
     *
     * @access public
     */
    public function init()
    {
        $this->dbGroups = new Admin_Model_DbTable_Groups();
    }

    /**
     * List all available groups
     *
     * @view views/scripts/group/index.phtml
     * @access public
     */
    public function indexAction()
    {
        $groups = array();

        FOREACH($this->dbGroups->fetchAll() AS $row) {
            $groups[] = new Admin_Model_DbRow_Group($row);
        }

        $this->view->groups = $groups;
    }

    /**
     * Add a group
     *
     * @view views/scripts/group/add.phtml
     * @access public
     */
    public function addAction()
    {
        $form = new Admin_Form_Group_Add();
        $form->setAction('/noc/admin/group/add');

        IF($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $this->dbGroups->insert($this->getRequest()->getParam('name'), $this->getRequest()->getParam('description'));
            $this->_redirect('admin/group/index');

        } ELSEIF($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams()) === FALSE) {
            $form->setDescription($form->getErrorMessages());
        }

        $this->view->form = $form;
    }


    /**
     * Edit a group
     *
     * @access public
     */
    public function editAction()
    {
        $groupRow = new Admin_Model_DbRow_Group($this->dbGroups->find($this->checkGroupIdParam()));
        
        $form = new Admin_Form_Group_Edit($groupRow);
        $form->setAction('/noc/admin/group/edit');

        IF($this->getRequest()->isPost()) {
            IF($form->isValid($this->getRequest()->getParams())) {
                $groupRow->fromArray(array(
                    'name'        => $this->getRequest()->getParam('name'),
                    'description' => $this->getRequest()->getParam('description')
                ));

                $this->dbGroups->update($groupRow->toDbArray(), $groupRow->get('id'));

                $this->_redirect('admin/group/index');

            } ELSE {
                $form->setDescription('An error occured');
            }
        }

        $this->view->form = $form;
    }


    /**
     * Delete a Group
     * 
     * @access public
     * @todo FIXME: somehow the App_Form_Delete Form is gone, not working ATM!
     */
    public function deleteAction()
    {
        $groupRow = new Admin_Model_DbRow_Group($this->dbGroups->find($this->checkGroupIdParam()));
        $linkedUsers = $this->dbGroups->fetchUsersAssignedToGroup($groupRow->get('id'));

        $form = new App_Form_Delete($groupRow);  //FIXME: File is gone, rewrite

        IF($linkedUsers->count() > 0) {
            $this->view->message = 'There are users linked to this group. Cannot delete!';
            $this->renderScript('error/deletenotpossible.phtml');
        } ELSE {
            IF($this->getRequest()->isPost()) {
                IF($form->isValid($this->getRequest()->getParams()) && $form->getElement('del_checkbox')->isChecked() === TRUE) {
                    $this->dbGroups->deleteById($groupRow->get('id'));
                    $this->_redirect('admin/group/index');
                } ELSE {
                    $form->setDescription('Failed to delete group. Unknown error occured.');
                }
            }
        }

        $this->view->form  = $form;
        $this->view->group = $groupRow;
    }

    /**
     * check if a valid group Id is given. if not throw an exception
     *
     * @access private
     * @throws Exception if no valid id is given
     * @return integer the Id
     */
    private function checkGroupIdParam()
    {
        $id = $this->getRequest()->getParam('id');

        IF($id === NULL || is_numeric($id) === FALSE || $this->dbGroups->find($id)->count() === 0) {
            throw new Admin_Model_Acl_Exception('Invalid or no Id Parameter given');
        }

        RETURN (int) $id;
    }
}
?>
