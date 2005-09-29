<?php
/**
 * Menu items
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */

/**
 * Utility function pass individual menu items to the main menu
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sitecontact_adminapi_getmenulinks()
{
     /*Security Check */
    if (xarSecurityCheck('AdminSiteContact', 0)) {
        $menulinks[] = Array('url' => xarModURL('sitecontact',
                                                'admin',
                                                'overview'),
            'title' => xarML('SiteContact Overview'),
            'label' => xarML('Overview'));
        $menulinks[] = Array('url' => xarModURL('sitecontact',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
} 

?>