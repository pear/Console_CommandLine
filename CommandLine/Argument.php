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

require_once 'Console/CommandLine/Element.php';

/**
 * Class that represent a command line argument.
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
class Console_CommandLine_Argument extends Console_CommandLine_Element
{
    // Public properties {{{

    /**
     * If set to true argument values will be stored in an array.
     *
     * @var    boolean $multiple
     * @access public
     */
    public $multiple = false;

    // }}}
    // Console_CommandLine_Element::validate() {{{

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
            Console_CommandLine::triggerError('argument_bad_name',
                E_USER_ERROR, array('{$name}' => $this->name));
        }
        parent::validate();
    }

    // }}}
}

?>
