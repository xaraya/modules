<?php
/**
 * File: $Id:
 * 
 * Utility function to pass individual menu items to the main menu
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarcpshop_userapi_getmenulinks()
{
    if (xarSecurityCheck('ViewxarCPShop', 0)) {
        $menulinks[] = Array('url' => xarModURL('xarcpshop','user','main'),
            'title' => xarML('Displays all CP Shop products for view'),
            'label' => xarML('Display'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
} 

?>
