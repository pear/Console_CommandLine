--TEST--
Test for Console_CommandLine::parse() method (user errors 1).
--SKIPIF--
<?php if(php_sapi_name()!='cli') echo 'skip'; ?>
--ARGS--
--int=foo foo bar
--FILE--
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tests.inc.php';

$parser = buildParser1();
try {
    $result = $parser->parse();
} catch (Exception $exc) {
    echo $exc->getMessage();
}

?>
--EXPECT--
option "int" require a value of type int (got "foo").
