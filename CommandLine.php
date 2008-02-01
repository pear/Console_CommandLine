<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Console_CommandLine package.
 *
 * A full featured package for managing command-line options and arguments 
 * hightly inspired from python optparse module, it allows the developper to 
 * easily build complex command line interfaces.
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

/**
 * Exception thrown by this class
 */
require_once 'Console/CommandLine/Exception.php';

/**
 * Main class for parsing command line options and arguments.
 * 
 * There are three ways to create parsers with this class:
 * <code>
 * // direct usage
 * $parser = new Console_CommandLine();
 *
 * // with an xml definition file
 * $parser = Console_CommandLine::fromXmlFile('path/to/file.xml');
 *
 * // with an xml definition string
 * $validXmlString = '..your xml string...';
 * $parser = Console_CommandLine::fromXmlString($validXmlString);
 * </code>
 *
 * @category  Console
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     File available since release 0.1.0
 * @example   docs/examples/ex1.php
 * @example   docs/examples/ex2.php
 */
class Console_CommandLine
{
    // Public properties {{{

    /**
     * Error messages.
     *
     * @var    array $errors
     * @access public
     */
    public static $errors = array(
        'option_bad_name'                    => 'option name must be a valid php variable name (got: {$name})',
        'argument_bad_name'                  => 'argument name must be a valid php variable name (got: {$name})',
        'option_long_and_short_name_missing' => 'you must provide at least an option short name or long name for option "{$name}"',
        'option_bad_short_name'              => 'option "{$name}" short name must be a dash followed by a letter (got: "{$short_name}")',
        'option_bad_long_name'               => 'option "{$name}" long name must be 2 dashes followed by a word (got: "{$long_name}")',
        'option_unregistered_action'         => 'unregistered action "{$action}" for option "{$name}".',
        'option_bad_action'                  => 'invalid action for option "{$name}".',
        'option_invalid_callback'            => 'you must provide a valid callback for option "{$name}"',
        'action_class_does_not_exists'       => 'action "{$name}" class "{$class}" not found, make sure that your class is available before calling Console_CommandLine::registerAction()',
        'invalid_xml_file'                   => 'XML definition file "{$file}" does not exists or is not readable',
        'invalid_rng_file'                   => 'RNG file "{$file}" does not exists or is not readable'
    );

    /**
     * The name of the program, if not given it defaults to argv[0].
     *
     * @var    string $name
     * @access public
     */
    public $name;

    /**
     * A description text that will be displayed in the help message.
     *
     * @var    string $description
     * @access public
     */
    public $description = '';

    /**
     * A string that represents the version of the program, if this property is 
     * not empty and property add_version_option is not set to false, the
     * command line parser will add a --version option, that will display the
     * property content.
     *
     * @var    string $version
     * @access public
     */
    public $version = '';

    /**
     * Boolean that determine if the command line parser should add the help
     * (-h, --help) option automatically.
     *
     * @var    bool $add_help_option
     * @access public
     */
    public $add_help_option = true;

    /**
     * Boolean that determine if the command line parser should add the version
     * (-v, --version) option automatically.
     * Note that the version option is also generated only if the version 
     * property is not empty, it's up to you to provide a version string of 
     * course.
     *
     * @var    bool $add_version_option
     * @access public
     */
    public $add_version_option = true;

    /**
     * The command line parser renderer instance.
     *
     * @var    object that implements Console_CommandLine_Renderer interface
     * @access protected
     */
    public $renderer = false;

    /**
     * The command line parser outputter instance.
     *
     * @var    object that implements Console_CommandLine_Outputter interface
     * @access protected
     */
    public $outputter = false;

    /**
     * The command line message provider instance.
     *
     * @var    object an instance of Console_CommandLine_Message
     * @access protected
     */
    public $message_provider = false;

    /**
     * Boolean that tells the parser to be POSIX compliant, POSIX demands the 
     * following behavior: the first non-option stops option processing.
     *
     * @var    bool $force_posix
     * @access public
     */
    public $force_posix = false;

    /**
     * An array of Console_CommandLine_Option objects.
     *
     * @var    array $options
     * @access public
     */
    public $options = array();

    /**
     * An array of Console_CommandLine_Argument objects.
     *
     * @var    array $args
     * @access public
     */
    public $args = array();

    /**
     * An array of Console_CommandLine_Command objects (sub commands).
     *
     * @var    array $commands
     * @access public
     */
    public $commands = array();

    /**
     * Parent, only relevant in Command objects but left here for interface 
     * convenience.
     *
     * @var    object Console_CommandLine
     * @access public
     */
    public $parent = false;

    /**
     * Array of valid actions for an option, this array will also store user 
     * registered actions.
     * The array format is:
     * <pre>
     * array(
     *     <ActionName:string> => array(<ActionClass:string>, <builtin:bool>)
     * )
     *
     * @var    array $actions
     * @static
     * @access public
     */
    public static $actions = array(
        'StoreTrue'   => array('Console_CommandLine_Action_StoreTrue', true),
        'StoreFalse'  => array('Console_CommandLine_Action_StoreFalse', true),
        'StoreString' => array('Console_CommandLine_Action_StoreString', true),
        'StoreInt'    => array('Console_CommandLine_Action_StoreInt', true),
        'StoreFloat'  => array('Console_CommandLine_Action_StoreFloat', true),
        'StoreArray'  => array('Console_CommandLine_Action_StoreArray', true),
        'Callback'    => array('Console_CommandLine_Action_Callback', true),
        'Counter'     => array('Console_CommandLine_Action_Counter', true),
        'Help'        => array('Console_CommandLine_Action_Help', true),
        'Version'     => array('Console_CommandLine_Action_Version', true),
        'Password'    => array('Console_CommandLine_Action_Password', true)
    );

    /**
     * Array of options that must be dispatched at the end.
     *
     * @var    array $_dispatchLater
     * @access private
     */
    private $_dispatchLater = array();

    // }}}
    // Console_CommandLine::__construct() {{{

    /**
     * Constructor.
     * Example:
     *
     * <code>
     * $parser = new Console_CommandLine(array(
     *     'name' => 'yourprogram', // if not given it defaults to argv[0]
     *     'description' => 'Some meaningful description of your program',
     *     'version' => '0.0.1', // your program version
     *     'add_help_option' => true, // or false to disable --version option
     *     'add_version_option' => true, // or false to disable --help option
     *     'renderer' => $rdr,  // an instance that implements the
     *                          // Console_CommandLine_Renderer interface
     *     'outputter' => $out, // an instance that implements the
     *                          // Console_CommandLine_Outputter interface
     *     'message_provider' => $mp, // an instance that implements the
     *                                // Console_CommandLine_MessageProvider
     *                                // interface
     *     'force_posix' => false // or true to force posix compliance
     * ));
     * </code>
     *
     * @param array $params an optional array of parameters
     *
     * @access public
     */
    public function __construct(array $params=array()) 
    {
        if (isset($params['name'])) {
            $this->name = $params['name'];
        } else {
            $this->name = $_SERVER['argv'][0];
        }
        if (isset($params['description'])) {
            $this->description = $params['description'];
        }
        if (isset($params['version'])) {
            $this->version = $params['version'];
        }
        if (isset($params['add_version_option'])) {
            $this->add_version_option = $params['add_version_option'];
        }
        if (isset($params['add_help_option'])) {
            $this->add_help_option = $params['add_help_option'];
        }
        if (isset($params['force_posix'])) {
            $this->force_posix = $params['force_posix'];
        } else if (getenv('POSIXLY_CORRECT')) {
            $this->force_posix = true;
        }
        if (isset($params['renderer'])) {
            $this->renderer = $params['renderer'];
        } else {
            // set the default renderer if not provided
            include_once 'Console/CommandLine/Renderer/Default.php';
            $this->renderer = new Console_CommandLine_Renderer_Default($this);
        }
        if (isset($params['outputter'])) {
            $this->outputter = $params['outputter'];
        } else {
            // set the default outputter if not provided
            include_once 'Console/CommandLine/Outputter/Default.php';
            $this->outputter = new Console_CommandLine_Outputter_Default();
        }
        if (isset($params['message_provider'])) {
            $this->message_provider = $params['message_provider'];
        } else {
            // set the default message provider if not provided
            include_once 'Console/CommandLine/MessageProvider/Default.php';
            $this->message_provider = 
                new Console_CommandLine_MessageProvider_Default();
        }
    }

    // }}}
    // Console_CommandLine::fromXmlFile() {{{

    /**
     * Return a command line parser instance built from an xml file.
     *
     * Example:
     * <code>
     * require_once 'Console/CommandLine.php';
     * $parser = Console_CommandLine::fromXmlFile('path/to/file.xml');
     * $result = $parser->parse();
     * </code>
     *
     * @param string $file path to the xml file
     *
     * @return object a Console_CommandLine instance
     * @access public
     * @static
     */
    public static function fromXmlFile($file) 
    {
        include_once 'Console/CommandLine/XmlParser.php';
        return Console_CommandLine_XmlParser::parse($file);
    }

    // }}}
    // Console_CommandLine::fromXmlString() {{{

    /**
     * Return a command line parser instance built from an xml string.
     *
     * Example:
     * <code>
     * require_once 'Console/CommandLine.php';
     * $xmldata = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
     * <command>
     *   <description>Compress files</description>
     *   <option name="quiet">
     *     <short_name>-q</short_name>
     *     <long_name>--quiet</long_name>
     *     <description>be quiet when run</description>
     *     <action>StoreTrue/action>
     *   </option>
     *   <argument name="files">
     *     <description>a list of files</description>
     *     <multiple>true</multiple>
     *   </argument>
     * </command>';
     * $parser = Console_CommandLine::fromXmlString($xmldata);
     * $result = $parser->parse();
     * </code>
     *
     * @param string $string the xml data
     *
     * @return object a Console_CommandLine instance
     * @access public
     * @static
     */
    public static function fromXmlString($string) 
    {
        include_once 'Console/CommandLine/XmlParser.php';
        return Console_CommandLine_XmlParser::parseString($string);
    }

    // }}}
    // Console_CommandLine::addArgument() {{{

    /**
     * Add an argument with the given $name to the command line parser.
     *
     * Example:
     * <code>
     * $parser = new Console_CommandLine();
     * // add an array argument
     * $parser->addArgument('input_files', array('multiple'=>true));
     * // add a simple argument
     * $parser->addArgument('output_file');
     * $result = $parser->parse();
     * print_r($result->args['input_files']);
     * print_r($result->args['output_file']);
     * // will print:
     * // array('file1', 'file2')
     * // 'file3'
     * // if the command line was:
     * // myscript.php file1 file2 file3
     * </code>
     *
     * In a terminal, the help will be displayed like this:
     * <code>
     * $ myscript.php install -h
     * Usage: myscript.php <input_files...> <output_file>
     * </code>
     *
     * @param mixed $name   a string containing the argument name or an
     *                      instance of Console_CommandLine_Argument
     * @param array $params an array containing the argument attributes
     *
     * @return object Console_CommandLine_Argument
     * @access public
     * @see    Console_CommandLine_Command
     */
    public function addArgument($name, $params=array())
    {
        if ($name instanceof Console_CommandLine_Argument) {
            $argument = $name;
        } else {
            include_once 'Console/CommandLine/Argument.php';
            $argument = new Console_CommandLine_Argument($name, $params);
        }
        $argument->validate();
        $this->args[$argument->name] = $argument;
        return $argument;
    }

    // }}}
    // Console_CommandLine::addCommand() {{{

    /**
     * Add a sub-command to the command line parser.
     *
     * Add a command with the given $name to the parser and return the 
     * Console_CommandLine_Command instance, you can then populate the command
     * with options, configure it, etc... like you would do for the main parser
     * because the class Console_CommandLine_Command inherits from
     * Console_CommandLine.
     *
     * An example:
     * <code>
     * $parser = new Console_CommandLine();
     * $install_cmd = $parser->addCommand('install');
     * $install_cmd->addOption(
     *     'verbose',
     *     array(
     *         'short_name'  => '-v',
     *         'long_name'   => '--verbose',
     *         'description' => 'be noisy when installing stuff',
     *         'action'      => 'StoreTrue'
     *      )
     * );
     * $parser->parse();
     * </code>
     * Then in a terminal:
     * <code>
     * $ myscript.php install -h
     * Usage: myscript.php install [options]
     *
     * Options:
     *   -h, --help     display this help message and exit
     *   -v, --verbose  be noisy when installing stuff
     *
     * $ myscript.php install --verbose
     * Installing whatever...
     * $
     * </code>
     *
     * @param mixed $name   a string containing the command name or an
     *                      instance of Console_CommandLine_Command
     * @param array $params an array containing the command attributes
     *
     * @return object Console_CommandLine_Command
     * @access public
     * @see    Console_CommandLine_Command
     */
    public function addCommand($name, $params=array())
    {
        if ($name instanceof Console_CommandLine_Command) {
            $command = $name;
        } else {
            include_once 'Console/CommandLine/Command.php';
            $params['name'] = $name;
            $command = new Console_CommandLine_Command($params);
        }
        $command->parent                = $this;
        $this->commands[$command->name] = $command;
        return $command;
    }

    // }}}
    // Console_CommandLine::addOption() {{{

    /**
     * Add an option to the command line parser.
     *
     * Add an option with the name (variable name) $optname and set its 
     * attributes with the array $params, then return the
     * Console_CommandLine_Option instance created.
     * The method accepts another form: you can directly pass a 
     * Console_CommandLine_Option object as the sole argument, this allows to
     * contruct  the option separately, in order to reuse an option in
     * different command line parsers or commands for example.
     *
     * Example:
     * <code>
     * $parser = new Console_CommandLine();
     * $parser->addOption('path', array(
     *     'short_name'  => '-p',  // a short name
     *     'long_name'   => '--path', // a long name
     *     'description' => 'path to the dir', // a description msg
     *     'action'      => 'StoreString',
     *     'default'     => '/tmp' // a default value
     * ));
     * $parser->parse();
     * </code>
     *
     * In a terminal, the help will be displayed like this:
     * <code>
     * $ myscript.php --help
     * Usage: myscript.php [options]
     *
     * Options:
     *   -h, --help  display this help message and exit
     *   -p, --path  path to the dir
     *
     * </code>
     *
     * Various methods to specify an option, these 3 commands are equivalent:
     * <code>
     * $ myscript.php --path=some/path
     * $ myscript.php -p some/path
     * $ myscript.php -psome/path
     * </code>
     *
     * @param mixed $name   a string containing the option name or an
     *                      instance of Console_CommandLine_Option
     * @param array $params an array containing the option attributes
     *
     * @return object Console_CommandLine_Option
     * @access public
     * @see    Console_CommandLine_Option
     */
    public function addOption($name, $params=array())
    {
        include_once 'Console/CommandLine/Option.php';
        if ($name instanceof Console_CommandLine_Option) {
            $opt = $name;
        } else {
            $opt = new Console_CommandLine_Option($name, $params);
        }
        $opt->validate();
        $this->options[$opt->name] = $opt;
        return $opt;
    }

    // }}}
    // Console_CommandLine::displayError() {{{

    /**
     * Display an error to the user and exit with $exitCode.
     *
     * @param string $error    the error message
     * @param int    $exitCode the exit code number
     *
     * @return void
     * @access public
     */
    public function displayError($error, $exitCode = 1)
    {
        $this->outputter->stderr($this->renderer->error($error));
        exit($exitCode);
    }

    // }}}
    // Console_CommandLine::displayUsage() {{{

    /**
     * Display the usage help message to the user and exit with $exitCode
     *
     * @param int $exitCode the exit code number
     *
     * @return void
     * @access public
     */
    public function displayUsage($exitCode = 1)
    {
        $this->outputter->stderr($this->renderer->usage());
        exit($exitCode);
    }

    // }}}
    // Console_CommandLine::displayVersion() {{{

    /**
     * Display the program version to the user
     *
     * @return void
     * @access public
     */
    public function displayVersion()
    {
        $this->outputter->stdout($this->renderer->version());
        exit(0);
    }

    // }}}
    // Console_CommandLine::findOption() {{{

    /**
     * Find the option that matches the given short_name (ex: -v), long_name
     * (ex: --verbose) or name (ex: verbose).
     *
     * @param string $str the option identifier
     *
     * @return mixed a Console_CommandLine_Option instance or false
     * @access public
     */
    public function findOption($str)
    {
        $str = trim($str);
        if ($str === '') {
            return false;
        }
        $matches = array();
        foreach ($this->options as $opt) {
            if ($opt->short_name == $str || $opt->long_name == $str ||
                $opt->name == $str) {
                // exact match
                return $opt;
            }
            if (substr($opt->long_name, 0, strlen($str)) === $str) {
                // abbreviated long option
                $matches[] = $opt;
            }
        }
        if ($count = count($matches)) {
            if ($count > 1) {
                $matches_str = '';
                $padding = '';
                foreach ($matches as $opt) {
                    $matches_str .= $padding . $opt->long_name;
                    $padding = ', ';
                }
                throw Console_CommandLine_Exception::build('OPTION_AMBIGUOUS',
                    array('name' => $str, 'matches' => $matches_str),
                    $this);
            }
            return $matches[0];
        }
        return false;
    }
    // }}}
    // Console_CommandLine::registerAction() {{{

    /**
     * Register a custom action for the parser, an example:
     *
     * <code>
     * <?php
     *
     * // in this example we create a "range" action:
     * // the user will be able to enter something like:
     * // $ <program> -r 1,5
     * // and in the result we will have:
     * // $result->options['range']: array(1, 5)
     *
     * require_once 'Console/CommandLine.php';
     * require_once 'Console/CommandLine/Action.php';
     *
     * class ActionRange extends Console_CommandLine_Action
     * {
     *     public function execute($value=false, $params=array())
     *     {
     *         $range = explode(',', str_replace(' ', '', $value));
     *         if (count($range) != 2) {
     *             throw new Exception(sprintf(
     *                 'Option "%s" must be 2 integers separated by a comma',
     *                 $this->option->name
     *             ));
     *         }
     *         $this->setResult($range);
     *     }
     * }
     * // then we can register our action
     * Console_CommandLine::registerAction('Range', 'ActionRange');
     * // and now our action is available !
     * $parser = new Console_CommandLine();
     * $parser->addOption('range', array(
     *     'short_name'  => '-r',
     *     'long_name'   => '--range',
     *     'action'      => 'Range', // note our custom action
     *     'description' => 'A range of two integers separated by a comma'
     * ));
     * // etc...
     *
     * ?>
     * </code>
     *
     * @param string $name  the name of the custom action
     * @param string $class the class name of the custom action
     *
     * @return void
     * @access public
     * @static
     */
    public static function registerAction($name, $class) 
    {
        if (!isset(self::$actions[$name])) {
            if (!class_exists($class)) {
                self::triggerError('action_class_does_not_exists',
                    E_USER_ERROR,
                    array('{$name}' => $name, '{$class}' => $class));
            }
            self::$actions[$name] = array($class, false);
        }
    }

    // }}}
    // Console_CommandLine::triggerError() {{{

    /**
     * A wrapper for programming errors triggering.
     *
     * @param string $msgId  identifier of the message
     * @param int    $level  the php error level
     * @param array  $params an array of search=>replaces entries
     *
     * @return void
     * @access public
     * @static
     */
    public static function triggerError($msgId, $level, $params=array()) 
    {
        if (isset(self::$errors[$msgId])) {
            $msg = str_replace(array_keys($params),
                array_values($params), self::$errors[$msgId]); 
            trigger_error($msg, $level);
        } else {
            trigger_error('unknown error', $level);
        }
    }

    // }}}
    // Console_CommandLine::parse() {{{

    /**
     * Parse the command line arguments and return a Console_CommandLine_Result 
     * object.
     *
     * @param integer $userArgc number of arguments (optional)
     * @param array   $userArgv array containing arguments (optional)
     * @param integer $beginAt  beginning index of the argv array (optional)
     *
     * @return object Console_CommandLine_Result
     * @access public
     * @throws Exception on user errors
     */
    public function parse($userArgc=null, $userArgv=null, $beginAt=0)
    {
        // add "auto" options help and version if needed
        if ($this->add_help_option) {
            $this->addOption('help', array(
                'short_name'  => '-h',    
                'long_name'   => '--help',
                'description' => 'show this help message and exit',
                'action'      => 'Help'   
            ));
        }
        if ($this->add_version_option && !empty($this->version)) {
            $this->addOption('version', array(
                'long_name'   => '--version',
                'description' => 'show the program version and exit',
                'action'      => 'Version'   
            ));
        }
        $argc = ($userArgc === null) ?
            (isset($argc) ? $argc : $_SERVER['argc']) : $userArgc;
        $argv = ($userArgv === null) ?
            (isset($argv) ? $argv : $_SERVER['argv']) : $userArgv;
        // case of a subcommand, skip main program args
        for ($i=0; $i<$beginAt; $i++) {
            $argc--;
            array_shift($argv);
        }
        // remove script name
        array_shift($argv);
        $argc--;
        // will contain aruments
        $args = array();
        // build an empty result
        include_once 'Console/CommandLine/Result.php';
        $result = new Console_CommandLine_Result();
        foreach ($this->options as $name=>$option) {
            $result->options[$name] = $option->default;
        }
        // parse command line tokens
        $i = 0;
        while (++$i && $argc--) {
            $token = array_shift($argv);
            try {
                if (isset($this->commands[$token])) {
                    $res = $this->commands[$token]->parse(null, null, $i);
                    $result->command_name = $token;
                    $result->command = $res;
                    break;
                } else {
                    $this->parseToken($token, $result, $args, $argc===0);
                }
            } catch (Exception $exc) {
                throw $exc;
            }
        }
        // minimum argument number check
        $argnum = count($this->args);
        if (count($args) < $argnum) {
            throw Console_CommandLine_Exception::build('ARGUMENT_REQUIRED',
                array('argnum' => $argnum, 'plural' => $argnum>1 ? 's': ''),
                $this);
        }
        // handle arguments
        $c = count($this->args);
        foreach ($this->args as $name=>$arg) {
            $c--;
            if ($arg->multiple) {
                $result->args[$name] = $c ? array_splice($args, 0, -$c) : $args;
            } else {
                $result->args[$name] = array_shift($args);
            }
        }
        // dispatch deferred options
        foreach ($this->_dispatchLater as $optArray) {
            $optArray[0]->dispatchAction($optArray[1], $optArray[2], $this);
        }
        return $result;
    }

    // }}}
    // Console_CommandLine::parseToken() {{{

    /**
     * Parse the command line token and modify *by reference* the $options and 
     * $args arrays.
     *
     * @param string $token  the command line token to parse
     * @param object $result the Console_CommandLine_Result instance
     * @param array  &$args  the argv array
     *
     * @return void
     * @access protected
     * @throws Exception on user errors
     */
    protected function parseToken($token, $result, &$args, $last)
    {
        static $lastopt  = false;
        static $stopflag = false;
        $token = trim($token);
        if (!$stopflag && $lastopt) {
            if (substr($token, 0, 1) == '-') {
                if ($lastopt->argument_optional) {
                    $this->_dispatchAction($lastopt, '', $result);
                    if ($lastopt->action != 'StoreArray') {
                        $lastopt = false;
                    }
                } else if (isset($result->options[$lastopt->name])) {
                    // case of an option that expect a list of args
                    $lastopt = false;
                } else {
                    throw Console_CommandLine_Exception::build('OPTION_VALUE_REQUIRED',
                        array('name' => $lastopt->name), $this);
                }
            } else {
                $this->_dispatchAction($lastopt, $token, $result);
                if ($lastopt->action != 'StoreArray') {
                    $lastopt = false;
                }
                return;
            }
        }
        if (!$stopflag && substr($token, 0, 2) == '--') {
            // a long option
            $optkv = explode('=', $token, 2);
            if (trim($optkv[0]) == '--') {
                // the special argument "--" forces in all cases the end of 
                // option scanning.
                $stopflag = true;
                return;
            }
            $opt = $this->findOption($optkv[0]);
            if (!$opt) {
                throw Console_CommandLine_Exception::build('OPTION_UNKNOWN',
                    array('name' => $optkv[0]), $this);
            }
            $value = isset($optkv[1]) ? $optkv[1] : false;
            if (!$opt->expectsArgument() && $value !== false) {
                throw Console_CommandLine_Exception::build('OPTION_VALUE_UNEXPECTED',
                    array('name' => $opt->name, 'value' => $value), $this);
            }
            if ($opt->expectsArgument() && $value === false) {
                // maybe the long option argument is separated by a space, if 
                // this is the case it will be the next arg
                if ($last && !$opt->argument_optional) {
                    throw Console_CommandLine_Exception::build('OPTION_VALUE_REQUIRED',
                        array('name' => $opt->name), $this);
                }
                // we will have a value next time
                $lastopt = $opt;
                return;
            }
            if ($opt->action == 'StoreArray') {
                $lastopt = $opt;
            }
            $this->_dispatchAction($opt, $value, $result);
        } else if (!$stopflag && substr($token, 0, 1) == '-') {
            // a short option
            $optname = substr($token, 0, 2);
            if ($optname == '-') {
                // special case of "-" passed on the command line, it should be 
                // treated as an argument
                $args[] = $optname;
                return;
            }
            $opt     = $this->findOption($optname);
            if (!$opt) {
                throw Console_CommandLine_Exception::build('OPTION_UNKNOWN',
                    array('name' => $optname), $this);
            }
            // parse other options or set the value
            // in short: handle -f<value> and -f <value>
            $next = substr($token, 2, 1);
            // check if we must wait for a value
            if ($next === false) {
                if ($opt->expectsArgument()) {
                    if ($last && !$opt->argument_optional) {
                        throw Console_CommandLine_Exception::build('OPTION_VALUE_REQUIRED',
                            array('name' => $opt->name), $this);
                    }
                    // we will have a value next time
                    $lastopt = $opt;
                    return;
                }
                $value = false;
            } else {
                if (!$opt->expectsArgument()) { 
                    if ($nextopt = $this->findOption('-' . $next)) {
                        $this->_dispatchAction($opt, false, $result);
                        $this->parseToken('-' . substr($token, 2), $result,
                            $args, $last);
                        return;
                    } else {
                        throw Console_CommandLine_Exception::build('OPTION_UNKNOWN',
                            array('name' => $next), $this);
                    }
                }
                if ($opt->action == 'StoreArray') {
                    $lastopt = $opt;
                }
                $value = substr($token, 2);
            }
            $this->_dispatchAction($opt, $value, $result);
        } else {
            // We have an argument.
            // if we are in POSIX compliant mode, we must set the stop flag to 
            // true in order to stop option parsing.
            if (!$stopflag && $this->force_posix) {
                $stopflag = true;
            }
            $args[] = $token;
        }
    }

    // }}}
    // Console_CommandLine::_dispatchAction() {{{

    /**
     * Dispatch the given option or store the option to dispatch it later.
     *
     * @param string $token  the command line token to parse
     * @param object $result the Console_CommandLine_Result instance
     * @param array  &$args  the argv array
     *
     * @return void
     * @access protected
     * @throws Exception on user errors
     */
    private function _dispatchAction($option, $token, $result)
    {
        if ($option->action == 'Password') {
            $this->_dispatchLater[] = array($option, $token, $result);
        } else {
            $option->dispatchAction($token, $result, $this);
        }
    }
    // }}}
}

?>
