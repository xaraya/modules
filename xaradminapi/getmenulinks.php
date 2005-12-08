<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @author the xarDPLink module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xardplink_adminapi_getmenulinks()
{


    /* Show an overview menu option here if you like */

    if (xarSecurityCheck('AddExample', 0)) {
    $menulinks[] = Array('url' => xarModURL('xardplink','admin','main'),

            'title' => xarML('xarDPLink Overview'),
            'label' => xarML('Overview'));
    }

    /* Security Check */
    if (xarSecurityCheck('AdminXardplink', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item. Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = Array('url' => xarModURL('xardplink','admin','modifyconfig'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}
?>