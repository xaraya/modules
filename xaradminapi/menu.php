<?php
/**
 * Standard function to generate the common admin menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * generate the common admin menu configuration
 */
function sigmapersonnel_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('SIGMApersonnel Administration');

    // Preset some status variable
    $menu['status'] = '';
    return $menu;
}

?>
