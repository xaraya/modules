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
define('UT_OUTLENGTH'       ,60);            // width of the text output in text reporting mode
define('UT_SPACETEXT'       ,'[sp]');
define('UT_CRTEXT'          ,'[cr]');
/**
  * class xarTestSuite
  * 
  */
class xarTestSuite {
    var $_name;                // Name of this testsuite
    var $_testcases = array(); // array which holds all testcases
    
    /**
     * Constructor just sets the name attribute
     */
    function xarTestSuite($name='default') {
        $this->_name=$name;
    }
    
    /**
     * Add a testcase object to the testsuite
     */
    function AddTestCase($testClass,$name='') {
        // Make sure the class exist
        if (class_exists($testClass) && (get_parent_class($testClass) == 'xartestcase')) {
            if ($name=='') { $name=$testClass; }
            // Base dir is one dir up from the testsdir 
            $basedir = $this->_parentdir(getcwd());
            // Add a new testCase object into the array.
            $this->_testcases[$name]=new xarTestCase($testClass,$name,true,$basedir);
        }
    }

    /**
     * Count the number of testcases in this suite
     */
    function CountTestCases() { return count($this->_testcases); }

    /**
     * Run the testcases
     */
    function run() {
        foreach($this->_testcases as $testcase) {
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
    
    function report($type,$show_results=true) {
        $report = new xarTestReport($type);
        $report->present(array($this),$show_results);
    }

}

/** 
 * Base class for reporters
 *
 */
class xarTestReport {
    /**
     * Abstract presentation function, this should be implemented in 
     * the subclasses
     *
     */
    // function present(array $testsuites=array()) {}

    /**
     * Constructor instantiates the right type of object
     * make it a singleton, so the constructor is actually called only once
     * during a test run. 
     */
    function xarTestReport($type='text') {
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
        $this = $instance;
    }

    /**
     * For which revision marker are we running the testreport
     */
    function getTopOfTrunk() {
        $tot = exec('bk changes -r+ -d:REV:');
        return $tot;
    }

}

class xarTextTestReport extends xarTestReport {
    
    // Constructor must be here, otherwise we get into a loop
    function xarTextTestReport() {
        // Because the constructor is only called once (singleton) during a test
        // run, the per testrun output should go in here. In this case, a simple
        // header which tells us at which point in the repository we're running the tests
        echo "Running tests for top of tree revision: ".$this->getTopOfTrunk()."\n";
        
    }

    // Presentation function
    function present($testsuites,$show_results=true) {
        foreach($testsuites as $testsuite) {
            // Only include suites with testcases
            if($testsuite->countTestCases() > 0) {
                echo "TestSuite: ".$testsuite->_name."\n";
                $nroftestcases = $testsuite->CountTestCases();
                foreach (array_keys($testsuite->_testcases) as $casekey) {
                    echo "|- TestCase: ".$testsuite->_testcases[$casekey]->_name."\n";
                    if($show_results) {
                        $tests =& $testsuite->_testcases[$casekey]->_tests;
                        foreach (array_keys($tests) as $key ) {
                            $result =& $tests[$key]->_result;
                            if ($nroftestcases != 1) {
                                echo "|";
                            } else {
                                echo " ";
                            }
                            if (!empty($result->_message)) {
                                echo " |- ". str_pad($result->_message,UT_OUTLENGTH,".",STR_PAD_RIGHT) . 
                                    (get_class($result)=="xartestsuccess"?"Passed":"FAILED") . "\n";
                                if(get_class($result)=="xartestfailure") {
                                    // Try to give some more info about what was expected
                                    $got = $result->got;
                                    $got = str_replace(array("\r\n","\r"),"\n",$got);
                                    $got = str_replace(' ',UT_SPACETEXT,$got);
                                    $got = str_replace("\n",UT_CRTEXT,$got);
                                    $got = print_r($got,true);

                                    $expected = $result->expected;
                                    $expected = str_replace(array("\r\n","\r"),"\n",$expected);
                                    $expected = str_replace(' ',UT_SPACETEXT,$expected);
                                    $expected = str_replace("\n",UT_CRTEXT,$expected);
                                    $expected = print_r($expected,true);
                                    echo "    |- I got     : $got\n";
                                    echo "    |- I expected: $expected\n";
                                }
                            } else {
                                echo " |- ". str_pad("WARNING: invalid result in $key()",UT_OUTLENGTH,".",STR_PAD_RIGHT) .
                                    (get_class($result)=="xartestsuccess"?"Passed":"FAILED") . "\n"; 
                            }
                        }
                    }
                    $nroftestcases--;
                }
            }
        }
    }
}

class xarHTMLTestReport extends xarTestReport {

    // Constructor must be here, otherwize we get into a loop
    function xarHTMLTestReport() { }
}

/**
 * class xarTestCase gathers info for the tests for a certain class
 *
 *
 */
class xarTestCase extends xarTestAssert {
    var $_name;           // Name of this testcase
    var $_tests=array();  // xarTest objects
    var $_basedir;        // from which directory should tests be running
  
    /**
     * Construct the testCase, make sure we only construct the 
     * array of test objects once 
     */
    function xarTestCase($testClass='',$name='',$init=false, $basedir='') {
        if (get_parent_class($testClass) == 'xartestcase') {
            if ($init) {
                $this = new $testClass();
                $this->_name=$name;
                $this->_basedir=$basedir;
                $this->_collecttests();
            }
        }
    }

    // Abstract functions, these should be implemented in the actual test class
    function setup() {} 
    // Precondition for a testcase default to true when not defined 
    function precondition() { return true; }
    function teardown() {}

    function runTests() {
        $savedir=getcwd();
        chdir($this->_basedir);
        foreach(array_keys($this->_tests) as $key) {
            $this->_tests[$key]->run();
        }
        chdir($savedir);
    }

    function pass($msg='Passed') {
        $res = array('value' => true, 'msg' => $msg);
        return $res;
    }

    function fail($msg='Failed',$got,$expected) {
        $res = array('value' => false, 'msg' => $msg, 'got' => $got, 'expected' => $expected);
        //var_dump($res);
        return $res;
    }

    // private functions
    function _collecttests() {
        $methods = get_class_methods($this);
            
        foreach ($methods as $method) {
            if (substr($method, 0, strlen(UT_PREFIXTESTMETHOD)) == UT_PREFIXTESTMETHOD && 
                strtolower($method) != strtolower(get_class($this))) {
                $this->_tests[$method] =& new xarTest($this, $method);
            }
        }
    }
    
}

/**
 * Class to hold the actual test
 */
class xarTest {
    var $_parentobject;
    var $_testmethod;
    var $_result;

    function xarTest(&$container, $method) {
        $this->_parentobject=& $container;
        $this->_testmethod=$method;
    }

    function run() {
        $testcase=& $this->_parentobject;
        $testmethod=$this->_testmethod;
        $testcase->setup();
        
        if($testcase->precondition()) {
            // Run the actual testmethod
            $result=$testcase->$testmethod();
            $this->_result = new xarTestResult($result);
        } else {
            $this->_result = new xarTestException($result);
        }
        
        $testcase->teardown();
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
class xarTestResult {
    var $_message;

    function xarTestResult($result) {
        if ($result['value'] === true) {
            $this = new xarTestSuccess($result);
        } else {
            $this = new xarTestFailure($result);
        }
    }
}

class xarTestSuccess extends xarTestResult {
    function xarTestSuccess($result) { 
        $this->_message=$result['msg'];
    }
}

class xarTestFailure extends xarTestResult {
    var $got;
    var $expected;

    function xarTestFailure($result) {
        $this->_message=$result['msg'];
        $this->got = $result['got'];
        $this->expected = $result['expected'];
    }
}

class xarTestException extends xarTestResult {
    function xarTestException($result) { 
        $this->_message=$result['msg'];
    }
}
    
class xarTestAssert {
    
    // Abstract functions which should be implemented in subclasses
    // function fail($msg='no message', $got, $expected) {}
    // function pass($msg='no message') {}

    function assertEquals($actual, $expected, $delta = 0,$msg='Test for Equals') {
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
        return $this->fail($msg,$actual,$expected);
    }

    
    function assertNotNull($object,$msg='Test for Not Null') {
        if ($object !== null) { 
            return $this->pass($msg); 
        }
        return $this->fail($msg,$object, 'Null value');
    }


    function assertNull($object,$msg='Test for Null') {
        if ($object === null) {
            return $this->pass($msg);
        } 
        return $this->fail($msg,'Non null value','Null');
    }


    function assertSame($actual, $expected,$msg='Test for Same') {
        if ($actual === $expected) {
            return $this->pass($msg);
        }
        return $this->fail($msg,$actual,$expected);
    }


    function assertNotSame($actual, $expected,$msg='Test for Not Same') {
        if ($actual !== $expected) {
            return $this->pass($msg);
        } 
        return $this->fail($msg,$actual,$expected);
    }
    

    function assertTrue($condition,$msg='Test for True') {
        if ($condition === true) {
            return $this->pass($msg);
        }
        return $this->fail($msg,$condition,'True condition');
    }


    function assertFalse($condition,$msg='Test for False') {
        if ($condition === false) {
            return $this->pass($msg);
        } 
        return $this->fail($msg,$condition,'False condition');
    }


    function assertRegExp($actual, $expected,$msg='Test for Regular Expression') {
        if (preg_match($actual, $expected)) {
            return $this->pass($msg,$actual,$expected);
        }
        return $this->fail($msg,$actual,$expected);
    }

    // TODO: This is a confusing assertion, if will fail for all other
    //       datatypes, which is good, but not very intuitive
    function assertEmpty($actual,$msg='Test for empty array') {
        if (is_array($actual) && empty($actual)) {
            return $this->pass($msg);
        }
        return $this->fail($msg,$actual,'Empty');
    }

}

?>