<?php
/**
 * Utility function to pass menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author MichelV
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function maxercalls_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AddMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'user',
                'new'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Adds a new Call to the system.'),
            'label' => xarML('Add call'));
    }
    // Security Check
    if (xarSecurityCheck('EditMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'viewcalls'),
            'title' => xarML('View all maxercalls items that have been added.'),
            'label' => xarML('View calls'));
    }
    // Security Check
    if (xarSecurityCheck('AdminMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'view'),
            'title' => xarML('View the dynamic objects'),
            'label' => xarML('View DD objects'));
    }

    // Security Check
    if (xarSecurityCheck('AdminMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
