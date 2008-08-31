<?php
/**
 * File: $Id$
 *
 * Unit testing framework 
 *
 * @package quality
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage testing
 * @author Jan Schrage <jan@xaraya.com>
 * @author Frank Besler <besfred@xaraya.com>
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Defines which offer some rudimentory support for
 * customizing the test framework.
 *
 */
define('UT_PREFIXTESTMETHOD','test'); // functions starting with this prefix are considered tests
define('UT_OUTLENGTH',80);            // width of the text output in text reporting mode


/**
  * class xarTestSuite
  * 
  */
class xarTestSuite 
{
    public $name;                // Name of this testsuite
    public $testcases = array(); // array which holds all testcases
    
    /**
     * Constructor just sets the name attribute
     */
    function __construct($name='Default') 
    {
        $this->name=$name;
    }
    
    /**
     * Add a testcase object to the testsuite
     */
    function AddTestCase($testClass,$name='') 
    {
        // Make sure the class exist
        if (class_exists($testClass) && (get_parent_class($testClass) == 'xarTestCase')) {
            if ($name=='') { $name=$testClass; }
            // Base dir is one dir up from the testsdir 
            $basedir = $this->_parentdir(getcwd());
            // Add a new testCase object into the array.
            $this->testcases[$name]=new xarTestCase($testClass,$name,true,$basedir);
        }
    }

    /**
     * Count the number of testcases in this suite
     */
    function CountTestCases() { return count($this->testcases); }

    /**
     * Run the testcases
     */
    function run() 
    {
        foreach($this->testcases as $testcase) {
            $testcase->runTests();
        }
    }

    function _parentdir($dir) {
        // FIXME :Get the parent dir of the dir inserted, dirty hack
        chdir('..');
        $toreturn=getcwd();
        chdir($dir);
        return $toreturn;
    }
    
    function report($type,$show_results=true) 
    {
        $report = new xarTestReport($type);
        $report->instance->present(array($this),$show_results);
    }

}

/** 
 * Base class for reporters
 *
 */
class xarTestReport 
{
    public $instance;
    
    /**
     * Abstract presentation function, this should be implemented in 
     * the subclasses
     *
     */
     function present(array $testsuites=array()) {}

    /**
     * Constructor instantiates the right type of object
     * make it a singleton, so the constructor is actually called only once
     * during a test run. 
     */
    function __construct($type='text') 
    {
        static $instance=NULL;

        // what type to instantiate
        if(!isset($instance)) {
            switch($type) {
            case 'html':
                $instance = new xarHTMLTestReport();
                break;
            default:
                $instance = new xarTextTestReport();
                break;
            }
        }
        $this->instance = $instance;
    }

    /**
     * For which revision marker are we running the testreport
     */
    function getTopOfTrunk() 
    {
        $tot = exec('bk changes -r+ -d:REV:');
        return $tot;
    }

}

class xarTextTestReport extends xarTestReport 
{
    
    // Constructor must be here, otherwise we get into a loop
    function __construct() 
    {
        // Because the constructor is only called once (singleton) during a test
        // run, the per testrun output should go in here. In this case, a simple
        // header which tells us at which point in the repository we're running the tests
        echo "Running tests for top of tree revision: ".$this->getTopOfTrunk()."\n";
        
    }

    // Presentation function
    function present($testsuites,$show_results=true) 
    {
        foreach($testsuites as $testsuite) {
            // Only include suites with testcases
            if($testsuite->countTestCases() > 0) {
                echo "TestSuite: ".$testsuite->name."\n";
                $nroftestcases = $testsuite->CountTestCases();
                foreach (array_keys($testsuite->testcases) as $casekey) {
                    echo "|- TestCase: ".$testsuite->testcases[$casekey]->name."\n";
                    if($show_results) {
                        $tests =& $testsuite->testcases[$casekey]->_tests;
                        foreach (array_keys($tests) as $key ) {
                            $result =& $tests[$key]->result;
                            if ($nroftestcases != 1) {
                                echo "|";
                            } else {
                                echo " ";
                            }
                            if (!empty($result->message)) {
                                echo " |- ". str_pad($result->message,UT_OUTLENGTH,".",STR_PAD_RIGHT) . 
                                    (get_class($result)=="xarTestSuccess"?"Passed":"FAILED") . "\n";
                            } else {
                                echo " |- ". str_pad("WARNING: invalid result in $key()",UT_OUTLENGTH,".",STR_PAD_RIGHT) .
                                    (get_class($result)=="xarTestSuccess"?"Passed":"FAILED") . "\n"; 
                            }
                        }
                    }
                    $nroftestcases--;
                }
            }
        }
    }
}

class xarHTMLTestReport extends xarTestReport 
{
    function __construct() 
    {
        // Because the constructor is only called once (singleton) during a test
        // run, the per testrun output should go in here. In this case, a simple
        // header which tells us at which point in the repository we're running the tests
        echo "Running tests for top of tree revision: ".$this->getTopOfTrunk()."\n";
        
    }

    // Presentation function
    function present($testsuites,$show_results=true) 
    {
        foreach($testsuites as $testsuite) {
            // Only include suites with testcases
            if($testsuite->countTestCases() > 0) {
                echo "<br />";
                echo "TestSuite: ".$testsuite->name;
                $nroftestcases = $testsuite->CountTestCases();
                foreach (array_keys($testsuite->testcases) as $casekey) {
                    echo "<br />&#160;&#160;";
                    echo "|- TestCase: ".$testsuite->testcases[$casekey]->name;
                    if($show_results) {
                        $tests =& $testsuite->testcases[$casekey]->tests;
                        foreach (array_keys($tests) as $key ) {
                            $result =& $tests[$key]->result;
                            if ($nroftestcases != 1) {
                                echo "  |";
                            } else {
                                echo " ";
                            }
                            if (!empty($result->message)) {
                                echo "&#160;&#160;|- ". str_pad($result->message,UT_OUTLENGTH,".",STR_PAD_RIGHT) . 
                                    (get_class($result)=="xarTestSuccess" ? "Passed" : "FAILED");
                            } else {
                                echo "&#160;&#160;|- ". str_pad("WARNING: invalid result in $key()",UT_OUTLENGTH,".",STR_PAD_RIGHT) .
                                    (get_class($result)=="xarTestSuccess" ? "Passed" : "FAILED"); 
                            }
                            echo get_class($result);
                            echo "<br />";
                        }
                    }
                    $nroftestcases--;
                }
            }
        }
    }
}

/**
 * class xarTestCase gathers info for the tests for a certain class
 *
 *
 */
class xarTestCase extends xarTestAssert 
{
    public $name;             // Name of this testcase
    public $tests = array();  // xarTest objects
    public $_basedir;         // from which directory should tests be running
    public $expected;         // the expected output from the test
    public $actual;           // the actual output of the test

    /**
     * Construct the testCase, make sure we only construct the 
     * array of test objects once 
     */
    function __construct($testClass='',$name='',$init=false, $basedir='') 
    {
//        if (get_parent_class($testClass) == 'xarTestCase') {
            if ($init) {
                $clazz = new $testClass();
                $this->name=$name;
                $this->_basedir=$basedir;
                $clazz->_collecttests();
                $this->tests = $clazz->tests;
            }
//        }
    }

    // Abstract functions, these should be implemented in the actual test class
    function setup() {} 
    // Precondition for a testcase default to true when not defined 
    function precondition() { return true; }
    function teardown() {}

    function runTests() 
    {
        $savedir=getcwd();
//        chdir($this->_basedir);
        foreach(array_keys($this->tests) as $key) {
            $this->tests[$key]->run();
        }
        chdir($savedir);
    }

    function pass($msg='Passed') 
    {
        $res = array('value' => true, 'msg' => $msg);
        return $res;
    }

    function fail($msg='Failed') 
    {
        $res = array('value' => false, 'msg' => $msg);
        return $res;
    }

    // private functions
    function _collecttests() 
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (substr($method, 0, strlen(UT_PREFIXTESTMETHOD)) == UT_PREFIXTESTMETHOD && 
                strtolower($method) != strtolower(get_class($this))) {
                $this->tests[$method] = new xarTest($this, $method);
            }
        }
    }
    
}

/**
 * Class to hold the actual test
 */
class xarTest 
{
    public $_parentobject;
    public $_testmethod;
    public $result;
    public $expected;         // the expected output from the test
    public $actual;           // the actual output of the test

    function __construct(&$container, $method) 
    {
        $this->_parentobject=& $container;
        $this->_testmethod=$method;
    }

    function run() 
    {
        $testcase= $this->_parentobject;
        $testmethod=$this->_testmethod;
        $testcase->setup();
        
        // Run the actual testmethod
        $result=$testcase->$testmethod();
        if($testcase->precondition()) {
            $this->result = new xarTestResult($result);
            if ($result['value'] === true) {
                $this->result  = new xarTestSuccess($result['msg']);
            } else {
                $this->result  = new xarTestFailure($result['msg']);
            }
        } else {
            $this->result = new xarTestException($result);
        }        
        $testcase->teardown();
        $this->expected = $testcase->expected;
        $this->actual = $testcase->actual;
    }
}

/**
 * Testresults
 *
 * This class constructs the xarTestResult object in the xarTest object
 * depending on the outcome of the called testmethod a different object
 * is created
 *
 */
class xarTestResult 
{
    public $message;

    function __construct($result) 
    {
    }
}

class xarTestSuccess extends xarTestResult 
{
    function __construct($msg) { 
        $this->message=$msg;
    }
}

class xarTestFailure extends xarTestResult 
{
    function __construct($msg) {
        $this->message=$msg;
    }
}

class xarTestException extends xarTestResult 
{
    function __construct($result) { 
        $this->message=$result['msg'];
    }
}
    
class xarTestAssert 
{
    
    // Abstract functions which should be implemented in subclasses
    // function fail($msg='no message') {}
    // function pass($msg='no message') {}

    function assertEquals($expected, $actual, $delta = 0,$msg='Test for Equals') 
    {
        if ((is_array($actual)  && is_array($expected)) ||
            (is_object($actual) && is_object($expected))) {
            if (is_array($actual) && is_array($expected)) {
                ksort($actual);
                ksort($expected);
            }
            
            $actual   = serialize($actual);
            $expected = serialize($expected);
            
            if (serialize($actual) == serialize($expected)) {
                return $this->pass($msg);
            }
        } 

        // Compare delta values
        if (is_numeric($actual) && is_numeric($expected)) {
            if (($actual >= ($expected - $delta) && $actual <= ($expected + $delta))) {
                return $this->pass($msg);
            }
        } 

        // Compare the direct values
        if ($actual == $expected) {
            return $this->pass($msg);
        } 

        // Couldn't find a combination which works
        return $this->fail($msg);
    }

    
    function assertNotNull($object,$msg='Test for Not Null') 
    {
        if ($object !== null) { 
            return $this->pass($msg); 
        }
        return $this->fail($msg);
    }


    function assertNull($object,$msg='Test for Null') 
    {
        if ($object === null) {
            return $this->pass($msg);
        } 
        return $this->fail($msg);
    }


    function assertSame($expected, $actual,$msg='Test for Same') 
    {
        if ($actual === $expected) {
            return $this->pass($msg);
        }
        return $this->fail($msg);
    }


    function assertNotSame($expected, $actual,$msg='Test for Not Same') 
    {
        if ($actual !== $expected) {
            return $this->pass($msg);
        } 
        return $this->fail($msg);
    }
    

    function assertTrue($condition,$msg='Test for True') 
    {
        if ($condition === true) {
            return $this->pass($msg);
        }
        return $this->fail($msg);
    }


    function assertFalse($condition,$msg='Test for False') 
    {
        if ($condition === false) {
            return $this->pass($msg);
        } 
        return $this->fail($msg);
    }


    function assertRegExp($expected, $actual,$msg='Test for Regular Expression') 
    {
        if (preg_match($expected, $actual)) {
            return $this->pass($msg);
        }
        return $this->fail($msg);
    }

    // TODO: This is a confusing assertion, if will fail for all other
    //       datatypes, which is good, but not very intuitive
    function assertEmpty($expected,$msg='Test for empty array') 
    {
        if (is_array($expected) && empty($expected)) {
            return $this->pass($msg);
        }
        return $this->fail($msg);
    }

}

?>