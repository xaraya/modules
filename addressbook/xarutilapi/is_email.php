<?php
/**
 * File: $Id: is_email.php,v 1.2 2003/12/22 07:12:50 garrett Exp $
 *
 * AddressBook utilapi is_email
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
 * @param string $email
 * @return bool true / false
 */
function addressbook_utilapi_is_email ($args) {
    extract($args);

    if (empty($email)) {
        return false;
    }
    if (!ereg("@",$email)) {
        return false;
    }
    list($User,$Host) = split("@",$email);
    if(!ereg("\.",$Host)) {
        return false;
    }
    list($dom,$count) = split("\.",$Host);
    if ( (empty($User)) or (empty($dom)) or (empty($count)) ) {
        return false;
    }
    if ((ereg("[    ]",$User)) || (ereg("[  ]",$Host))) {
        return false;
    }
    $EmailRegExp="^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$";
    if (!eregi($EmailRegExp,$email)) {
        return false;
    }
    return true;
} // END is_email

?>