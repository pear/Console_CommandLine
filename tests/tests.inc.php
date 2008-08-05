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
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 */

error_reporting(E_ALL | E_STRICT);

require_once 'Console/CommandLine.php';
require_once 'Console/CommandLine/Renderer.php';
require_once 'Console/CommandLine/Outputter.php';
require_once 'Console/CommandLine/MessageProvider.php';

/**
 * XXX this is a dirty workaround for the PEAR run-test bug reported here:
 * http://pear.php.net/bugs/bug.php?id=12793
 */
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == '--') {
    unset($_SERVER['argv'][1]);
    $_SERVER['argc']--;
    if (isset($argv)) {
        $argv = $_SERVER['argv'];
        $argc = $_SERVER['argc'];
    }
}

/**
 * A dummy callback for tests purposes.
 *
 */
function rot13Callback($value, $option, $result, $parser, $params=array())
{
    $ret = '';
    if (isset($params['prefix'])) {
        $ret .= $params['prefix'] . '__';
    }
    $ret .= str_rot13($value);
    if (isset($params['suffix'])) {
        $ret .= '__' . $params['suffix'];
    }
    return $ret;
}


/**
 * Build a parser instance and return it.
 *
 * @return object Console_CommandLine instance
 */
function buildParser1()
{
    $parser = new Console_CommandLine();
    $parser->name = 'some_program';
    $parser->version = '0.1.0';
    $parser->description = 'Description of our parser goes here...';

    // add options
    $parser->addOption('true', array(
        'short_name'  => '-t',
        'long_name'   => '--true',
        'action'      => 'StoreTrue',
        'description' => 'test the StoreTrue action'
    ));
    $parser->addOption('false', array(
        'short_name'  => '-f',
        'long_name'   => '--false',
        'action'      => 'StoreFalse',
        'description' => 'test the StoreFalse action'
    ));
    $parser->addOption('int', array(
        'long_name'   => '--int',
        'action'      => 'StoreInt',
        'description' => 'test the StoreInt action',
        'help_name'   => 'INT',
        'default'     => 1
    ));
    $parser->addOption('float', array(
        'long_name'   => '--float',
        'action'      => 'StoreFloat',
        'description' => 'test the StoreFloat action',
        'help_name'   => 'FLOAT',
        'default'     => 1.0
    ));
    $parser->addOption('string', array(
        'short_name'  => '-s',
        'long_name'   => '--string',
        'action'      => 'StoreString',
        'description' => 'test the StoreString action',
        'help_name'   => 'STRING',
        'choices'     => array('foo', 'bar', 'baz')
    ));
    $parser->addOption('counter', array(
        'short_name'  => '-c',
        'long_name'   => '--counter',
        'action'      => 'Counter',
        'description' => 'test the Counter action'
    ));
    $parser->addOption('callback', array(
        'long_name'     => '--callback',
        'action'        => 'Callback',
        'description'   => 'test the Callback action',
        'callback'      => 'rot13Callback',
        'action_params' => array('prefix' => 'foo', 'suffix' => 'bar')
    ));
    $parser->addOption('array', array(
        'short_name'  => '-a',
        'long_name'   => '--array',
        'action'      => 'StoreArray',
        'help_name'   => 'ARRAY',
        'description' => 'test the StoreArray action'
    ));
    $parser->addOption('password', array(
        'short_name'  => '-p',
        'long_name'   => '--password',
        'action'      => 'Password',
        'description' => 'test the Password action'
    ));
    $parser->addArgument('simple', array(
        'description' => 'test a simple argument'
    ));
    $parser->addArgument('multiple', array(
        'description' => 'test a multiple argument',
        'multiple'    => true
    ));
    return $parser;
}


/**
 * Build a parser instance and return it.
 *
 * @return object Console_CommandLine instance
 */
function buildParser2()
{
    $parser = new Console_CommandLine();
    $parser->name = 'some_program';
    $parser->version = '0.1.0';
    $parser->description = 'Description of our parser goes here...';

    // add general options
    $parser->addOption('verbose', array(
        'short_name'  => '-v',
        'long_name'   => '--verbose',
        'action'      => 'StoreTrue',
        'description' => 'verbose mode'
    ));
    $parser->addOption('logfile', array(
        'short_name'  => '-l',
        'long_name'   => '--logfile',
        'action'      => 'StoreString',
        'description' => 'path to logfile'
    ));
 
    // install subcommand
    $cmd1 = $parser->addCommand('install', array(
        'description' => 'install given package'
    ));
    $cmd1->addOption('force', array(
        'short_name'  => '-f',
        'long_name'   => '--force',
        'action'      => 'StoreTrue',
        'description' => 'force installation'
    ));
    $cmd1->addArgument('package', array(
        'description' => 'package to install'
    ));

    // uninstall subcommand
    $cmd2 = $parser->addCommand('uninstall', array(
        'description' => 'uninstall given package'
    ));
    $cmd2->addArgument('package', array(
        'description' => 'package to uninstall'
    ));
    return $parser;
}

class CustomRenderer implements Console_CommandLine_Renderer 
{
    public function usage()
    {
        return __METHOD__ . '()';
    }
    public function error($error)
    {
        return __METHOD__ . "($error)";
    }
    public function version()
    {
        return __METHOD__ . '()';
    }
}

class CustomOutputter implements Console_CommandLine_Outputter
{
    public function stdout($msg)
    {
        echo "STDOUT >> $msg\n";
    }
    public function stderr($msg)
    {
        echo "STDERR >> $msg\n";
    }
}

class CustomMessageProvider implements Console_CommandLine_MessageProvider
{
    public function get($code, $vars = array())
    {
        return $code;
    }
}

?>
