<?php
/**
 * Database Model for the Table "user_launchers" containing User Launchers
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Webdesktop
 * @subpackage Model_DbTable
 * @namespace Webdesktop_Model_DbTable
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Webdesktop_Model_DbTable_Launchers
 * @extends Zend_Db_Table_Abstract
 */
class Webdesktop_Model_DbTable_Launchers extends Zend_Db_Table_Abstract {
    
    /**
     * Constant for Autorun modules
     * The value is also used in the Db in the type column
     *
     * @var int
     */
    const LAUNCHER_AUTORUN    = 1;
    /**
     * Constant for Context Menu
     * The value is also used in the Db in the type column
     *
     * @var int
     * @deprecated dont use that, context is not needed
     */
    const LAUNCHER_CONTEXT    = 2;
    /**
     * Constant for Quickstart
     * The value is also used in the Db in the type column
     *
     * @var int
     */
    const LAUNCHER_QUICKSTART = 3;
    /**
     * Constant for Shortcuts
     * The value is also used in the Db in the type column
     *
     * @var int
     */
    const LAUNCHER_SHORTCUT   = 4;

    /**
     * primary key
     *
     * @var string
     */
    protected $_primary = 'l_id';
    /**
     * Table name
     *
     * @var String
     */
    protected $_name    = 'user_launchers';

    /**
     * Find a module launchers by the module Id. 
     *
     * @param int $moduleId
     * @return Zend_Db_Table_Rowset_Abstract
     * @access public
     */
    public function findModuleById($moduleId)
    {
        $select = $this->getAdapter()
                       ->select()
                       ->from($this->_name)
                       ->joinLeft('modules', 'm_id = l_m_id', array('m_moduleId'))
                       ->where('m_moduleId = ?', $moduleId);

        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Find module launchers by the user id
     *
     * @param int $userId
     * @return Zend_Db_Table_Row_Abstract
     * @access public
     */
    public function findByUserId($userId)
    {
        $select = $this->getAdapter()
                       ->select()
                       ->from($this->_name)
                       ->joinLeft('modules', 'm_id = l_m_id', array('m_moduleId'))
                       ->where('l_u_id = ?', $userId);
        RETURN $this->getAdapter()->fetchAll($select);
    }

    /**
     * Delete all launchers of $type for a specific user
     *
     * @param int $userId
     * @param int $type  (see constants defined in Webdesktop_Model_DbTable_Launchers)
     * @return int number of affected rows
     */
    public function deleteByUserType($userId, $type)
    {
        RETURN parent::delete(array(
            $this->getAdapter()->quoteInto('l_u_id = ?', $userId, Zend_Db::INT_TYPE),
            $this->getAdapter()->quoteInto('l_type = ?', $type)
        ));
    }

    /**
     * Add a new launcher for a module and a specific user to the db
     * 
     * @param int $moduleId
     * @param int $userId
     * @param int $type (see constants defined in Webdesktop_Model_DbTable_Launchers)
     * @return int PK if of inserted row
     */
    public function insertLauncher($moduleId, $userId, $type)
    {
        RETURN parent::insert(array(
            'l_m_id' => $moduleId,
            'l_u_id' => $userId,
            'l_type' => $type
        ));
    }
}
?>
