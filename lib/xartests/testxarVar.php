<?php

sys::import('modules.xarayatesting.class.xarUnitTest');

class testxarVar extends xarTestCase
{
    public $myBLC;
    
    public function setup()
    {
        include_once 'xarCore.php';
        include_once 'xarVar.php';
        include_once 'xarServer.php';
    }
    
    public function testCleanUntrusted()
    {
        // TODO: define more input and make them all pass
        $var = '<script>';
        $res =xarVarCleanUntrusted($var);
        return $this->assertEquals(strlen($res), 0, 0, "<script> is cleaned from untrusted");
    }

    public function testCleanFromInput()
    {
        // TODO: define more input and make them all pass
        $var = '<script>';
        $res=xarVarCleanFromInput($var);
        return $this->assertEquals(strlen($res), 0, 0, "<script> is cleaned from input");
    }
}
$tmp = new xarTestSuite('Variable system tests');
$tmp->AddTestCase('testxarVar', 'Testing xarVar.php');
$suites[] = $tmp;
