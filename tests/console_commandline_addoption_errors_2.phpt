--TEST--
Test for Console_CommandLine::addOption() method (errors 2).
--FILE--
<?php

require_once('Console/CommandLine.php');
require_once('Console/CommandLine/Command.php');

$parser = new Console_CommandLine();
$parser->addOption('name', array());

?>
--EXPECTF--

Fatal error: you must provide at least an option short name or long name for option "name" in %sCommandLine.php on line %d
