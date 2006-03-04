<?php
/**
 * Utility function to pass menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the MichelV
 * @return array containing the menulinks for the main menu items.
 */
function sigmapersonnel_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AddSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'newperson'),
            'title' => xarML('Adds a new person to the system.'),
            'label' => xarML('Add Person'));
    }
    // Security Check
    if (xarSecurityCheck('EditSIGMAPersonnel', 0)) {

        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'viewpersons'),
            'title' => xarML('View all sigmapersonnel items that have been added.'),
            'label' => xarML('View Persons'));
    }
    // Security Check
    if (xarSecurityCheck('AdminSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'view'),
            'title' => xarML('View this module Dyn Data configuration'),
            'label' => xarML('Module parameters'));
    }

    // Security Check
    if (xarSecurityCheck('AdminSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
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
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}

?>
