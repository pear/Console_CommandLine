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
$version = '1.0.0RC1';

/**
 * Current state
 */
$state = 'beta';

/**
 * Release notes
 */
$notes = '
- fixed a missing check when a short option require an argument and is the last
  in the argv array,
- more GNU getopt compliance: long option/argument can also be separated by a
  space now and long options abbreviations are supported,
- added a "Password" action: with this action it is possible to specify a
  password on the command line, and if it is missing it will be prompted to
  user (and will not be echo on stdin on UNIX systems),
- allow "force_posix" option to be passed to the constructor,
- added more tests.
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

//$package->addReplacement('CommandLine.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('CommandLine/*.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('CommandLine/*/*.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('examples/*.php', 'package-info', '@package_version@', 'version');
$package->addGlobalReplacement('package-info', '@package_version@', 'version');
$package->addReplacement('CommandLine/XmlParser.php', 'pear-config', '@pear_data_dir@', 'data_dir');
$package->addMaintainer('izi', 'lead', 'David JEAN LOUIS', 'izimobil@gmail.com');
$package->addDependency('php', '5.0.0', 'ge', 'php', false);

if (isset($_GET['make']) || 
    (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make')) {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    exit(1);
}

?>
