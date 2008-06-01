--TEST--
Test for Console_CommandLine::addOption() method (errors 4).
--FILE--
<?php

require_once('Console/CommandLine.php');
require_once('Console/CommandLine/Command.php');

$parser = new Console_CommandLine();
$parser->addOption('name', array('long_name'=>'d'));

?>
--EXPECTF--

Fatal error: option "name" long name must be 2 dashes followed by a word (got: "d") in %sCommandLine.php on line %d
