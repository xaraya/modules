<?php
/**
 * Generate the common user menu configuration
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * generate the common menu configuration
 */
function sigmapersonnel_userapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('SIGMAPersonnel');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('View sigmapersonnel items');
    $menu['menulink_view'] = xarModURL('sigmapersonnel', 'user', 'view');

    return $menu;
}

?>
