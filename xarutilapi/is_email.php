<?php
/**
 * AddressBook utilapi is_email
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 * @todo MichelV <1> Can this be replaced by a xar validation?
 */
function addressbook_utilapi_is_email ($args)
{
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