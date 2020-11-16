<?php

sys::import('modules.xarayatesting.class.xarUnitTest');

class testMLSSystem extends xarTestCase
{
    public function setup()
    {
        include_once  'xarMLS.php';
    }


    public function testEmptyMLString()
    {
        $out = xarML('');
        $expected='';
        return $this->AssertSame($out, $expected, 'Return empty string on empty input for xarML');
    }
}
$tmp = new xarTestSuite('MLS system tests');
$tmp->AddTestCase('testMLSSystem', 'MLS system tests');
$suites[] = $tmp;
