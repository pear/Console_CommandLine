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
 * @since     Class available since release @package_version@
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
$version = '0.1.1';

/**
 * Current state
 */
$state = 'alpha';

/**
 * Release notes
 */
$notes = '
* bugs
  - fixed bug #12711,
  - fixed wrong error triggered when argument name is invalid.

* changes
  - some cosmetic changes,
  - added a lot of phpt test files.
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
    'ignore'            => array('*CVS*', 'package.php', 'package.xml', 'package2.xml', '*.tgz'),
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
