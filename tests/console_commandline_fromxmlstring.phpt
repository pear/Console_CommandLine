--TEST--
Test for Console_CommandLine::fromXmlString() method.
--SKIPIF--
<?php if(php_sapi_name()!='cli') echo 'skip'; ?>
--ARGS--
--help 2>&1
--FILE--
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tests.inc.php';

$str = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test.xml');
$parser = Console_CommandLine::fromXmlString($str);
$parser->parse();

?>
--EXPECT--
zip given files using the php zip module.

Usage:
  test [options] <files...> <zipfile>

Options:
  -c choice, --choice=choice  choice option
  -v, --verbose               turn on verbose output
  -d, --delete                delete original files after zip operation
  -h, --help                  show this help message and exit
  --version                   show the program version and exit

Arguments:
  files    a list of files to zip together
  zipfile  path to the zip file to generate
