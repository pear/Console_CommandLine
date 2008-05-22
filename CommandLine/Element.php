<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Console_CommandLine package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Console 
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     File available since release 0.1.0
 */

/**
 * Class that represent a command line element (an option, or an argument).
 *
 * @category  Console
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     Class available since release 0.1.0
 */
abstract class Console_CommandLine_Element
{
    // Public properties {{{

    /**
     * The element name.
     *
     * @var    string $name
     * @access public
     */
    public $name;

    /**
     * The name of variable displayed in the usage message, if no set it 
     * defaults to the "name" property.
     *
     * @var    string $help_name
     * @access public
     */
    public $help_name;

    /**
     * The element description.
     *
     * @var    string $description
     * @access public
     */
    public $description;

    // }}}
    // __construct() {{{

    /**
     * Constructor.
     *
     * @param string $name   the name of the element
     * @param array  $params an optional array of parameters
     *
     * @access public
     */
    public function __construct($name = null, $params = array()) 
    {
        $this->name = $name;
        foreach ($params as $attr=>$value) {
            if (property_exists($this, $attr)) {
                $this->$attr = $value;
            }
        }
    }

    // }}}
    // toString() {{{

    /**
     * Return the string representation of the argument.
     *
     * @access public
     * @return string
     */
    public function toString()
    {
        return $this->help_name;
    }
    // }}}
    // validate() {{{

    /**
     * Validate the option instance.
     *
     * @access public
     * @return void
     */
    public function validate()
    {
        // if no help_name passed, default to name
        if ($this->help_name == null) {
            $this->help_name = $this->name;
        }
    }

    // }}}
}

?>
