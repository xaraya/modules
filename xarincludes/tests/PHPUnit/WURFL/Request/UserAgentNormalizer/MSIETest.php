<?php
/**
 * test case
 */

require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_MSIETest extends WURFL_Request_UserAgentNormalizer_BaseTest
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Specific_MSIE();
    }


    /**
     * @test
     * @dataProvider msieUserAgentsDataProvider
     *
     */
    public function shoudRemoveAllTheCharactersAfterTheMinorVersion($userAgent, $expected)
    {
        $this->assertNormalizeEqualsExpected($userAgent, $expected);
    }



    public function msieUserAgentsDataProvider()
    {
        return [
                ["Mozilla/2.0 (compatible; MSIE 3.02; Windows CE; Smartphone; 176x220)", "MSIE 3.0"],
                ["Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; Smartphone; 176x220)", "MSIE 4.0"],
                ["MSIE 3.x", "MSIE 3.x"],
                ["Mozilla", "Mozilla"],
                ["Firefox", "Firefox"],

        ];
    }
}
