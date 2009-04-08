<?php

class testxarVar1 extends xarTestCase {
    
    function testCleanUntrusted() {
        // TODO: define more input and make them all pass
        $var = '<script>';
        $res =xarVarCleanUntrusted($var);
        return $this->assertEquals(strlen($res),0,0,"<script> is cleaned from untrusted");
    }

    function testCleanFromInput() {
        // TODO: define more input and make them all pass
        $var = '<script>';
        $res=xarVarCleanFromInput($var);
        return $this->assertEquals(strlen($res),0,0,"<script> is cleaned from input");
    }
}
$tmp = new xarTestSuite('Variable system tests');
$tmp->AddTestCase('testxarVar','Testing xarVar.php');
$suites[] = $tmp;
?>