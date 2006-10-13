<?php
/**
 * AddressBook util checkForIE
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Invoke the sniffer API and check if the request comes from an IE browser
 *
 * @param none
 * @return bool (true - client uses Internet Explorer / false - no IE
 */
function addressbook_utilapi_checkforie()
{
    if (xarModLoad('sniffer','user')) {
        xarModAPIFunc('sniffer','user','sniff');

        if (!stristr(xarSessionGetVar('browsername'),'internet explorer')) {
            return FALSE;
        }
        if (xarSessionGetVar('browserversion') < 5) {
            return FALSE;
        }
        return TRUE;
    } else {
        xarErrorFree();
        xarCoreExceptionFree();
    }
    return FALSE;

} //END checkForIE

?>
