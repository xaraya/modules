<?php

/**
 * The unit testing framework warrants it own testsuite ;-)
 *
 */

sys::import('modules.xarayatesting.class.xarUnitTest');
$tmp=new xarTestSuite('Unit testing framework');

/**
 * Testcase for the assert functions
 **/
class testUnitTestAssert extends xarTestCase
{
    
    // Checking for the pass
    public function testTrue()
    {
        return $this->assertTrue(true, "assertTrue on true assertion");
    }
    public function testFalse()
    {
        return $this->assertFalse(false, "assertFalse on false assertion");
    }
    public function testNull()
    {
        return $this->assertNull(null, "assertNull on null assertion");
    }
    public function testNotNull()
    {
        return $this->assertNotNull("i'm not null", "assertNotNull on not null assertion");
    }
    public function testEquals()
    {
        return $this->assertEquals(1, 1, 0, "assertEquals on two equal values");
    }
    public function testSame()
    {
        return $this->assertSame(1, 1, "assertSame on two same values");
    }
    public function testNotSame()
    {
        return $this->assertNotSame(1, "1", "assertNotSame on two not the same values");
    }
    public function testEmpty()
    {
        return $this->assertEmpty(array(), "assertEmpty on empty array");
    }
    public function testRegExp()
    {
        return $this->assertRegExp('/tchi/i', 'matching', "assertRegExp on matching string");
    }

    // Checking for the fail
    // We can't use the assert functions itself here to return, as that is what we are testing
    // Make sure we only return for pass if it exactly matches the expected result
    public function testTrueFalse()
    {
        $res = $this->assertTrue(1==2, "assertTrue on false assertion");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testFalseTrue()
    {
        $res = $this->assertFalse(1==1, "asserFalse on true assertion");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testNullNotNull()
    {
        $res = $this->assertNull("i'm not null", "assertNull on not null assertion");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testNotNullNull()
    {
        $res = $this->assertNotNull(null, "assertNotNull on null assertion");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testEqualsNotEquals()
    {
        $res = $this->assertEquals(1, 2, 0, "assertEquals on two not equal values");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testSameNotSame()
    {
        $res = $this->assertSame(1, "1", "assertSame on two not the same values");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testNotSameSame()
    {
        $res = $this->assertNotSame(1, 1, "assertNotSame on two equal values");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
    public function testEmptyNotEmpty()
    {
        $res = $this->assertEmpty(array('empty'=> 'nope'), "assertEmpty on non empty array");
        if ($res["value"] === false) {
            $res["value"] = true;
        } else {
            $res["value"] = false;
        }
        return $res;
    }
}

$tmp->AddTestCase('testUnitTestAssert', 'Test the assertion functions for unit test framework');

/**
 * Testcase for the test methods in the testcase class
 *
 */
class testTestMethods extends xarTestCase
{
    public $mymethodlist=array();

    public function setup()
    {
        $this->mymethodlist[]="testmethodlist";
        $this->mymethodlist[]="testplaceholder";
    }
    
    public function teardown()
    {
        // As each test runs in it individual environment
        // we need to reset the array
        $this->mymethodlist=array();
    }

    public function testMethodList()
    {
        return $this->assertEquals($this->mymethodlist, array_keys($this->_tests), 0, "Test method retrieval");
    }

    public function testPlaceHolder()
    {
        return $this->assertTrue(true, "Placeholder test for test method retrieval");
    }
}
$tmp->AddTestCase('testTestMethods', 'Test methods in a testCase class');

/**
 * Bogus testcase, shouldn't interfere with anything
 *
 * Also used as testdata in testTestSuites testCase below
 */
class noTestCase
{
    public function testbogus()
    {
        return $this->assertTrue(true, "***ERROR***");
    }
}

/**
 * Testsuite construction
 *
 */
class testTestSuites extends xarTestCase
{
    public $mytestsuite;
    
    public $invalidsuite = 'thissuitedoesnotexist';
    public $empty = array();
    
    public function setup()
    {
        $this->mytestsuite = new xarTestSuite();
    }
    
    public function precondition()
    {
        // bogus class must not accidently exist
        if (class_exists($this->invalidsuite)) {
            return false;
        }
        // The noTestCase class must exist
        if (!class_exists('noTestCase')) {
            return false;
        }
        return true;
    }
    
    public function teardown()
    {
        $this->mytestsuite = '';
        $this->invalidsuite = '';
    }
    
    public function testinvalidsuite()
    {
        $this->mytestsuite->AddTestCase($this->invalidsuite, 'This is invalid');
        return $this->assertEquals($this->mytestsuite->_testcases, $this->empty, 0, 'Non-existing test suite');
    }
    public function testsubclassing()
    {
        $this->mytestsuite->AddTestCase('noTestCase', 'This is invalid');
        return $this->assertEquals($this->mytestsuite->_testcases, $this->empty, 0, 'No subclass of xarTestCase');
    }
}
$tmp->AddTestCase('testTestSuites', 'Testsuite and testcase construction');

// Add this suite to the list
$suites[]=$tmp;
