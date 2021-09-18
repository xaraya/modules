<?php
/**
 * test case
 */


require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_ChromeTest extends WURFL_Request_UserAgentNormalizer_BaseTest
{
    public const CHROME_USERAGENTS_FILE = "chrome.txt";

    public function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Specific_Chrome();
    }


    /**
     * @test
     * @dataProvider chromeUserAgentsDataProvider
     *
     */
    public function shoudReturnOnlyFirefoxStringWithTheMajorVersion($userAgent, $expected)
    {
        $this->assertNormalizeEqualsExpected($userAgent, $expected);
    }


    public function chromeUserAgentsDataProvider()
    {
        return [
                [
                        @"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13", "Chrome/0",
                    ],
                ["Chrome/9.x", "Chrome/9"],
                ["Mozilla", "Mozilla"],
                ["Chrome", "Chrome"],

        ];
    }
}
