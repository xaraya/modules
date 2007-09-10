<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * generate the common admin menu configuration
 */
function logconfig_adminapi_menu()
{
    if (!xarVarFetch('func','str',$activelink, 'main', XARVAR_NOT_REQUIRED)) return;

    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Logging System Administration');

    $menu['menulinks'] = xarModAPIFunc('logconfig','admin','getmenulinks');

    $menu['activelink'] = $activelink;

    // Return the array containing the menu configuration
    return $menu;
}

?>