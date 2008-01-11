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
 * Required by this class.
 */
require_once 'Console/CommandLine.php';
require_once 'Console/CommandLine/Element.php';

/**
 * Class that represent a commandline option.
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
class Console_CommandLine_Option extends Console_CommandLine_Element
{
    // Public properties {{{

    /**
     * The option short name (ex: -v).
     *
     * @var string $short_name
     * @access public
     */
    public $short_name;

    /**
     * The option long name (ex: --verbose).
     *
     * @var string $long_name
     * @access public
     */
    public $long_name;

    /**
     * The option action, defaults to StoreString.
     *
     * @var int $action
     * @access public
     */
    public $action = 'StoreString';

    /**
     * The default value of the option if not provided on the command line.
     *
     * @var mixed $default
     * @access public
     */
    public $default;

    /**
     * An array of possible values for the option if this array is not empty 
     * and the value passed is not in the array an exception is raised.
     * This only make sense for actions that accept values of course.
     *
     * @var array $choices
     * @access public
     */
    public $choices = array();

    /**
     * The callback function (or method) to call for an action of type 
     * Callback, this can be any callable supported by the php function 
     * call_user_func.
     * 
     * Example:
     *
     * <code>
     * $parser->addOption('myoption', array(
     *     'short_name' => '-m',
     *     'long_name'  => '--myoption',
     *     'action'     => 'Callback',
     *     'callback'   => 'myCallbackFunction'
     * ));
     * </code>
     *
     * @var    mixed $callback
     * @access public
     */
    public $callback;

    /**
     * An associative array of additional params to pass to the class 
     * corresponding to the action, this array will also be passed to the 
     * callback defined for an action of type Callback, Example:
     *
     * <code>
     * // for a custom action
     * $parser->addOption('myoption', array(
     *     'short_name'    => '-m',
     *     'long_name'     => '--myoption',
     *     'action'        => 'MyCustomAction',
     *     'action_params' => array('foo'=>true, 'bar'=>false)
     * ));
     *
     * // if the user type:
     * // $ <yourprogram> -m spam
     * // in your MyCustomAction class the execute() method will be called
     * // with the value 'spam' as first parameter and 
     * // array('foo'=>true, 'bar'=>false) as second parameter
     * </code>
     *
     * @var    array $action_params
     * @access public
     */
    public $action_params = array();

    /**
     * For options that expect an argument, this property tells the parser if 
     * the argument is optional and can be ommited
     *
     * @var boolean $argumentOptional
     * @access public
     */
    public $argument_optional = false;

    // }}}
    // Console_CommandLine_Option::__construct() {{{

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
        parent::__construct($name, $params);
        if ($this->action == 'Password') {
            // special case for Password action, password can be passed to the 
            // commandline or prompted by the parser
            $this->argument_optional = true;
        }
    }

    // }}}
    // Console_CommandLine_Option::toString() {{{

    /**
     * Return the string representation of the option.
     *
     * @access public
     * @return string
     */
    public function toString()
    {
        $ret     = '';
        $padding = '';
        if ($this->short_name != null) {
            $ret .= $this->short_name;
            if ($this->expectsArgument()) {
                $ret .= ' ' . $this->help_name;
            }
            $padding = ', ';
        }
        if ($this->long_name != null) {
            $ret .= $padding . $this->long_name;
            if ($this->expectsArgument()) {
                $ret .= '=' . $this->help_name;
            }
        }
        return $ret;
    }

    // }}}
    // Console_CommandLine_Option::expectsArgument() {{{

    /**
     * Return true if the option requires one or more argument and false 
     * otherwise.
     *
     * @access public
     * @return boolean
     */
    public function expectsArgument()
    {
        if ($this->action == 'StoreTrue' || $this->action == 'StoreFalse' ||
            $this->action == 'Help' || $this->action == 'Version' ||
            $this->action == 'Counter') {
            return false;
        }
        return true;
    }

    // }}}
    // Console_CommandLine_Option::dispatchAction() {{{

    /**
     * Format the value $value according to the action of the option and 
     * update the passed Console_CommandLine_Result object.
     *
     * @param mixed  $value  the value to format
     * @param object $result a Console_CommandLine_Result instance
     * @param object $parser a Console_CommandLine instance
     *
     * @return void
     * @access public
     */
    public function dispatchAction($value, $result, $parser)
    {
        // check value is in option choices
        if (!empty($this->choices) && !in_array($value, $this->choices)) {
            throw Console_CommandLine_Exception::build('OPTION_VALUE_NOT_VALID',
                array(
                    'name'    => $this->name,
                    'choices' => implode('", "', $this->choices),
                    'value'   => $value,
                ), $parser);
        }
        $actionInfo = Console_CommandLine::$actions[$this->action];
        if (true === $actionInfo[1]) {
            // we have a "builtin" action
            $tokens = explode('_', $actionInfo[0]);
            include_once implode('/', $tokens) . '.php';
        }
        $clsname = $actionInfo[0];
        $action  = new $clsname($result, $this, $parser);
        $action->execute($value, $this->action_params);
    }

    // }}}
    // Console_CommandLine_Option::validate() {{{

    /**
     * Validate the option instance.
     *
     * @access public
     * @return void
     */
    public function validate()
    {
        // check if the option name is valid
        if (!preg_match('/^[a-zA-Z_\x7f-\xff]+[a-zA-Z0-9_\x7f-\xff]*$/',
            $this->name)) {
            Console_CommandLine::triggerError('option_bad_name',
                E_USER_ERROR, array('{$name}' => $this->name));
        }
        // call the parent validate method
        parent::validate();
        // a short_name or a long_name must be provided
        if ($this->short_name == null && $this->long_name == null) {
            Console_CommandLine::triggerError('option_long_and_short_name_missing',
                E_USER_ERROR, array('{$name}' => $this->name));
        }
        // check if the option short_name is valid
        if ($this->short_name != null && 
            !(preg_match('/^\-[a-zA-Z]{1}$/', $this->short_name))) {
            Console_CommandLine::triggerError('option_bad_short_name',
                E_USER_ERROR, array(
                    '{$name}' => $this->name, 
                    '{$short_name}' => $this->short_name
                ));
        }
        // check if the option long_name is valid
        if ($this->long_name != null && 
            !preg_match('/^\-\-[a-zA-Z]+[a-zA-Z0-9_\-]*$/', $this->long_name)) {
            Console_CommandLine::triggerError('option_bad_long_name',
                E_USER_ERROR, array(
                    '{$name}' => $this->name, 
                    '{$long_name}' => $this->long_name
                ));
        }
        // check if we have a valid action
        if (!is_string($this->action)) {
            Console_CommandLine::triggerError('option_bad_action',
                E_USER_ERROR, array('{$name}' => $this->name));
        }
        if (!isset(Console_CommandLine::$actions[$this->action])) {
            Console_CommandLine::triggerError('option_unregistered_action',
                E_USER_ERROR, array(
                    '{$action}' => $this->action,
                    '{$name}' => $this->name
                ));
        }
        // if the action is a callback, check that we have a valid callback
        if ($this->action == 'Callback' && !is_callable($this->callback)) {
            Console_CommandLine::triggerError('option_invalid_callback',
                E_USER_ERROR, array('{$name}' => $this->name));
        }
    }

    // }}}
}

?>
