<?php
/**
 * AddressBook utilapi stamp2date
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
 * Transforms a timestamp into a Address Book date format
 *
 * @param string $idate
 * @return string formated date
 */
function addressbook_utilapi_stamp2date($args)
{
    extract($args);
    if( (!isset($idate)) || (empty($idate)) || ($idate=='')) {
        return '';
    }
    $token = "-";
    $p1 = strtok($idate,$token);
    $p2 = strtok($token);
    $p3 = strtok($token);
    $p4 = strtok($token);
    $returnValue = '';
    $dateformat = xarModGetVar('addressbook','dateformat');
    if ($dateformat == 1) {
        //$returnValue = date("d.m.Y",$idate);
        $returnValue = $p3.'.'.$p2.'.'.$p1;
    }
    else {
        //$returnValue = date("m.d.Y",$idate);
        $returnValue = $p2.'.'.$p3.'.'.$p1;
    }
    return $returnValue;
}

?>
