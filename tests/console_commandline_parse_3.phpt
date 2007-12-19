--TEST--
Test for Console_CommandLine::parse() method (various options).
--SKIPIF--
<?php if(php_sapi_name()!='cli') echo 'skip'; ?>
--INI--
register_argc_argv=1
variables_order=GPS
--ARGS--
-tfsfoo --int=3 --float=4.0 -cccc --callback=somestring -a foo bar baz
--FILE--
<?php

require_once 'Console/CommandLine.php' ;
require_once 'Console/CommandLine/Argument.php' ;
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tests.inc.php';

$parser = buildParser1();
$result = $parser->parse();
var_dump($result->options);

?>
--EXPECT--
array(10) {
  ["true"]=>
  bool(true)
  ["false"]=>
  bool(false)
  ["int"]=>
  int(3)
  ["float"]=>
  float(4)
  ["string"]=>
  string(3) "foo"
  ["counter"]=>
  int(4)
  ["callback"]=>
  string(20) "foo__fbzrfgevat__bar"
  ["array"]=>
  array(3) {
    [0]=>
    string(3) "foo"
    [1]=>
    string(3) "bar"
    [2]=>
    string(3) "baz"
  }
  ["help"]=>
  NULL
  ["version"]=>
  NULL
}
