<?php
/**
 * Utility function to pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author MichelV
 * @return array containing the menulinks for the main menu items.
 */
function maxercalls_userapi_getmenulinks()
{
    if (xarSecurityCheck('ViewMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'user',
                'view'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Displays all your maxercalls that are entered'),
            'label' => xarML('Your calls'));
    }
    if (xarSecurityCheck('AddMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'user',
                'new'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Enter new Maxercall'),
            'label' => xarML('New call'));
    }

    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = array();
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}

?>
