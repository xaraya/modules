<?php

class testxarUser1 extends xarTestCase
{
    public $myBLC;

    public function testEmptyUserVar()
    {
        return $this->assertNull(xarUser::getVar(''), "Passing empty user var should return null");
    }
}

$tmp = new xarTestSuite('User system tests');
$tmp->AddTestCase('testxarUser', 'Testing xarUser.php');
$suites[] = $tmp;
