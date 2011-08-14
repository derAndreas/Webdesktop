<?php
/**
 * Base Form for all forms
 *
 * Reduces LOC and maintain complexity
 *
 * @author Andreas Mairhofer <andreas@classphp.de>
 * @verion 0.1
 * @package Admin
 * @subpackage Form
 * @namespace Admin_Form_Action
 * @see Zend Framework <http://framework.zend.com>
 * @license     http://framework.zend.com/license New BSD License
 */

/**
 * @class Admin_Form_Base
 * @extends Zend_Form
 */
class Admin_Form_Base extends Zend_Form
{
    /**
     * Constructor
     * Needs to be called by any extending form to add default decorators
     * @param Bool|Array $decorators if true, use default decorators, if array
     *                   then set the array as decorators
     */
    public function  __construct($options = array(), $decorators = TRUE)
    {
        parent::__construct($options);

        IF($decorators === TRUE) {
            $this->setDecorators(array(
                'FormElements',
                array('errors', array('class' => 'error', 'placement' => 'prepend')), // FIXME: experimental, remove if causing problem s
                array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
                array('Description', array('tag' => 'p', 'class' => 'error', 'placement' => 'prepend')),
                'Form'
            ));
        } ELSEIF(is_array($decorators)) {
            $this->setDecorators($decorators);
        }

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));
    }
}

?>
