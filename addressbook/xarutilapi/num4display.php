<?php
/**
 * File: $Id: num4display.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook utilapi num4display
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

/**
 * Validates the passed in string as an email address
 *
 * @param string $inum
 * @return string - number formatted for display
 */
function addressbook_userapi_num4display($args){
    extract($args);
    if( (!isset($inum)) || (empty($inum)) || ($inum=='')) {
        return '';
    }
    $returnValue = '';
    $dateformat = xarModGetVar(__ADDRESSBOOK__,'numformat');
    if ($dateformat == '9.999,99') {
        $returnValue = number_format($inum,2,',','.');
    }
    else {
        $returnValue = number_format($inum,2,'.',',');
    }
    return $returnValue;
}

?>