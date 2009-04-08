<?php

class testxarUser1 extends xarTestCase {
    var $myBLC;
    
    function testEmptyUserVar() {
        return $this->assertNull(xarUserGetVar(''),"Passing empty user var should return null");
    }

}

$tmp = new xarTestSuite('User system tests');
$tmp->AddTestCase('testxarUser','Testing xarUser.php');
$suites[] = $tmp;

?>