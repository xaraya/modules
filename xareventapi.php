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
 * Function to determine the client (browser, bot, ...)
 *
 * @return Boolean
 */
function sniffer_eventapi_OnSessionCreate($arg)
{
    // Note : we can't use xarModAPIFunc for this event !
    include_once 'modules/sniffer/xaruserapi/sniff.php';
    return sniffer_userapi_sniff($arg);
}

?>
