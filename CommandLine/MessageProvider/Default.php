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
 * The message provider interface.
 */
require_once 'Console/CommandLine/MessageProvider.php';

/**
 * Lightweight class that manages messages used by Console_CommandLine package, 
 * allowing the developper to customize these messages, for example to 
 * internationalize a command line frontend.
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
class Console_CommandLine_MessageProvider_Default implements Console_CommandLine_MessageProvider
{
    // Console_CommandLine_MessageProvider_Default properties {{{

    /**
     * Associative array of messages
     *
     * @var $messages
     * @static
     * @access protected
     */
    protected $messages = array(
        'OPTION_VALUE_REQUIRED'   => 'option "{$name}" require a value.',
        'OPTION_VALUE_UNEXPECTED' => 'option "{$name}" does not expects a value (got "{$value}").',
        'OPTION_VALUE_NOT_VALID'  => 'option "{$name}" must be one of the following: "{$choices}" (got "{$value}").',
        'OPTION_VALUE_TYPE_ERROR' => 'option "{$name}" require a value of type {$type} (got "{$value}").',
        'OPTION_AMBIGUOUS'        => 'ambiguous option "{$name}", can be one of the following: {$matches}.',
        'OPTION_UNKNOWN'          => 'unknown option "{$name}".',
        'ARGUMENT_REQUIRED'       => 'you must provide at least {$argnum} argument{$plural}.',
        'PROG_HELP_LINE'          => 'Type "{$progname} -h" to get help.',
        'PROG_VERSION_LINE'       => '{$progname} version {$version}.',
        'COMMAND_HELP_LINE'       => 'Type "{$progname} <command> -h" to get help on specific command.',
        'USAGE_WORD'              => 'Usage',
        'OPTION_WORD'             => 'Options',
        'ARGUMENT_WORD'           => 'Arguments',
        'COMMAND_WORD'            => 'Commands',
        'PASSWORD_PROMPT'         => 'Password: ',
        'PASSWORD_PROMPT_ECHO'    => 'Password (warning: will echo): '
    );

    // }}}
    // Console_CommandLine_MessageProvider_Default::get() {{{

    /**
     * Retrieve the given string identifier corresponding message.
     *
     * @param string $code the string identifier of the message
     * @param array  $vars an array of template variables
     *
     * @return string
     * @access public
     */
    public function get($code, $vars=array())
    {
        if (!isset($this->messages[$code])) {
            return 'UNKNOWN';
        }
        $tmpkeys = array_keys($vars);
        $keys    = array();
        foreach ($tmpkeys as $key) {
            $keys[] = '{$' . $key . '}';
        }
        return str_replace($keys, array_values($vars), $this->messages[$code]);
    }

    // }}}
}

?>
