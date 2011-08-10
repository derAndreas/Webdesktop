<?php
/**
 * Index Controller for the virtual Desktop
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @namespace Webdesktop
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_IndexController
 * @extends Zend_Controller_Action
 */
class Webdesktop_IndexController extends Zend_Controller_Action
{
    /**
     * @var Webdesktop_Model_Webdesktop
     */
    protected $model;
    /**
     * Init the the Controller
     *
     * Setup the Webdesktop Model for usage in indexAction
     */
    public function init()
    {
        $user   = Zend_Auth::getInstance()->getIdentity();
        $config = new Zend_Config_Ini(Zend_Registry::get('appConfigPath') . 'webdesktop.ini');

        $this->model  = new Webdesktop_Model_Webdesktop($user, $config);
    }
    /**
     * IndexAction to load the user webdesktop
     *
     * Gather UserWebdesktop informations and push them into the View, so
     * that the Desktop loads with user settings.
     *
     * @access public
     */
    public function indexAction()
    {
        $this->view->userConfig = $this->model->getInitConfig();
    }



}

