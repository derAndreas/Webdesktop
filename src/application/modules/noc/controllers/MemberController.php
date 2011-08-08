<?php
/**
 * Member Controller for the NOC
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Noc
 * @namespace Noc
 * @see ExtJs4 <http://www.sencha.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3.0
 */

/**
 * @class Noc_MemberController
 * @extends Zend_Controller_Action
 */
class Noc_MemberController extends Zend_Controller_Action {

    /**
     * IndexAction to render a PHTML in the layout
     *
     * @todo check the ACL to which modules the user has access.
     *       currently the modules are staticly written in the HTML Template
     *       so that non admin users can click on the admin link, but will redirected
     *       to login
     */
    public function indexAction()
    {
        
    }


}
?>
