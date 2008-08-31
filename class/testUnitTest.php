<?php

/**
 * The unit testing framework warrants it own testsuite ;-)
 *
 */

$tmp=new xarTestSuite('Unit testing framework');

/**
 * Testcase for the assert functions 
 **/
class testUnitTestAssert extends xarTestCase {
    
    // Checking for the pass 
    function testTrue() { return $this->assertTrue(true,"assertTrue on true assertion"); }
    function testFalse() { return $this->assertFalse(false,"assertFalse on false assertion"); }
    function testNull() { return $this->assertNull(NULL,"assertNull on null assertion"); }
    function testNotNull() { return $this->assertNotNull("i'm not null","assertNotNull on not null assertion"); }
    function testEquals() { return $this->assertEquals(1,1,0,"assertEquals on two equal values"); }
    function testSame() { return $this->assertSame(1,1,"assertSame on two same values"); }
    function testNotSame() { return $this->assertNotSame(1,"1","assertNotSame on two not the same values"); }
    function testEmpty() { return $this->assertEmpty(array(),"assertEmpty on empty array"); }
    function testRegExp() { return $this->assertRegExp('/tchi/i','matching',"assertRegExp on matching string"); }

    // Checking for the fail
    // We can't use the assert functions itself here to return, as that is what we are testing
    // Make sure we only return for pass if it exactly matches the expected result
    function testTrueFalse() {
        $res = $this->assertTrue(1==2,"assertTrue on false assertion");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testFalseTrue() {
        $res = $this->assertFalse(1==1,"asserFalse on true assertion");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testNullNotNull() {
        $res = $this->assertNull("i'm not null","assertNull on not null assertion");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testNotNullNull() {
        $res = $this->assertNotNull(NULL,"assertNotNull on null assertion");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testEqualsNotEquals() {
        $res = $this->assertEquals(1,2,0,"assertEquals on two not equal values");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testSameNotSame() {
        $res = $this->assertSame(1,"1","assertSame on two not the same values");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testNotSameSame() {
        $res = $this->assertNotSame(1,1,"assertNotSame on two equal values");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }
    function testEmptyNotEmpty() {
        $res = $this->assertEmpty(array('empty'=> 'nope'),"assertEmpty on non empty array");
        if ($res["value"] === false) $res["value"] = true; else  $res["value"] = false;
        return $res;
    }

}

$tmp->AddTestCase('testUnitTestAssert','Test the assertion functions for unit test framework');

/**
 * Testcase for the test methods in the testcase class
 *
 */
class testTestMethods extends xarTestCase {   
    var $mymethodlist=array(); 

    function setup() {
        $this->mymethodlist[]="testmethodlist";
        $this->mymethodlist[]="testplaceholder";
    }
    
    function teardown () {
        // As each test runs in it individual environment
        // we need to reset the array
        $this->mymethodlist=array();
    }

    function testMethodList() {
        return $this->assertEquals($this->mymethodlist,array_keys($this->_tests),0,"Test method retrieval");
    }

    function testPlaceHolder() {
        return $this->assertTrue(true,"Placeholder test for test method retrieval");
    }
}
$tmp->AddTestCase('testTestMethods','Test methods in a testCase class');

/** 
 * Bogus testcase, shouldn't interfere with anything
 *
 * Also used as testdata in testTestSuites testCase below
 */
class noTestCase {
    function testbogus() { 
        return $this->assertTrue(true,"***ERROR***"); 
    }
}

/**
 * Testsuite construction
 *
 */
class testTestSuites extends xarTestCase {
    var $mytestsuite;
    
    var $invalidsuite = 'thissuitedoesnotexist';
    var $empty = array();
    
    function setup() {
        $this->mytestsuite = new xarTestSuite();
    }
    
    function precondition() {
        // bogus class must not accidently exist
        if (class_exists($this->invalidsuite)) { return false; }
        // The noTestCase class must exist
        if (!class_exists('noTestCase')) { return false; }
        return true;
    }
    
    function teardown () {
        $this->mytestsuite = '';
        $this->invalidsuite = '';
    }
    
    function testinvalidsuite() {
        $this->mytestsuite->AddTestCase($this->invalidsuite,'This is invalid');
        return $this->assertEquals($this->mytestsuite->_testcases,$this->empty,0,'Non-existing test suite');
    }
    function testsubclassing()
    {
        $this->mytestsuite->AddTestCase('noTestCase','This is invalid');   
        return $this->assertEquals($this->mytestsuite->_testcases,$this->empty,0,'No subclass of xarTestCase');
    }
    
}
$tmp->AddTestCase('testTestSuites','Testsuite and testcase construction');

// Add this suite to the list
$suites[]=$tmp;

?>
