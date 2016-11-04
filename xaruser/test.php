<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * the main user function - only used for external triggers
 * @param  $args ['itemid'] job id (optional)
 */
function scheduler_user_test()
{
    //callscheduler();
    $date = date('d.m.Y h:i:s');
    xarLogMessage('Entered in scheduler_user_test');
    xarLogMessage('Current Date time');
    xarLogVariable('datetime', $date);

    writeInLog();
}
function callScheduler()
{
    $url = "http://eventhubsacramento.com/index.php?module=scheduler&type=user&func=main";
    $content = getUrlContent($url);
}
function writeInLog()
{
    $url = "http://eventhubsacramento.com/writeinlog.php";
    $content = getUrlContent($url);
}

function getUrlContent($url, $loop = 0, $delay = 0)
{
    $file_contents = "";
    for($loopCount = 0; $loopCount <= $loop; $loopCount++)
    {
        $ch = curl_init($url);
        $timeout = 10;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode['http_code'] < "300") {
            if ($file_contents != '' || $loop == 0) {
                break;
            }
        } else {
            $file_contents = "";
        }
        sleep($delay);
    }
    return $file_contents;
}
?>