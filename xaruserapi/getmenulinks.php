<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author MichelV.
 * @return array containing the menulinks for the main menu items.
 */
function sigmapersonnel_userapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.
    if (xarSecurityCheck('ViewSIGMApersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'user',
                'view'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Display all sigmapersonnel items for view'),
            'label' => xarML('View'));
    }
    if (xarSecurityCheck('AddSIGMApresence', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'user',
                'new'),
            'title' => xarML('Add a new presence entry'),
            'label' => xarML('Add presence'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}

?>
