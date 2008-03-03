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
 * The renderer interface.
 */
require_once 'Console/CommandLine/Renderer.php';

/**
 * Console_CommandLine default renderer.
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
class Console_CommandLine_Renderer_Default implements Console_CommandLine_Renderer
{
    // Properties {{{

    /**
     * integer that define the max width of the help text
     *
     * @var    integer $line_width
     * @access public
     */
    public $line_width = 75;

    /**
     * An instance of Console_CommandLine
     *
     * @var    object Console_CommandLine $parser
     * @access protected
     */
    protected $parser = false;

    // }}}
    // Console_CommandLine_Renderer_Default::__construct() {{{

    /**
     * Constructor.
     *
     * @param object $parser a Console_CommandLine instance
     *
     * @access public
     */
    public function __construct($parser) 
    {
        $this->parser = $parser;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::usage() {{{

    /**
     * Return the full usage message
     *
     * @return string the usage message
     * @access public
     */
    public function usage()
    {
        $ret = '';
        if (!empty($this->parser->description)) { 
            $ret .= $this->description() . "\n\n";
        }
        $ret .= $this->usageLine() . "\n";
        if (count($this->parser->commands) > 0) {
            $ret .= $this->commandUsageLine() . "\n";
        }
        if (count($this->parser->options) > 0) {
            $ret .= "\n" . $this->optionList() . "\n";
        }
        if (count($this->parser->args) > 0) {
            $ret .= "\n" . $this->argumentList() . "\n";
        }
        if (count($this->parser->commands) > 0) {
            $ret .= "\n" . $this->commandList() . "\n";
        }
        $ret .= "\n";
        return $ret;
    }
    // }}}
    // Console_CommandLine_Renderer_Default::error() {{{

    /**
     * Return a formatted error message
     *
     * @param string $error the error message to format
     *
     * @return string the error string
     * @access public
     */
    public function error($error)
    {
        $ret = 'Error: ' . $error . "\n";
        if ($this->parser->add_help_option) {
            $name = $this->name();
            $ret .= $this->wrap($this->parser->message_provider->get('PROG_HELP_LINE',
                array('progname' => $name))) . "\n";
            if (count($this->parser->commands) > 0) {
                $ret .= $this->wrap($this->parser->message_provider->get('COMMAND_HELP_LINE',
                    array('progname' => $name))) . "\n";
            }
        }
        return $ret;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::version() {{{

    /**
     * Return the program version string
     *
     * @return string the version string
     * @access public
     */
    public function version()
    {
        return $this->parser->message_provider->get('PROG_VERSION_LINE', array(
            'progname' => $this->name(),
            'version'  => $this->parser->version
        )) . "\n";
    }

    // }}}
    // Console_CommandLine_Renderer_Default::name() {{{

    /**
     * return the full name of the program or the sub command
     *
     * @return string
     * @access protected
     */
    protected function name()
    {
        $name   = '';
        $parent = $this->parser->parent;
        while ($parent) {
            $name .= $parent->name . ' ';
            if (count($parent->options) > 0) {
                $name .= '[' 
                    . strtolower($this->parser->message_provider->get('OPTION_WORD',
                          array('plural' => 's'))) 
                    . '] ';
            }
            $parent = $parent->parent;
        }
        $name .= $this->parser->name;
        return $this->wrap($name);
    }

    // }}}
    // Console_CommandLine_Renderer_Default::description() {{{

    /**
     * Return the command line description message
     *
     * @access protected
     * @return string the usage message
     */
    protected function description()
    {
        return $this->wrap($this->parser->description);
    }

    // }}}
    // Console_CommandLine_Renderer_Default::usageLine() {{{

    /**
     * Return the command line usage message
     *
     * @return string the usage message
     * @access protected
     */
    protected function usageLine()
    {
        $usage = $this->parser->message_provider->get('USAGE_WORD') . ":\n";
        $ret   = $usage . '  ' . $this->name();
        if (count($this->parser->options) > 0) {
            $ret .= ' [' 
                . strtolower($this->parser->message_provider->get('OPTION_WORD'))
                . ']';
        }
        if (count($this->parser->args) > 0) {
            foreach ($this->parser->args as $name=>$arg) {
                $ret .= ' <' . $arg->help_name . ($arg->multiple?'...':'') . '>';
            }
        }
        return $this->columnWrap($ret, 2);
    }

    // }}}
    // Console_CommandLine_Renderer_Default::commandUsageLine() {{{

    /**
     * Return the command line usage message for subcommands
     *
     * @return string
     * @access protected
     */
    protected function commandUsageLine()
    {
        if (count($this->parser->commands) == 0) {
            return '';
        }
        $ret = '  ' . $this->name();
        if (count($this->parser->options) > 0) {
            $ret .= ' [' 
                . strtolower($this->parser->message_provider->get('OPTION_WORD'))
                . ']';
        }
        //XXX
        $ret .= " <command> [options] [args]";
        return $this->columnWrap($ret, 2);
    }

    // }}}
    // Console_CommandLine_Renderer_Default::argumentList() {{{

    /**
     * Render the arguments list that will be displayed to the user, you can 
     * override this method if you want to change the look of the list.
     *
     * @return string the formatted argument list
     * @access protected
     */
    protected function argumentList()
    {
        $col  = 0;
        $args = array();
        foreach ($this->parser->args as $arg) {
            $argstr = '  ' . $arg->toString();
            $args[] = array($argstr, $arg->description);
            $ln     = strlen($argstr);
            if ($col < $ln) {
                $col = $ln;
            }
        }
        $ret = $this->parser->message_provider->get('ARGUMENT_WORD') . ":";
        foreach ($args as $arg) {
            $text = str_pad($arg[0], $col) . '  ' . $arg[1];
            $ret .= "\n" . $this->columnWrap($text, $col+2);
        }
        return $ret;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::optionList() {{{

    /**
     * Render the options list that will be displayed to the user, you can 
     * override this method if you want to change the look of the list.
     *
     * @return string the formatted option list
     * @access protected
     */
    protected function optionList()
    {
        $col     = 0;
        $options = array();
        foreach ($this->parser->options as $option) {
            $optstr    = '  ' . $option->toString();
            $options[] = array($optstr, $option->description);
            $ln        = strlen($optstr);
            if ($col < $ln) {
                $col = $ln;
            }
        }
        $ret = $this->parser->message_provider->get('OPTION_WORD') . ":";
        foreach ($options as $option) {
            $text = str_pad($option[0], $col) . '  ' . $option[1];
            $ret .= "\n" . $this->columnWrap($text, $col+2);
        }
        return $ret;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::commandList() {{{

    /**
     * Render the command list that will be displayed to the user, you can 
     * override this method if you want to change the look of the list.
     *
     * @return string the formatted subcommand list
     * @access protected
     */
    protected function commandList()
    {

        $commands = array();
        $col      = 0;
        foreach ($this->parser->commands as $cmdname=>$command) {
            $cmdname    = '  ' . $cmdname;
            $commands[] = array($cmdname, $command->description);
            $ln         = strlen($cmdname);
            if ($col < $ln) {
                $col = $ln;
            }
        }
        $ret = $this->parser->message_provider->get('COMMAND_WORD') . ":";
        foreach ($commands as $command) {
            $text = str_pad($command[0], $col) . '  ' . $command[1];
            $ret .= "\n" . $this->columnWrap($text, $col+2);
        }
        return $ret;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::wrap() {{{

    /**
     * Wraps the text passed to the method.
     *
     * @param string $text The text to wrap
     * @param int    $lw   The column width. Defaults to line_width property.
     *
     * @return string
     * @access protected
     */
    protected function wrap($text, $lw=null)
    {
        if ($this->line_width > 0) {
            if ($lw === null) {
                $lw = $this->line_width;
            }
            return wordwrap($text, $lw, "\n", false);
        }
        return $text;
    }

    // }}}
    // Console_CommandLine_Renderer_Default::columnWrap() {{{

    /**
     * Wraps the text passed to the method at the specified width.
     *
     * @param string $text the text to wrap
     * @param int    $cw   the wrap width
     *
     * @return string
     * @access protected
     */
    protected function columnWrap($text, $cw)
    {
        $tokens = explode("\n", $this->wrap($text));
        $ret    = $tokens[0];
        $chunks = $this->wrap(trim(substr($text, strlen($ret))), 
            $this->line_width - $cw);
        $tokens = explode("\n", $chunks);
        foreach ($tokens as $token) {
            if (!empty($token)) {
                $ret .= "\n" . str_repeat(' ', $cw) . $token;
            }
        }
        return $ret;
    }

    // }}}
}

?>
