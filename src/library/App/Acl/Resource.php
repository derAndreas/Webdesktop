<?php
/**
 * Definition of a ressource in the application
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package App
 * @subpackage App_Acl
 * @namespace App_Acl
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class App_Acl_Resource
 */
class App_Acl_Resource implements Zend_Acl_Resource_Interface {
    /**
     * Unique identifier of a resource
     *
     * @var string
     */
    protected $id;
    /**
     * the resource name
     * 
     * @var string
     */
    protected $name;

    /**
     * construct a resource
     *
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = (string) $name;
    }

    /**
     * set the resource name
     *
     * @param string $name
     * @return App_Acl_Resource $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        RETURN $this;
    }

    /**
     * Get the resoruce name
     *
     * @return string
     */
    public function getName()
    {
        RETURN $this->name;
    }
    
    /**
     * get the resource identifier
     * Interface implementation
     * 
     * @return string
     */
    public function getResourceId() 
    {
        RETURN (string) $this->name;
    }
}
?>
