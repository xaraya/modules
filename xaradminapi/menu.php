<?php
/**
 * File: $Id:
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Generate the common admin menu configuration
 */
function sitecontact_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('SiteContact Administration');
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = '';

    return $menu;
} 

?>
