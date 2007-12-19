--TEST--
Test for Console_CommandLine::addArgument() method.
--FILE--
<?php

require_once('Console/CommandLine.php');
require_once('Console/CommandLine/Argument.php');

$parser = new Console_CommandLine();
$parser->addArgument('arg1');
$parser->addArgument('arg2', array(
    'multiple' => true,
    'description' => 'description of arg2'
));
$arg3 = new Console_CommandLine_Argument('arg3', array(
    'multiple' => true,
    'description' => 'description of arg3'    
));
$parser->addArgument($arg3);

var_dump($parser->args);

?>
--EXPECT--
array(3) {
  ["arg1"]=>
  object(Console_CommandLine_Argument)#5 (4) {
    ["multiple"]=>
    bool(false)
    ["name"]=>
    string(4) "arg1"
    ["help_name"]=>
    string(4) "arg1"
    ["description"]=>
    NULL
  }
  ["arg2"]=>
  object(Console_CommandLine_Argument)#6 (4) {
    ["multiple"]=>
    bool(true)
    ["name"]=>
    string(4) "arg2"
    ["help_name"]=>
    string(4) "arg2"
    ["description"]=>
    string(19) "description of arg2"
  }
  ["arg3"]=>
  object(Console_CommandLine_Argument)#7 (4) {
    ["multiple"]=>
    bool(true)
    ["name"]=>
    string(4) "arg3"
    ["help_name"]=>
    string(4) "arg3"
    ["description"]=>
    string(19) "description of arg3"
  }
}
