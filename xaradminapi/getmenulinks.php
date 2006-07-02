<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @author @author jojodee
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function legis_adminapi_getmenulinks()
{
    /* Show an overview menu option here if you like */

    if (xarSecurityCheck('AdminLegis', 0)) {
           $menulinks[] = Array('url' => xarModURL('legis','admin','masters'),
            'title' => xarML('Manage Master Documents.'),
            'label' => xarML('Manage Master Docs'));
       $menulinks[] = Array('url' => xarModURL('legis','admin','doclets'),
            'title' => xarML('Manage Doclets'),
            'label' => xarML('Manage Doclets'));

    }
    if (xarSecurityCheck('DeleteLegis', 0)) {
        //Set validity, mark status of passed-unpassed, set veto
        //add, edit, delete
        $menulinks[] = Array('url' => xarModURL('legis','admin','view'),
            'title' => xarML('Manage Documents'),
            'label' => xarML('Manage Documents'));
    }
    if (xarSecurityCheck('AdminLegis', 0)) {
        $menulinks[] = Array('url' => xarModURL('legis','admin','manageusers'),
             'title' => xarML('Manage the Legis Users'),
            'label' => xarML('Manage Users'));
        $menulinks[] = Array('url' => xarModURL('legis','admin','modifyconfig'),
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
