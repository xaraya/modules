<?php

sys::import('modules.xarayatesting.class.xarUnitTest');

class testEvt extends xarTestCase {
    
    function setup() {
        $GLOBALS['xarDebug'] = false;
        include_once 'xarCore.php';
        include_once 'xarException.php';
        include_once 'xarEvt.php';
        include_once 'xarMLS.php';
    }
    
    function precondition() {
        // Abort on bogus file: must not exist
//        if (file_exists('xartests/doesntexist')) return false;
        // Testdata for BL
//        if (!file_exists('xartests/test.xt')) return false;
        return true;
    }

    function teardown () {
        // not needed here
    }
/***   
 * Existing functions in xarEvt.php with their possible return values
 *
x* - function xarEvt_init($args, $whatElseIsGoingLoaded)
x*   - return true (doesnt do anything)
 * - function xarEvt_trigger($eventName, $value = NULL)
 *   - if (!xarEvt__checkEvent($eventName)) return
 *   - shall we catch xarLogMessage("Triggered event ($eventName)")  ???
 *   - no return if ok (?return true?)
 * - function xarEvt_notify($modName, $modType, $eventName, $value)
 *   - if (!xarEvt__checkEvent($eventName)) return;
 *   - if (empty($modName)) { 
 *       xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'EMPTY_PARAM', 'modName');
 *       return;
 *   - if (xarExceptionMajor() != XAR_NO_EXCEPTION) return;
 *   - no return if ok (?return true?)
x* - function xarEvt_registerEvent($eventName)
x*   - if (empty($eventName)) {
x*       xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'EMPTY_PARAM', 'eventName');
x*       return;
x*   - no return if ok (?return true?)
x* - function xarEvt__checkEvent($eventName)
x*   - if (!isset($GLOBALS['xarEvt_knownEvents'][$eventName])) {
x*       xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'EVENT_NOT_REGISTERED', $eventName);
X*       return;
x*   - return true; if ok
 * - function xarEvt__GetActiveModsList()
 *   - if (!$result) return;
 *   - return $modList; (array)
 */

    function testInit() {
        return $this->assertTrue(xarEvt_init('',''),"Event System Initialisation");
    }

    function testCheckEventFalse() {
        return $this->assertNull(xarEvt__checkEvent('unregEvt'),"Check unregistered event");
    }

    function testCheckEventTrue() {
        $GLOBALS['xarEvt_knownEvents']['regEvt'] = true;
        return $this->assertTrue(xarEvt__checkEvent('regEvt'),"Check registered event");
    }

// // function xarEvt_registerEvent
    function testRegisterEventFalse() {
        return $this->assertFalse(xarEvt_registerEvent(''),"Register Event without specifiying a name");
    }

    function testRegisterEvent() {
        return $this->assertTrue(xarEvt_registerEvent('regEvt'),"Register Event with name");
    }

}

$l_suite = new xarTestSuite('Events');
$l_suite->AddTestCase('testEvt','Testing Events System');
$suites[] = $l_suite;
?>
