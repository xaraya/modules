<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
 *
 * AddressBook user is_url
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
 * is_url
 */
function AddressBook_userapi_is_url ($args) {
    extract($args);
    $UrlElements = parse_url($url);
    if( (empty($UrlElements)) or (!$UrlElements) ) {
        return false;
    }

    if ((!isset($UrlElements['host'])) || (!empty($UrlElements['host']))) {
        return false;
    }
    return true;
} // END is_url

?>