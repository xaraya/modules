<?php
/**
 * File: $Id: modifyconfig.php,v 1.3 2003/07/02 02:15:15 garrett Exp $
 *
 * AddressBook utility functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

function AddressBook_userapi_checkForIE() {

    xarModAPIFunc('sniffer','user','sniff');

    if (!stristr(xarSessionGetVar('browsername'),'internet explorer')) {
        return FALSE;
    }
    if (xarSessionGetVar('browserversion') < 5) {
        return FALSE;
    }
    return TRUE;

} //END checkForIE

?>