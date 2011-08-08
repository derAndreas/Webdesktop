<?php
/**
 * Description of Resource
 *
 * @author Godfather
 */
class App_Acl_Resource implements Zend_Acl_Resource_Interface {
    /**
     * Unique identifier of a resource
     *
     * @var string
     */
    protected $resourceId;
    /**
     * the resource name
     * 
     * @var string
     */
    protected $resourceName;

    /**
     * construct a resource
     *
     * @param int $id
     * @param string $name
     * @access public
     */
    public function __construct($id, $name)
    {
        $this->resourceId = $id;
        $this->resourceName = (string) $name;
    }

    /**
     * set the resource name
     *
     * @param string $name
     * @return App_Acl_Resource $this
     * @access public
     */
    public function setName($name)
    {
        $this->resourceName = (string) $name;

        RETURN $this;
    }

    /**
     * Get the resoruce name
     *
     * @return string
     * @access public
     */
    public function getName()
    {
        RETURN (string) $this->resourceName;
    }
    
    /**
     * get the resource identifier
     * 
     * @return string
     * @access public
     */
    public function getResourceId() 
    {
        RETURN (string) $this->resourceName;
    }

}
?>
