<?php
/**
 * test case
 */

require_once dirname(__FILE__).'/../../classautoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * WURFL_Handlers_Matcher_RISMatcher test case.
 */
class WURFL_Handlers_Matcher_RISMatcherTest extends PHPUnit_Framework_TestCase
{
    private $risMatcher;

    protected function setUp()
    {
        $this->risMatcher = WURFL_Handlers_Matcher_RISMatcher::INSTANCE();
    }

    /**
     * @dataProvider risData
     */
    public function testMatch($candidates, $needle, $tolerance, $expected)
    {
        $result = $this->risMatcher->match($candidates, $needle, $tolerance);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider distanceData
     */
    public function testDistance($t1, $t2, $expected)
    {
    }

    public function testMatchMustReturnFirstMatch()
    {
        $expected = "aaa bbb 1";
        $needle = "aaa bbb 4";

        $candidates = ["aaa bbb 1", "aaa bbb 2", "aaa bbb 3", "aaa bbb 5", "aaa bbb 6" ];

        $match = $this->risMatcher->match($candidates, $needle, 1);

        $this->assertEquals($expected, $match);
    }

    public function risData()
    {
        $candidates = ["aaa bbb ccc ddd", "aaa bbb ccc", "aaa bbb", "aaa", "aaa xxx" ];
        sort($candidates);
        return [
            [$candidates, "aaa bbb ccc ddd", 15, "aaa bbb ccc ddd" ],
            [$candidates, "aaa bbb ccc xxx", 15, null ], //
            [$candidates, "aaa bbb ccc", 11, "aaa bbb ccc" ],
            [$candidates, "aaa bbb ccc ddd", 3, "aaa bbb ccc ddd" ],
        ];
    }

    public function distanceData()
    {
        return [["pippo", "pippotopo", 5 ], ["pippo", "pippo", 5 ], ["pippo", "pixxxxx", 2 ] ];
    }
}
