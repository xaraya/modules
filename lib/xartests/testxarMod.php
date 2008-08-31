<?php

sys::import('modules.xarayatesting.class.xarUnitTest');

class testxarMod extends xarTestCase {
       
    function setup() {
        include_once 'xarCore.php';
        include_once 'xarLog.php';
        include_once 'xarDB.php';
        include_once 'xarServer.php';
        include_once 'xarMod.php';
        include_once 'xarEvt.php';
        include_once 'xarMLS.php';
    }
    
    function testInit() {
        return $this->assertTrue(xarMod_init('',''),"Module System Initialisation");
    }
    
    function testGetFileInfo() {
        $savedir=getcwd();
        // We must be in the root of the webserver directory
        $dir=exec('bk root')."/html";
        chdir($dir);
        $info = xarMod_getFileInfo('base');
        // The returned array should contain 15 entries
        $res = $this->assertEquals(count($info),15,0,"GetFileInfo should return 15 entries");
        chdir($savedir);
        return $res;
    }

}
$tmp = new xarTestSuite('Module system tests');
$tmp->AddTestCase('testxarMod','Testing xarMod.php');
$suites[] = $tmp;

?>