<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * This function is an alias for the event handler
 * so that it can also be called via the Module API
 *
 * @return Boolean
 */
function sniffer_userapi_sniff($args)
{
/*
    // check whether proceed or quit
    $uas = xarSessionGetVar('uaid');
    if (!empty($uas)) {
        return true;
    }
*/
    // Note : we can't use xarModAPIFunc as this function
    // is not defined on start of Xaraya session
    include_once 'modules/sniffer/xaruserapi/sniffbasic.php';
    $sniff = sniffer_userapi_sniffbasic($args);

    if (!isset($sniff) || !isset($sniff['uaid']) || !isset($sniff['client'])) return;
    $uaid = $sniff['uaid'];
    $client = $sniff['client'];

    // provide user agent details as session variables
    xarSessionSetVar("uaid", $uaid);
    xarSessionSetVar('browsername', $client->getname('browser'));
    xarSessionSetVar('browserversion', $client->property('version'));
    xarSessionSetVar('osname', $client->property('platform'));
    xarSessionSetVar('osversion', $client->property('os'));
//  xarSessionSetVar('caps', $client->property('caps'));
//  xarSessionSetVar('quirks', $client->property('quirks'));
//  xarSessionSetVar('browserlang', $client->property('language'));

    // end of sniffin... bark
    return true;
}

?>
