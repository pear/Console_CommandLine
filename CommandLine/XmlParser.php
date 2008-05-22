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
 * Required file
 */
require_once 'Console/CommandLine.php';

/**
 * Parser for command line xml definitions.
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
class Console_CommandLine_XmlParser
{
    // parse() {{{

    /**
     * Parse the given xml definition file and return a
     * Console_CommandLine instance constructed with the xml data.
     *
     * @param string $xmlfile the xml file to parse
     *
     * @return object a Console_CommandLine instance
     * @access public
     * @static
     */
    public static function parse($xmlfile) 
    {
        if (!is_readable($xmlfile)) {
            Console_CommandLine::triggerError('invalid_xml_file',
                E_USER_ERROR, array('{$file}' => $xmlfile));
        }
        $doc = new DomDocument();
        $doc->load($xmlfile);
        self::validate($doc);
        $root = $doc->childNodes->item(0);
        return self::_parseCommandNode($root, true);
    }

    // }}}
    // parseString() {{{

    /**
     * Parse the given xml definition string and return a
     * Console_CommandLine instance constructed with the xml data.
     *
     * @param string $xmlstr the xml string to parse
     *
     * @return object a Console_CommandLine instance
     * @access public
     * @static
     */
    public static function parseString($xmlstr) 
    {
        $doc = new DomDocument();
        $doc->loadXml($xmlstr);
        self::validate($doc);
        $root = $doc->childNodes->item(0);
        return self::_parseCommandNode($root, true);
    }

    // }}}
    // validate() {{{

    /**
     * Validate the xml definition using Relax NG
     *
     * @param object $doc a DomDocument instance (the document to validate)
     *
     * @return boolean
     * @access public
     * @static
     */
    public static function validate($doc) 
    {
        if (is_dir('@pear_data_dir@' . DIRECTORY_SEPARATOR . 'Console_CommandLine')) {
            $rngfile = '@pear_data_dir@' . DIRECTORY_SEPARATOR
                . 'Console_CommandLine' . DIRECTORY_SEPARATOR . 'data' 
                . DIRECTORY_SEPARATOR . 'xmlschema.rng';
        } else {
            $rngfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' 
                . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR 
                . 'xmlschema.rng';
        }
        if (!is_readable($rngfile)) {
            Console_CommandLine::triggerError('invalid_xml_file',
                E_USER_ERROR, array('{$file}' => $rngfile));
        }
        return $doc->relaxNGValidate($rngfile);
    }

    // }}}
    // _parseCommandNode() {{{

    /**
     * Parse the root command node or a command node and return the
     * constructed Console_CommandLine or Console_CommandLine_Command instance.
     *
     * @param object $node       a DomDocumentNode instance
     * @param bool   $isRootNode boolean that tells if the node is a root node
     *
     * @return object Console_CommandLine or Console_CommandLine_Command
     * @access private
     * @static
     */
    private static function _parseCommandNode($node, $isRootNode=false) 
    {
        if ($isRootNode) { 
            $obj = new Console_CommandLine();
        } else {
            include_once 'Console/CommandLine/Command.php';
            $obj = new Console_CommandLine_Command();
        }
        foreach ($node->childNodes as $cNode) {
            $cNodeName = $cNode->nodeName;
            switch ($cNodeName) {
            case 'name':
            case 'description':
            case 'version':
                $obj->$cNodeName = trim($cNode->nodeValue);
                break;
            case 'add_help_option':
            case 'add_version_option':
            case 'force_posix':
                $obj->$cNodeName = self::_bool(trim($cNode->nodeValue));
                break;
            case 'option':
                $obj->addOption(self::_parseOptionNode($cNode));
                break;
            case 'argument':
                $obj->addArgument(self::_parseArgumentNode($cNode));
                break;
            case 'command':
                $obj->addCommand(self::_parseCommandNode($cNode));
                break;
            default:
                break;
            }
        }
        return $obj;
    }

    // }}}
    // _parseOptionNode() {{{

    /**
     * Parse an option node and return the constructed
     * Console_CommandLine_Option instance.
     *
     * @param object $node a DomDocumentNode instance
     *
     * @return object a Console_CommandLine_Option instance
     * @access private
     * @static
     */
    private static function _parseOptionNode($node) 
    {
        include_once 'Console/CommandLine/Option.php';
        $obj = new Console_CommandLine_Option($node->getAttribute('name'));
        foreach ($node->childNodes as $cNode) {
            $cNodeName = $cNode->nodeName;
            if ($cNodeName == 'choices') {
                foreach ($cNode->childNodes as $subChildNode) {
                    if ($subChildNode->nodeName == 'choice') {
                        $obj->choices[] = trim($subChildNode->nodeValue);
                    }
                }
            } elseif (property_exists($obj, $cNodeName)) {
                $obj->$cNodeName = trim($cNode->nodeValue);
            }
        }
        return $obj;
    }

    // }}}
    // _parseArgumentNode() {{{

    /**
     * Parse an argument node and return the constructed 
     * Console_CommandLine_Argument instance.
     *
     * @param object $node a DomDocumentNode instance
     *
     * @return object a Console_CommandLine_Argument instance
     * @access private
     * @static
     */
    private static function _parseArgumentNode($node) 
    {
        include_once 'Console/CommandLine/Argument.php';
        $obj = new Console_CommandLine_Argument($node->getAttribute('name'));
        foreach ($node->childNodes as $cNode) {
            $cNodeName = $cNode->nodeName;
            switch ($cNodeName) {
            case 'description':
            case 'help_name':
            case 'default':
                $obj->$cNodeName = trim($cNode->nodeValue);
                break;
            case 'multiple':
                $obj->multiple = self::_bool(trim($cNode->nodeValue));
                break;
            default:
                break;
            }
        }
        return $obj;
    }

    // }}}
    // _bool() {{{

    /**
     * Return a boolean according to true/false possible strings
     * 
     * @param string $str the string to process
     *
     * @return boolean
     * @access private
     * @static
     */
    private static function _bool($str)
    {
        return in_array((string)$str, array('true', '1', 'on', 'yes'));
    }

    // }}}
}

?>
