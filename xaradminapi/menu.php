<?php
/**
 * File: $Id:
 *
 * Standard function to generate the common admin menu configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * generate the common admin menu configuration
 */
function bible_adminapi_menu()
{
    $menu = array();
    $menu['menutitle'] = xarML('Bible Administration');

    // get status message and reset it for next use
    $menu['statusmsg'] = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    return $menu;
}

?>
