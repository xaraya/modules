<?php
/**
 * File: $Id: is_url.php,v 1.3 2004/11/16 05:40:47 garrett Exp $
 *
 * AddressBook utilapi is_url
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
 * Validates the passed in string as url
 *
 * @param string $url
 * @return bool true / false
 */
function addressbook_utilapi_is_url ($args) 
{
    extract($args);
    $UrlElements = parse_url($url);
    if ((empty($UrlElements)) or (!$UrlElements)) {
        return false;
    }

    if ((!isset($UrlElements['host'])) || (empty($UrlElements['host']))) {
        return false;
    }
    return true;
} // END is_url

?>