<?php
/**
 * A collection of webdesktop modules
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
 * @class Webdesktop_Model_ModuleSet
 * @extends ArrayObject
 * @interface Countable
 */
class Webdesktop_Model_ModuleSet extends ArrayObject implements Countable
{
    /**
     * Add a webdesktop module to the collection
     *
     * @param Webdesktop_Model_Modules_Abstract $object
     * @return Webdesktop_Model_Modules_Abstract
     */
    public function add(Webdesktop_Model_Modules_Abstract $object)
    {
        IF($this->offsetExists($object->getId()) === FALSE) {
            $this->offsetSet($object->getId(), $object);
        }
        RETURN $object;
    }
    /**
     * Remove a Module from the collection
     *
     * @param Webdesktop_Model_Modules_Abstract|string $module
     * @return bool
     */
    public function remove($module)
    {
        IF($module instanceof  Webdesktop_Model_Modules_Abstract) {
            $ident = $module->getId();
        } ELSEIF(is_string($module)) {
            $ident = $module;
        } ELSE {
            throw new Webdesktop_Model_Exception('Could not find Identifier for module to remove Module from Collection');
        }
        IF($this->offsetExists($ident) === TRUE) {
            $this->offsetUnset($ident);
        }
        RETURN TRUE;
    }

    /**
     * Check if a module is in the collection
     *
     * @param Webdesktop_Model_Modules_Abstract|string $module
     * @return boolRemove a Module from the collection
     */
    public function has($module)
    {
        IF($module instanceof  Webdesktop_Model_Modules_Abstract) {
            $ident = $module->getId();
        } ELSEIF(is_string($module)) {
            $ident = $module;
        } ELSE {
            throw new Webdesktop_Model_Exception('Could not find Identifier for module to remove Module from Collection');
        }

        RETURN (bool) $this->offsetExists($ident);
    }

    /**
     * Return an array of all initscripts from all modules in the collection
     *
     * @return Array|String
     * @todo if collection is empty it currently returns an empty string, check it
     */
    public function getInitScripts()
    {
        IF($this->count() === 0) {
            RETURN '';
        }
        
        $return = array();
        
        FOREACH($this->getArrayCopy() AS $obj) {
            $return[] = $obj->createInitScript();
        }
        
        RETURN $return;
    }

    /**
     * Generate an array with all initCSS files from
     * all modules in the collection
     * 
     * @return array
     * @todo revalidate if needed
     */
    public function getInitCss()
    {
        IF($this->count() === 0) {
            RETURN array();
        }
        $return = array();

        FOREACH($this->getArrayCopy() AS $obj) {
            FOREACH($obj->getFilesModuleCss() AS $file) {
                $return[] = $obj->getId() . '/' . $file;
            }
        }

        RETURN $return;
    }


    // interface implementations

     /**
      * Countable implementation
      *
      * @return int
      */
     public function count()
     {
         RETURN (int) count($this->getArrayCopy());
     }


}
?>
