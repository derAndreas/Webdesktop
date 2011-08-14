<?php
/**
 * Define an abstract representation of any element in the administration
 *
 * Attention: Because Zend_Db::FETCH_INTO is not supported
 *            do not use this class as $_rowClass in the TableAbstraction
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Model
 * @namespace App_Model_DbRow
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Model_DbRow_Abstract
 */
abstract class App_Model_DbRow_Abstract {

    /**
     * Constant for a row dummy
     * In the mapping it is possible to set JSCON columns, but they do not exist
     * as DB columns. Use the dummy.
     * @var int
     */
    CONST ROW_DUMMY = 0x01;

    /**
     * Maps the User Table to own defined keys
     * to abstract more from db schema
     *
     * @var array
     */
    protected $_transformColumnMap = array();

    /**
     * Define columns that should be looked for, when
     * it comes to DB actions with calling self::toDbArray()
     * @var array
     */
    protected $defaultDbColumns = array();
    /**
     * Define columns that should be looked for, when
     * it comes to Json actions with calling self::toJsonArray()
     * @var array
     */
    protected $defaultJsonColumns = array();


    /**
     * Construct the DbRow
     *
     * @param array $data (optional)
     */
    public function  __construct($data = array())
    {
        $this->fromArray($data);
    }

    /**
     * Set a key/value pair
     *
     * The value will only be set, if the $key is defined
     * in the self::_transformColumnMap.
     *
     * @param string $key
     * @param mixed $value
     * @return App_Model_DbRow_Abstract
     */
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

    /**
     * Get a value for a key
     *
     * The value will only be fetched for key's that are defined
     * in self::_transformColumnMap.
     *
     * @param string $key
     * @param mixed $defaultVal
     * @return mixed
     */
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

    /**
     * Set data from an array
     *
     * To set the data the rules defined at self::set() are applied
     *
     * @param array $data
     * @return App_Model_DbRow_Abstract
     */
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

    /**
     * Generate a an array with the dbColumn names as key, so that
     * it can be used in Zend_Db Operations
     *
     * If the DbQuery needs it, define an own filter, what should
     * be in the returning array. Default is to use the keys defined
     * in self::defaultDbColumns()
     *
     * @param array $filter
     * @return array
     */
    public function toDbArray($filter = NULL)
    {
        $result = array();
        $cols   = array_values($this->_transformColumnMap);
        $cust   = array_keys($this->_transformColumnMap);

        IF($filter === NULL) {
            $filter = $this->defaultDbColumns;
        }
        
        FOREACH($cust AS $c) {
            // check if the value was set and is not in the filtered parameter
            IF(isset($this->$c) && in_array($c, $filter, TRUE)) {
                $col = $this->_transformColumnMap[$c];
                $result[$col] = $this->$c;
            }
        }
        RETURN $result;
    }

    /**
     * Generate a an array with the self::_transformColumnMap index
     * names as key, so that it can be used with direct json encoding.
     *
     * @param array $filter
     * @return array
     */
    public function toJsonArray($filter = NULL)
    {
        $cols   = array_values($this->_transformColumnMap);
        $cust   = array_keys($this->_transformColumnMap);
        $result = array();

        IF($filter === NULL) {
            $filter = $this->defaultJsonColumns;
        }
        
        FOREACH($this->_transformColumnMap AS $key => $colName) {
            IF(isset($this->$key) && in_array($key, $filter, TRUE)) {
                $result[$key] = $this->$key;
            }
        }
        RETURN $result;
    }
}
?>
