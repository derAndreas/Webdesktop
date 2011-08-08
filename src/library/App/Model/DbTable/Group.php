
<?php
/**
 * Description of user
 *
 * @author Andreas
 */
class App_Model_DbTable_Group extends Zend_Db_Table_Abstract {

    protected $_name = 'user_groups';
    protected $_dependentTables = array('App_Model_DbTable_User');
    protected $_referenceMap = array(
        'GroupsUser' => array(
            'columns'           => array('ug_id'),
            'refTableClass'     => 'App_Model_DbTable_User',
            'refColumns'        => array('uu_ug_id')
        )
    );
}
?>
