<?php
/**
 * test case
 */


require_once 'BaseTest.php';

/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_MaemoTest extends WURFL_Request_UserAgentNormalizer_BaseTest
{
    public function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Specific_Maemo();
    }


    /**
     * @test
     * @dataProvider maemoUserAgentsDataProvider
     *
     */
    public function shoudReturnTheStringAfterMaemo($userAgent, $expected)
    {
        $found = $this->normalizer->normalize($userAgent);
        $this->assertEquals($found, $expected);
    }


    public function maemoUserAgentsDataProvider()
    {
        return [
                ["Mozilla/5.0 (X11; U; Linux armv7l; en-GB; rv:1.9.2.3pre) Gecko/20100624 Firefox/3.5 Maemo Browser 1.7.4.8 RX-51 N900", "Maemo Browser 1.7.4.8 RX-51 N900"],
                ["Mozilla", "Mozilla"],
                ["Maemo Browser 1.7.4.8 RX-51 N900", "Maemo Browser 1.7.4.8 RX-51 N900"],

        ];
    }
}
