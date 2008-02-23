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
 * @since     Class available since release 0.1.0
 */

// to skip E_STRICT errors from PackageFileManager package
error_reporting(E_ALL);

/**
 * Uses PackageFileManager
 */ 
require_once 'PEAR/PackageFileManager.php';

/**
 * Current version
 */
$version = '1.0.0RC2';

/**
 * Current state
 */
$state = 'beta';

/**
 * Release notes
 */
$notes = '
- some clean up in the default renderer,
- wrapping can be disabled by setting $renderer->line_width to -1,
- fixed bug #13038: changed the signature of the parse method to allow the
  developer to pass argc and argv array (instead of using $_SERVER values).
';

/**
 * Description summary.
 */
$summary = 'A full featured command line options and arguments parser';

/**
 * Full description
 */
$description = <<<EOT
Console_CommandLine is a full featured package for managing command-line 
options and arguments highly inspired from python optparse module, it allows 
the developer to easily build complex command line interfaces.

Main features:
  * handles sub commands (ie. $ myscript.php -q subcommand -f file),
  * can be completely built from an xml definition file,
  * generate --help and --version options automatically,
  * can be completely customized,
  * builtin support for i18n,
  * and much more...
EOT;

$package = new PEAR_PackageFileManager();

$result = $package->setOptions(array(
    'package'           => 'Console_CommandLine',
    'summary'           => $summary,
    'description'       => $description,
    'version'           => $version,
    'state'             => $state,
    'license'           => 'MIT License',
    'ignore'            => array('package.php', 'package.xml', 'package2.xml', '*.tgz'),
    'filelistgenerator' => 'cvs',
    'notes'             => $notes,
    'simpleoutput'      => true,
    'baseinstalldir'    => 'Console',
    'packagedirectory'  => dirname(__FILE__),
    'dir_roles'         => array(
        'data'  => 'data',
        'docs'  => 'doc',
        'tests' => 'test'
    )
));

if (PEAR::isError($result)) {
    echo $result->getMessage();
    exit(1);
}

// XXX code below will only work when this feature request is implemented in
// PEAR_PackageFileManager: http://pear.php.net/bugs/bug.php?id=12820
//$package->addReplacement('CommandLine.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('{docs/examples,CommandLine}/*.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('CommandLine/*/*.php', 'package-info', '@package_version@', 'version');

$package->addGlobalReplacement('package-info', '@package_version@', 'version');
$package->addReplacement('CommandLine/XmlParser.php', 'pear-config', '@pear_data_dir@', 'data_dir');
$package->addMaintainer('izi', 'lead', 'David JEAN LOUIS', 'izimobil@gmail.com');
$package->addDependency('php', '5.1.0', 'ge', 'php', false);

if (isset($_GET['make']) || 
    (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make')) {
    $result = $package->writePackageFile();
    system('pear convert package.xml');
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    exit(1);
}

?>
