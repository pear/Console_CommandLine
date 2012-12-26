--TEST--
Test for Console_CommandLine::parse() with a web request 2
--GET--
help
--FILE--
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tests.inc.php';

$parser = buildParser1();
$parser->parse();

?>
--EXPECT--
Description of our parser goes here...

Usage:
  some_program [options] simple [multiple1 multiple2 ...]

Options:
  -t, --true                        test the StoreTrue action
  -f, --false                       test the StoreFalse action
  --int=INT                         test the StoreInt action
  --float=FLOAT                     test the StoreFloat action
  -s STRING, --string=STRING        test the StoreString action
  -c, --counter                     test the Counter action
  --callback=callback               test the Callback action
  -a ARRAY, --array=ARRAY           test the StoreArray action
  -p password, --password=password  test the Password action
  -h, --help                        show this help message and exit
  -v, --version                     show the program version and exit

Arguments:
  simple    test a simple argument
  multiple  test a multiple argument
