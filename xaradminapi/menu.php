<?php
/**
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * generate the common admin menu configuration
 */
function sitetools_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('SiteTools Administration');
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = '';
    // Return the array containing the menu configuration
    return $menu;
}
?>