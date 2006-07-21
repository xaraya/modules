<?php
/**
 * Generate the common menu configuration
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module3
 * @link http://xaraya.com/index.php/release/247.html
 * @author MichelV
 */
/**
 * generate the common menu configuration
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array
 */
function maxercalls_userapi_menu()
{
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('View calls');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('View your Maxercalls');
    $menu['menulink_view'] = xarModURL('maxercalls', 'user', 'view');

    return $menu;
}

?>
