<?php
/**
 * File: $Id$
 *
 * An example test class.
 *
 * @package example
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Roger Keays <r.keays@ninthave.net>
 */


/* a suite to add the tests to */
$tmp = new xarTestSuite('Example Suite');


/**
 * Example test class.
 *
 * @package example
 * @author Roger Keays <r.keays@ninthave.net>
 */
class testExample extends xarTestCase 
{

    /**
     * Initialize the Xaraya core.
     */
    function setup() 
    {
		$GLOBALS['xarDebug'] = false;

        /* these must point to the correct location of the core */
        include_once 'includes/xarCore.php';
        include_once 'includes/xarLog.php';
        include_once 'includes/xarVar.php';
        include_once 'includes/xarException.php';
        xarErrorFree();
    }
    
    /**
     * Here is an example of a test which is expected to pass. As well as
     * assertSame, we also have: 
     *    assertEquals($actual, $expected, $delta, $msg)
     *    assertNonNull($object, $msg)
     *    assertNull($object, $msg)
     *    assertSame($actual, $expected, $msg)
     *    assertNotSame($actual, $expected, $msg)
     *    assertTrue($condition, $msg)
     *    assertFalse($condition, $msg)
     *    assertRegExp($actual, $expected, $msg)
     *    assertEmpty($actual, $msg)  // for arrays only
     * 
     * @see BitKeeper/custom/unittest/xarUnitTest.php
     */
    function testSame() 
    {
        $in = "EXAMPLE";
        $expected = "example";
        $out = strtolower($in);
        return $this->assertSame($out,$expected,"Testing using assertSame");
    }
}

/* add the tests to the suite */
$tmp->AddTestCase('testExample', 'Tests for the exmple module');

/* add this suite to the list */
$suites[] = $tmp;

?>
