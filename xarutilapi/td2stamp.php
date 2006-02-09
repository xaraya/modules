<?php
/**
 * AddressBook utilapi td2stamp
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * converts a date string into a AddressBook formatted date
 *
 * @param string $idate - the date to format
 * @return string formated date
 */
function addressbook_utilapi_td2stamp($args)
{
    extract($args);
    if( (!isset($idate)) || (empty($idate)) || ($idate=='')) {
        return 'NULL';
    }
    $dateformat = xarModGetVar('addressbook','dateformat');
    $token = "-./ ";
    $p1 = strtok($idate,$token);
    $p2 = strtok($token);
    $p3 = strtok($token);
    $p4 = strtok($token);
    $y = ""; $m = ""; $d = "";
    if ($dateformat == 1) {
        $y = $p3;
        $m = $p2;
        $d = $p1;
    }
    else {
        $y = $p3;
        $m = $p1;
        $d = $p2;
    }
    if (($y != "") && ($y <= 99)) {
        if ($y >= 70) $y = $y + 1900;
        if ($y < 70) $y = $y + 2000;
    }
    if (checkdate($m,$d,$y)) {
        $returnValue = mktime(0,0,0,$m,$d,$y);
    } else {
        $returnValue = 0;
    }

    return $returnValue;

} // END td2stamp

?>