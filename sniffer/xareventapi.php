<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Sniffer System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage aniffer module
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
    include_once 'modules/sniffer/xaruserapi.php';
    return sniffer_userapi_sniff($arg);
}

?>