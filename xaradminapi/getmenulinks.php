<?php
/**
 * Menu items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Utility function pass individual menu items to the main menu
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sitecontact_adminapi_getmenulinks()
{
    $menulinks = array();
     /*Security Check */
    if (xarSecurityCheck('AdminSiteContact', 0)) {
       $menulinks[] = Array('url' => xarModURL('sitecontact','admin','managesctypes'),
            'title' => xarML('Manage the contact forms'),
            'label' => xarML('Manage Contact Forms'));
        $menulinks[] = Array('url' => xarModURL('sitecontact','admin','view'),
            'title' => xarML('Manage contact form responses'),
            'label' => xarML('Review Responses'));
        $menulinks[] = Array('url' => xarModURL('sitecontact','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }

    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}

?>