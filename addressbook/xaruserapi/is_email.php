<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
 *
 * AddressBook user is_email
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
 * is_email
 */
function AddressBook_userapi_is_email ($args) {
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