<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
 *
 * AddressBook user checkSubmitValues
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
 * checksubmitvalues
 */
function AddressBook_userapi_checksubmitvalues($args) {

    extract($args);
    // check for empty fields
    if ((empty($lname)) && (empty($fname)) && (empty($title)) && (empty($company))) {
        return _AB_UPDATE_CHKMSG_1;
    }
    // check for type of custom fields
    $cus_fields = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo');

    foreach($cus_fields as $cus) {
        switch ($cus['type']) {
                case 'decimal(10,2) default NULL':
                    if ((empty($cus['userData']) != 1) && (!ereg("^[+|-]{0,1}[0-9.,]{0,8}[.|,]{0,1}[0-9]{0,2}$",$cus['userData'],$regs))) {
                        return _AB_CHKMSG_1;
                    }
                    break;
                case 'int default NULL':
                    if ((empty($cus['userData']) != 1) && (!ereg("^[0-9]{1,9}$",$cus['userData'],$regs))) {
                        return _AB_CHKMSG_2;
                    }
                    break;
                case 'date default NULL':
                    if (empty($cus['userData']) != 1) {
                        $dateformat = xarModGetVar(__ADDRESSBOOK__,'dateformat');
                        $token = "-./ ";
                        $p1 = strtok($cus['userData'],$token);
                        $p2 = strtok($token);
                        $p3 = strtok($token);
                        $p4 = strtok($token);
                        $date = ""; $y = ""; $m = ""; $d = "";
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
                        if ($y != "" && $y <= 99) {
                            if ($y >= 70) $y = $y + 1900;
                            if ($y < 70) $y = $y + 2000;
                        }
                        if (!checkdate($m, $d, $y)) {
                            return _AB_CHKMSG_3;
                        }
                    }
                    break;
            }
    } // END foreach

    return false;

} // END checksubmitvalues

?>