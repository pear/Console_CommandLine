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
 * F
 * Class that represent an option action.
 *
 * @category  Console
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     Class available since release 0.1.0
 * @abstract
 */
abstract class Console_CommandLine_Action
{
    // Properties {{{

    /**
     * A reference to the result instance.
     *
     * @var    object Console_CommandLine_Result $result
     * @access protected
     */
    protected $result;

    /**
     * A reference to the option instance.
     *
     * @var    object Console_CommandLine_Option $option
     * @access protected
     */
    protected $option;

    /**
     * A reference to the parser instance.
     *
     * @var    object Console_CommandLine $parser
     * @access protected
     */
    protected $parser;

    // }}}
    // __construct() {{{

    /**
     * Constructor
     *
     * @param object $result a Console_CommandLine_Result instance
     * @param object $option a Console_CommandLine_Option instance
     * @param object $parser a Console_CommandLine instance
     *
     * @access public
     */
    public function __construct($result, $option, $parser)
    {
        $this->result = $result;
        $this->option = $option;
        $this->parser = $parser;
    }

    // }}}
    // getResult() {{{

    /**
     * Convenience method to retrieve the value of result->options[name].
     *
     * @return mixed $value the assigned value or null
     * @access public
     */
    public function getResult()
    {
        if (isset($this->result->options[$this->option->name])) {
            return $this->result->options[$this->option->name];
        }
        return null;
    }

    // }}}
    // setResult() {{{

    /**
     * Convenience method to assign the result->options[name] value.
     *
     * @param mixed $result the option value
     *
     * @return void
     * @access public
     */
    public function setResult($result)
    {
        $this->result->options[$this->option->name] = $result;
    }

    // }}}
    // execute() {{{

    /**
     * Execute the action with the value entered by the user.
     *
     * @param mixed $value  the option value
     * @param array $params an optional array of parameters
     *
     * @return string
     * @access public
     * @abstract
     */
    abstract public function execute($value=false, $params=array());
    // }}}
}

?>
