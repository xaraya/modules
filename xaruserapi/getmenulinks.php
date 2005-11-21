<?php
/**
 * Decode the short URLs for Julian
 *
 * @package julian
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link  link to information for the subpackage
 * @author Julian development Team
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the mstDNS module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function julian_userapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.

    if (xarSecurityCheck('Viewjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'user',
                'day'),
            'title' => xarML('Day view'),
            'label' => xarML('Day'));
    }
    if (xarSecurityCheck('Viewjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'user',
                'week'),
            'title' => xarML('Week view'),
            'label' => xarML('Week'));
    }
    if (xarSecurityCheck('Viewjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'user',
                'month'),
            'title' => xarML('Month view'),
            'label' => xarML('Month'));
    }
    if (xarSecurityCheck('Viewjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'user',
                'year'),
            'title' => xarML('Year view'),
            'label' => xarML('Year'));
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
