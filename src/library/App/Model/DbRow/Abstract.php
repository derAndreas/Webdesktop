<?php
/**
 * Define an abstract representation of any element in the administration
 *
 * Attention: Because Zend_Db::FETCH_INTO is not supported
 *            do not use this class as $_rowClass in the TableAbstraction
 *
 * @author Andreas
 */
class App_Model_DbRow_Abstract {

    /**
     * Maps the User Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array();

    protected $defaultDbColumns = array();
    protected $defaultJsonColumns = array();


    public function  __construct($data = array())
    {
        $this->fromArray($data);
    }

    public function set($key, $value)
    {
        IF(array_key_exists($key, $this->_transformColumnMap)) {
            $this->$key = $value;
        }
        IF(in_array($key, $this->_transformColumnMap, TRUE)) {
            $col = array_search($key, $this->_transformColumnMap);
            $this->$col = $value;
        }
        RETURN $this;
    }

    public function get($key, $defaultVal = NULL)
    {
        IF(array_key_exists($key, $this->_transformColumnMap)) {
            RETURN $this->$key;
        }
        IF(in_array($key, $this->_transformColumnMap, TRUE)) {
            $col = array_search($key, $this->_transformColumnMap, TRUE);
            $col = $col[0];
            RETURN $this->$col;
        }

        RETURN $defaultVal;
    }

    public function fromArray($data)
    {
        IF($data instanceof  Zend_Db_Table_Rowset_Abstract && $data->current()) {
            $data = $data->current()->toArray();
        }
        IF($data instanceof Zend_Db_Table_Row) {
            $data = $data->toArray();
        }
        IF(is_array($data)) {
            FOREACH($data AS $key => $value) {
                $this->set($key, $value);
            }
        }
        RETURN $this;
    }

    public function toDbArray($filter = NULL)
    {
        IF($filter === NULL) {
            $filter = $this->defaultDbColumns;
        }
        $cols = array_values($this->_transformColumnMap);
        $cust = array_keys($this->_transformColumnMap);
        $result = array();
        FOREACH($cust AS $c) {
            // check if the value was set and is not in the filtered parameter
            IF(isset($this->$c) && in_array($c, $filter, TRUE)) {
                $col = $this->_transformColumnMap[$c];
                $result[$col] = $this->$c;
            }
        }
        RETURN $result;
    }

    public function toJsonArray($columns = NULL)
    {
        IF($columns === NULL) {
            $filter = $this->defaultJsonColumns;
        }
        $cols = array_values($this->_transformColumnMap);
        $cust = array_keys($this->_transformColumnMap);
        $result = array();
        FOREACH($this->_transformColumnMap AS $key => $colName) {
            IF(isset($this->$key) && in_array($key, $filter, TRUE)) {
                $result[$key] = $this->$key;
            }
        }
        RETURN $result;
    }
}
?>
