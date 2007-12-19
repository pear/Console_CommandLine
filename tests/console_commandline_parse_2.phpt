--TEST--
Test for Console_CommandLine::parse() method (--help).
--SKIPIF--
<?php if(php_sapi_name()!='cli') echo 'skip'; ?>
--ARGS--
--help 2>&1
--FILE--
<?php

require_once 'Console/CommandLine.php' ;
require_once 'Console/CommandLine/Argument.php' ;
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tests.inc.php';

$parser = buildParser1();
$parser->parse();

?>
--EXPECT--
Description of our parser goes here...

Usage:
  some_program [options]

Options:
  -t, --true           test the StoreTrue action
  -f, --false          test the StoreFalse action
  --int=INT            test the StoreInt action
  --float=FLOAT        test the StoreFloat action
  -s, --string=STRING  test the StoreString action
  -c, --counter        test the Counter action
  --callback=callback  test the Callback action
  -a, --array=ARRAY    test the StoreArray action
  -h, --help           show this help message and exit
  --version            show the program version and exit

