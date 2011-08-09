<?php
/**
 * Simpel validation class to validate requests (GET/POST) against a
 * validator map. The validators are all Zend default validators.
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Plugin
 * @namespace App_Plugin
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Validate_Ajax
 * @todo rewrite: it should be possible to setup the validator map through constructor
 *       This means that all current usages needs to be rewritten
 */
class App_Validate_Ajax {

    /**
     * array of all Zend_Validator_* instances
     *
     * @var array of Zend_Validator_* Instances
     */
    private $validators = array();
    /**
     * Instance for the PluginLoader to load the Zend_Validate_* classes
     *
     * @var Zend_Loader_PluginLoader
     */
    private $pluginLoader;
    /**
     * Array with the error messages of failed validations
     *
     * @var array
     */
    private $messages;

    /**
     * Constructor to init the pluginLoader
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader(array('Zend_Validate_' => 'Zend/Validate/'));
    }

    /**
     * Validate the $params, which are defined in $vmap
     *
     * The map looks somthing like this:
     *
     * $vmap = array(
     *      'paramKey' => array(
     *          'validators' => array(
     *              'Int',
     *              array('Between' => array(0, 100)),
     *              array('inArray' => array(array('center', 'tile')))
     *          ),
     *          'message' => 'this is the error message'
     *      )
     * );
     *
     * So, for each element of POST|GET that is to check, add an array entry,
     * with the index name of the POST|GET index.
     * Two elements in this array are needed: validators, message
     * Validators is an array, with all Zend validators, same name convention.
     * If there is a validator, that need some parameters to init, that the
     * validator is written like the 'between' example above.
     * Sometimes it is necessary to nest the param array in an other array.
     * Didn't figure out why :|
     *
     * Third parameter of this method is is just for the lazy ones.
     * Don't want to add to each validator an "notEmpty" validator, because every
     * element is required, just set the flag
     *
     * @param array $params
     * @param array $vmap
     * @param bool $defaultNotEmpty
     * @return bool true|false
     */
    public function isValid($params, array $vmap, $defaultNotEmpty = FALSE)
    {
        $this->messages = array();

        // read the validator map and load the validators
        FOREACH($vmap AS $pKey => $map) {

            IF($defaultNotEmpty === TRUE) {
                $map['validators'][] = 'notEmpty';
            }
            
            $this->getValidators($map['validators']);
        }

        // validate
        FOREACH($vmap AS $pKey => $map) {
            $value = isset($params[$pKey]) ? $params[$pKey] : NULL;
            
            FOREACH($map['validators'] AS $v) {
                IF(is_array($v)) {
                    $v = key($v);
                }

                IF($this->validators[$v]->isValid($value) === FALSE) {
                    $this->messages[] = $map['message'];
                    continue; // ?? why?
                }
            }
        }

        RETURN (count($this->messages) === 0) ? TRUE : FALSE;

    }


    /**
     * Get the error messages of the last run of self::isValid()
     *
     * @return array
     */
    public function getMessages()
    {
        RETURN $this->messages;
    }

    /**
     * Retrieve all validators
     *
     * @return array
     * @see Zend_Form_Element::getValidators() (function from Zend_Form_Element borrowed and modified)
     */
    protected function getValidators($array)
    {
        $validators = array();
        FOREACH ($array AS $value) {

            IF(is_string($value)) {
                $_validator['name'] = $value;
                $_validator['options'] = NULL;
            } ELSEIF(is_array($value)) {
                $_validator['name'] = key($value);
                $_validator['options'] = current($value);
            }

            IF(array_key_exists($_validator['name'], $this->validators) === FALSE) {
                $validator = $this->loadValidator($_validator);
                $validators[get_class($validator)] = $validator;
            }
        }

        RETURN $validators;
    }


    /**
     * Lazy-load a validator
     *
     * @param  array $validator Validator definition
     * @return Zend_Validate_Interface
     * @see Zend_Form_Element::loadValidator() (function from Zend_Form_Element borrowed and modified)
     */
    protected function loadValidator(array $validator)
    {
        $origName = $validator['name'];
        $name     = $this->pluginLoader->load($validator['name']);

        IF(array_key_exists($name, $this->validators)) {
            throw new Webdesktop_Model_Exception(sprintf('Validator instance already exists for validator "%s"', $origName));
        }

        IF(empty($validator['options'])) {
            $instance = new $name;
        } ELSE {
            $r = new ReflectionClass($name);
            IF($r->hasMethod('__construct')) {
                $instance = $r->newInstanceArgs((array) $validator['options']);
            } ELSE {
                $instance = $r->newInstance();
            }
        }

        $this->validators[$origName] = $instance;

        RETURN $instance;
    }

}
?>
