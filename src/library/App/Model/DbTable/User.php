<?php
/**
 * Description of user
 *
 * @author Godfather
 */
class App_Model_DbTable_User extends Zend_Db_Table_Abstract {

    protected $_name = 'user_users';
    protected $_primaray = 'uu_id';
    protected $_dependentTables = array('App_Model_DbTable_Group');
    protected $_referenceMap = array(
        'UserGroups' => array(
            'columns'           => array('uu_ug_id'),
            'refTableClass'     => 'App_Model_DbTable_Group',
            'refColumns'        => array('ug_id')
        )
    );


    /**
     * get the role id and name, where the user is member
     *
     * @param int $userId
     * @param int $groupId
     * @return array
     * @access public
     */
    public function getRoleBinding($userId, $groupId)
    {
        $stmt = 'SELECT
                    uar_id,
                    uar_name
                 FROM
                    user_acl_role_member
                 LEFT JOIN user_acl_roles ON uarm_uar_id = uar_id
                 WHERE ((
                    uarm_type = "user"
                    AND
                    uarm_member_id = '.$userId.'
                 )
                 OR (
                    uarm_type = "group"
                    AND
                    uarm_member_id = '.$groupId.'
                 ))
                 GROUP BY uar_id

        ';

        RETURN $this->getAdapter()->fetchAll($stmt);
    }

    public function findUsersByName($name, $start = 0, $limit = 10)
    {
        $query = strtolower($name . '%');
        $select = $this->getAdapter()->select()
                  ->from($this->_name, array('uu_id AS id', 'uu_name AS uname', 'uu_email AS mail'))
                  ->joinLeft('user_groups', 'uu_ug_id = ug_id', array('ug_name AS gname'))
                    ->where($this->getAdapter()->quoteInto('LOWER(uu_name) LIKE ?', $query))
                  ->order('uu_name ASC');
        RETURN $this->getAdapter()->fetchAll($select);
    }

}
?>
