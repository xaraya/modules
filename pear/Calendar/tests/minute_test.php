<?php

// $Id: minute_test.php 159563 2004-05-24 22:25:43Z quipo $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfMinute extends TestOfCalendar
{
    public function TestOfMinute()
    {
        $this->UnitTestCase('Test of Minute');
    }
    public function setUp()
    {
        $this->cal = new Calendar_Minute(2003, 10, 25, 13, 32);
    }
    public function testPrevDay_Array()
    {
        $this->assertEqual(
            [
                'year'   => 2003,
                'month'  => 10,
                'day'    => 24,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0, ],
            $this->cal->prevDay('array')
        );
    }
    public function testPrevSecond()
    {
        $this->assertEqual(59, $this->cal->prevSecond());
    }
    public function testThisSecond()
    {
        $this->assertEqual(0, $this->cal->thisSecond());
    }
    public function testThisSecond_Timestamp()
    {
        $this->assertEqual(
            $this->cal->cE->dateToStamp(
                2003,
                10,
                25,
                13,
                32,
                0
            ),
            $this->cal->thisSecond('timestamp')
        );
    }
    public function testNextSecond()
    {
        $this->assertEqual(1, $this->cal->nextSecond());
    }
    public function testNextSecond_Timestamp()
    {
        $this->assertEqual(
            $this->cal->cE->dateToStamp(
                2003,
                10,
                25,
                13,
                32,
                1
            ),
            $this->cal->nextSecond('timestamp')
        );
    }
    public function testGetTimeStamp()
    {
        $stamp = mktime(13, 32, 0, 10, 25, 2003);
        $this->assertEqual($stamp, $this->cal->getTimeStamp());
    }
}

class TestOfMinuteBuild extends TestOfMinute
{
    public function TestOfMinuteBuild()
    {
        $this->UnitTestCase('Test of Minute::build()');
    }
    public function testSize()
    {
        $this->cal->build();
        $this->assertEqual(60, $this->cal->size());
    }
    public function testFetch()
    {
        $this->cal->build();
        $i=0;
        while ($Child = $this->cal->fetch()) {
            $i++;
        }
        $this->assertEqual(60, $i);
    }
    public function testFetchAll()
    {
        $this->cal->build();
        $children = [];
        $i = 0;
        while ($Child = $this->cal->fetch()) {
            $children[$i]=$Child;
            $i++;
        }
        $this->assertEqual($children, $this->cal->fetchAll());
    }
    public function testSelection()
    {
        require_once(CALENDAR_ROOT . 'Second.php');
        $selection = [new Calendar_Second(2003, 10, 25, 13, 32, 43)];
        $this->cal->build($selection);
        $i = 0;
        while ($Child = $this->cal->fetch()) {
            if ($i == 43) {
                break;
            }
            $i++;
        }
        $this->assertTrue($Child->isSelected());
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new TestOfMinute();
    $test->run(new HtmlReporter());
    $test = new TestOfMinuteBuild();
    $test->run(new HtmlReporter());
}
