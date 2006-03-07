<?php
/**
 * Utility function to pass menu items to the main menu
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
function xarcpshop_adminapi_getmenulinks()
{
     if (xarSecurityCheck('AddxarCPShop', 0)) {
        $menulinks[] = Array('url' => xarModURL('xarcpshop','admin','new'),
                             'title' => xarML('Add a new shop'),
                             'label' => xarML('Add shop'));
    }
    // Security Check
    if (xarSecurityCheck('EditxarCPShop', 0)) {
        $menulinks[] = Array('url' => xarModURL('xarcpshop','admin','view'),
                             'title' => xarML('View all shops'),
                             'label' => xarML('View Shops'));
    }
    // Security Check
    if (xarSecurityCheck('AdminxarCPShop', 0)) {
        $menulinks[] = Array('url' => xarModURL('xarcpshop','admin','modifyconfig'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}

?>
