<?php
/**
 * File: $Id: checksubmitvalues.php,v 1.5 2003/12/22 07:12:50 garrett Exp $
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
function addressbook_userapi_checksubmitvalues($args) 
{

    $checkResult = TRUE;

    extract($args);
    // check for empty fields
    if ((empty($lname)) && (empty($fname)) && (empty($title)) && (empty($company))) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(xarML(_AB_ERRMSG_MISFIELDS_NAME_TAB)));
        $checkResult = FALSE;
    } else {
        // check for type of custom fields
        if (isset($custUserData) && is_array($custUserData)) {
            foreach($custUserData as $cus) {
                switch ($cus['type']) {
                        case 'decimal(10,2) default NULL':
                            if ((!empty($cus['userData'])) && (!ereg("^[+|-]{0,1}[0-9.,]{0,8}[.|,]{0,1}[0-9]{0,2}$",$cus['userData'],$regs))) {
                                xarErrorSet(XAR_USER_EXCEPTION,
                                            _AB_ERR_WARN,
                                            new abUserException(xarML(_AB_ERRMSG_FALSENUM_CUST_TAB)));
                                $checkResult = FALSE;
                            }
                            break;
                        case 'int default NULL':
                            if ((!empty($cus['userData'])) && (!ereg("^[0-9]{1,9}$",$cus['userData'],$regs))) {
                                xarErrorSet(XAR_USER_EXCEPTION,
                                            _AB_ERR_WARN,
                                            new abUserException(xarML(_AB_ERRMSG_INVALNUM_CUST_TAB)));
                                $checkResult = FALSE;
                            }
                            break;
                        case 'date default NULL':
                            if (!empty($cus['userData'])) {
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
                                    xarErrorSet(XAR_USER_EXCEPTION,
                                                _AB_ERR_WARN,
                                                new abUserException(xarML(_AB_ERRMSG_INVALDATE_CUST_TAB)));
                                    $checkResult = FALSE;
                                }
                            }
                            break;
                    }
            } // END foreach
        } // END if
    } // END if

    return $checkResult;

} // END checksubmitvalues

?>