<?php
/**
 * File: $Id: checkaccesslevel.php,v 1.4 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook user checkAccessLevel
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
 * checkAccessLevel - checks for AB specific access levels
 *
 * @param string $option - target access level
 * @return bool
 */
function addressbook_userapi_checkAccessLevel($args) {
    $access=false;
    $usermode = (xarModGetVar(__ADDRESSBOOK__, 'usermode'));
    $guestmode = (xarModGetVar(__ADDRESSBOOK__, 'guestmode'));
    extract($args);

    switch($option) {
        case 'view':
            if (xarUserIsLoggedIn()) {
                if ((xarSecurityCheck('EditAddressBook',0)) || (xarSecurityCheck('ModerateAddressBook',0))) {
                    $access = true;
                    break;
                }
                else {
                    if (($usermode == 1) || ($usermode == 3) || ($usermode == 5) || ($usermode == 7)) {
                        $access = true;
                        break;
                    }
                    else {
                        $access = false;
                        break;
                    }
                }
            }
            else {
                if (($guestmode == 1) || ($guestmode == 3) || ($guestmode == 5) || ($guestmode == 7)) {
                    $access = true;
                    break;
                }
                else {
                    $access = false;
                    break;
                }
            }
        case 'create':
            if (xarUserIsLoggedIn()) {
                if ((xarSecurityCheck('EditAddressBook',0)) || (xarSecurityCheck('ModerateAddressBook',0))) {
                    $access = true;
                    break;
                }
                else {
                    if (($usermode == 2) || ($usermode == 3) || ($usermode == 6) || ($usermode == 7)) {
                        $access = true;
                        break;
                    }
                    else {
                        $access = false;
                        break;
                    }
                }
            }
            else {
                if (($guestmode == 2) || ($guestmode == 3) || ($guestmode == 6) || ($guestmode == 7)) {
                    $access = true;
                    break;
                }
                else {
                    $access = false;
                    break;
                }
            }
        case 'edit':
            if (xarUserIsLoggedIn()) {
                if ((xarSecurityCheck('EditAddressBook',0)) || (xarSecurityCheck('ModerateAddressBook',0))) {
                    $access = true;
                    break;
                }
                else {
                    if ($usermode >= 4) {
                        $access = true;
                        break;
                    }
                    else {
                        $access = false;
                        break;
                    }
                }
            }
            else {
                if ($guestmode >= 4) {
                    $access = true;
                    break;
                }
                else {
                    $access = false;
                    break;
                }
            }
    } // END switch

    return $access;
} // END checkAccessLevel

?>