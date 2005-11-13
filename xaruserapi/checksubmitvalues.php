<?php
/**
 * File: $Id: checksubmitvalues.php,v 1.4 2004/11/16 05:40:47 garrett Exp $
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
                    _AB_ERR_INFO,
                    new abUserException(xarML('An Address Book Entry must contain data in at least one field of the Name tab!')));
        $checkResult = FALSE;
    } else {
        // check for type of custom fields
        if (isset($custUserData) && is_array($custUserData)) {
            foreach($custUserData as $cus) {
                switch ($cus['custType']) {
                        case _AB_CUSTOM_DECIMAL:
                            if ((!empty($cus['userData'])) && (!ereg("^[+|-]{0,1}[0-9.,]{0,8}[.|,]{0,1}[0-9]{0,2}$",$cus['userData'],$regs))) {
                                xarErrorSet(XAR_USER_EXCEPTION,
                                            _AB_ERR_INFO,
                                            new abUserException(xarML('There is a false numeric value in the #(1) tab.', xarModGetVar(__ADDRESSBOOK__,'custom_tab'))));
                                $checkResult = FALSE;
                            }
                            break;
                        case _AB_CUSTOM_INTEGER:
                            if ((!empty($cus['userData'])) && (!ereg("^[0-9]{1,9}$",$cus['userData'],$regs))) {
                                xarErrorSet(XAR_USER_EXCEPTION,
                                            _AB_ERR_INFO,
                                            new abUserException(xarML('In the #(1) tab there are characters in a digit-only field.',xarModGetVar(__ADDRESSBOOK__,'custom_tab'))));
                                $checkResult = FALSE;
                            }
                            break;
                        case _AB_CUSTOM_DATE:
                            if (!xarModAPIFunc(__ADDRESSBOOK__,'util','td2stamp',array('idate'=>$cus['userData']))) {
                                xarErrorSet(XAR_USER_EXCEPTION,
                                            _AB_ERR_INFO,
                                            new abUserException(xarML('In the #(1) tab there is a false date format.', xarModGetVar(__ADDRESSBOOK__,'custom_tab'))));
                                $checkResult = FALSE;
                            }
                            break;
                    }
            } // END foreach
        } // END if
    } // END if

    return $checkResult;

} // END checksubmitvalues

?>