<?php
/**
 * AddressBook utilapi is_url
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
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
